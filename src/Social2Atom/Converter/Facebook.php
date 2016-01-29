<?php
namespace Rakshazi\Social2Atom\Converter;

class Facebook extends General
{
    public function process()
    {
        $api = $this->di->get('facebook\API');
        $feed = $this->di->get('facebook\Feed');
        $raw = $api->page($this->source);
        $data = $feed->setRaw($raw)->get();
        $this->ready = $this->getAtom($data);
    }
}
