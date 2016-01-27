<?php
namespace Rakshazi\Social2Atom\Converter;

class Vk extends General
{
    public function process()
    {
        $api = $this->di->get('vk\API');
        $post = $this->di->get('vk\Post');
        $atom = $this->di->get('vk\Atom');
        $data = array();

        $data['group'] = $api->groupsGetById($this->source);
        $rawItems = $api->wallGet($data['group']->response[0]->gid);

        foreach ($rawItems->response as $item) {
            if (is_object($item)) {
                $data['items'][] = $post->setRaw($item)->get();
            }
        }

        usort($data['items'], function ($a, $b) {
            return ($a->date > $b->date) ? -1 : 1;
        });
        $this->ready = $atom->setRaw($data)->get();
    }
}
