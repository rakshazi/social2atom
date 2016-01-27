<?php
namespace App\Controller;

class Vk extends \Nius\Core\Controller
{
    public function index()
    {
        echo "<h2>Use it as " . $this->app->config('app.url') . '/vk/[groupname]</h2>';
    }

    public function get($domain)
    {
        $this->app->response->headers['Content-Type'] = 'application/atom+xml';
        $api = $this->app->load('vk\API');
        $post = $this->app->load('vk\Preprocessor\Post');
        $atom = $this->app->load('vk\Preprocessor\Atom');
        $data = array();

        $data['group'] = $api->groupsGetById($domain);
        $rawItems = $api->wallGet($data['group']->response[0]->gid);

        foreach ($rawItems->response as $item) {
            if (is_object($item)) {
                $data['items'][] = $post->setRaw($item)->get();
            }
        }

        usort($data['items'], function ($a, $b) {
            return ($a->date > $b->date) ? -1 : 1;
        });

        $atom->setRaw($data)->get();
    }
}
