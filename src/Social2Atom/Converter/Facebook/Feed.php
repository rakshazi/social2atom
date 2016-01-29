<?php
namespace Rakshazi\Social2Atom\Converter\Facebook;

class Feed extends \Rakshazi\Social2Atom\Converter\General\Feed
{

    protected function setInfo()
    {
        $info = parent::setInfo();
        $info->title = $this->raw->name;
        $info->description = $this->raw->about;
        $info->origin_url = $this->raw->link;

        return $info;
    }

    protected function setItems()
    {
        $items = array();
        foreach($this->raw->feed->data as $data) {
            $id = explode('_',$data->id);
            $url = $this->raw->link.'posts/'.$id[1];
            $item = new \stdClass();
            $item->id = $url;
            $item->url = $url;
            $item->date = $data->created_time;
            $item->author = null;
            $item->title = $this->getTitle($data->message);
            $item->text = $this->replaceLinks($data->message);

            $items[] = $item;
        }

        usort($items, function ($a, $b) {
            return ($a->date > $b->date) ? -1 : 1;
        });

        return $items;
    }

    protected function replaceLinks($text)
    {
        $patterns = array('/#([A-Za-zА-Яа-яЁ-ё0-9_]+)/u');
        $replacements = array('<a href="https://www.facebook.com/hashtag/\1" target="_blank">#\1</a>');

        return preg_replace($patterns, $replacements, $text);
    }
}
