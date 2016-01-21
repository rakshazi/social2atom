<?php
namespace App\Converter\Vk\Preprocessor;

class Video extends \App\Converter\General\Preprocessor
{
    protected function process()
    {
        $this->ready = '<i>Video available only on post source</i>';
    }
}
