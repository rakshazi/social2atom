<?php
namespace App\Converter\Vk\Preprocessor;

class Link extends \App\Converter\General\Preprocessor
{
    protected function process()
    {
        $this->ready = '';
        $this->ready .= '<a href="' . $this->raw->link->url . '">';
        $this->ready .= $this->raw->link->title;
        $this->ready .= '</a> ';
    }
}
