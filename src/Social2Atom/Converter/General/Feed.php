<?php
namespace Rakshazi\Social2Atom\Converter\General;

class Feed extends Preprocessor
{
    protected function process()
    {
        $this->ready = array(
            'info' => $this->setInfo(),
            'items' => $this->setItems()
        );
    }

    protected function setInfo()
    {
        $info = new \stdClass();
        $info->self_url = $this->di->config('app.uri');
        $info->app_url = $this->di->config('app.url');

        return $info;
    }

    protected function getTitle($text)
    {
        $text = strip_tags($text, '<br>');
        $text = str_replace(
            array("\n", "<br>", "!", "?", "."),
            array("<divider>", "<divider>", "!<divider>", "?<divider>", "<divider>"),
            $text
        );
        $sentences = explode("<divider>", $text);
        $title = "";
        foreach ($sentences as $sentence) {
            $title = trim($sentence);
            if (!empty($title)) {
                break;
            }
        }

        return $title;
    }
}
