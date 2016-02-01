<?php
namespace Rakshazi\Social2Atom\Converter\Vk;

class Audio extends \Rakshazi\Social2Atom\Converter\General\Preprocessor
{
    protected function process()
    {
        $this->ready = array();
        if ($this->di->config('vk.token')) {
            if($this->di->config('general.audio.enclosure')) {
                $this->ready = new \stdClass();
                $this->ready->url = $this->raw->audio->url;
                $this->ready->duration = $this->raw->audio->duration;

                return true;
            }

            $this->ready = "<audio controls><source ";
            $this->ready .= "src='".$this->raw->audio->url."' type='audio/mpeg'>";
            $this->ready .= "Your browser does not support the audio element.";
            $this->ready .= "</audio>";
            $this->ready .= $this->raw->audio->artist." - ".$this->raw->audio->title." ";
            $this->ready .= "<a href='".$this->raw->audio->url.'">Download</a>';
        }
    }
}
