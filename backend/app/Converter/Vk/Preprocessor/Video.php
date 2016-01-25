<?php
namespace App\Converter\Vk\Preprocessor;

class Video extends \App\Converter\General\Preprocessor
{
    protected function process()
    {
        if ($this->app->config('vk.token')) {
            $raw = $this->app->load('vk\API')->videoGet($this->raw->video->owner_id, $this->raw->video->vid);
            $this->ready = '<iframe type="text/html" width="607" height="360" src="' . $raw->response[1]->player;
            $this->ready .= '" frameborder="0"></iframe>';

            return $this;
        }

        $this->ready = "Video available only if you set vk.com access token";
    }
}
