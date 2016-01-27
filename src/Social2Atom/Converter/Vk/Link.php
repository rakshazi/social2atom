<?php
namespace Rakshazi\Social2Atom\Converter\Vk;

class Link extends \Rakshazi\Social2Atom\Converter\General\Preprocessor
{
    protected function process()
    {
        $this->ready = '';
        $this->ready .= '<a href="' . $this->raw->link->url . '">';
        $this->ready .= mb_convert_encoding($this->raw->link->title, "UTF-8");
        $this->ready .= '</a> ';
    }
}
