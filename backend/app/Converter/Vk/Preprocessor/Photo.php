<?php
namespace App\Converter\Vk\Preprocessor;

class Photo extends \App\Converter\General\Preprocessor
{
    protected function process()
    {
        $this->ready = '<img src="' . $this->raw->photo->src_big . '" title="' . $this->raw->photo->text . '"/>';
    }
}
