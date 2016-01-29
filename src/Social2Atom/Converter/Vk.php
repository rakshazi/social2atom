<?php
namespace Rakshazi\Social2Atom\Converter;

class Vk extends General
{
    public function process()
    {
        $api = $this->di->get('vk\API');
        $feed = $this->di->get('vk\Feed');
        $raw = array();
        $raw['info'] = $api->groupsGetById($this->source);
        $rawItems = $api->wallGet($raw['info']->response[0]->gid);
        foreach ($rawItems->response as $item) {
            if (is_object($item)) {
                $raw['items'][] = $item;
            }
        }
        $data = $feed->setRaw($raw)->get();
        $this->ready = $this->getAtom($data);
    }
}
