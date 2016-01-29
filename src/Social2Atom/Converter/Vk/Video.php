<?php
namespace Rakshazi\Social2Atom\Converter\Vk;

class Video extends \Rakshazi\Social2Atom\Converter\General\Preprocessor
{
    protected function process()
    {
        $this->ready = "Video available only if you set vk.com access token";
        if ($this->di->config('vk.token')) {
            $raw = $this->di->get('vk\API')->videoGet($this->raw->video->owner_id, $this->raw->video->vid);
            if (isset($raw->response[1]) && $raw->response[1]) {
                $this->ready = '<iframe allowfullscreen width="607" height="360" src="' . $raw->response[1]->player;
                $this->ready .= '" frameborder="0"></iframe>';
            }
        }
    }
}
