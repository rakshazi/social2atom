<?php
namespace Rakshazi\Social2Atom\Converter\Vk;

class Photo extends \Rakshazi\Social2Atom\Converter\General\Preprocessor
{
    protected function process()
    {
        $this->ready = '<img src="' . $this->raw->photo->src_big . '" title="' . mb_convert_encoding($this->raw->photo->text, "UTF-8") . '"/>';
    }
}
