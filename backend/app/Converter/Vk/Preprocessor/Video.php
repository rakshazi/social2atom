<?php
namespace App\Converter\Vk\Preprocessor;

class Video extends \App\Converter\General\Preprocessor
{
    protected function process()
    {
        $this->ready = "Video available only if you set vk.com access token";
        if ($this->app->config('vk.token')) {
            $raw = $this->app->load('vk\API')->videoGet($this->raw->video->owner_id, $this->raw->video->vid);
            if ($raw->response[0] > 0) {
                $this->ready = '<iframe allowfullscreen width="607" height="360" src="' . $raw->response[1]->player;
                $this->ready .= '" frameborder="0"></iframe>';
            }
        }
    }
}
