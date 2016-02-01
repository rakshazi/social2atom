<?php
namespace Rakshazi\Social2Atom\Converter\Vk;

class Audio extends \Rakshazi\Social2Atom\Converter\General\Preprocessor
{
    protected function process()
    {
        $this->ready = array();
        if ($this->di->config('vk.token')) {
            $this->ready = new \stdClass();
            $this->ready->url = $this->raw->audio->url;
            $this->ready->duration = $this->raw->audio->duration;
        }
    }
}
