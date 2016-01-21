<?php
namespace App\Controller;

Class Vk extends \Nius\Core\Controller
{
    public function index()
    {
        echo "<h2>Use it as ".$this->app->config('app.url').'/vk/[groupname]</h2>';
    }

    public function get($domain)
    {
        $api = new \App\Converter\Vk\API($this->app);
        $post = new \App\Converter\Vk\Preprocessor\Post($this->app);
        $atom = new \App\Converter\Vk\Preprocessor\Atom($this->app);
        $data = array();

        $rawItems = $api->wallGet($domain);
        $data['group'] = $api->groupsGetById($domain);

        foreach ($rawItems->response as $item) {
            if (is_object($item)) {
                $data['items'][] = $post->setRaw($item)->get();
            }
        }

        usort($data['items'], function($a, $b) {
          return ($a->date > $b->date) ? -1 : 1;
        });

        $atom->setRaw($data)->get();
    }
}
