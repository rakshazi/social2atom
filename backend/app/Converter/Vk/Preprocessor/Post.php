<?php
namespace App\Converter\Vk\Preprocessor;

class Post extends \App\Converter\General\Preprocessor
{
    protected function process()
    {
        $this->ready = $this->raw;
        $this->setLinks();
        $this->setAuthor();
        $this->setTitle();
        $this->setAttachments();
    }

    protected function setLinks()
    {
        $patterns = array(
            '/\[(club[0-9]+)\|(.+)\]/u',
            '/\[(id[0-9]+)\|(.+)\]/u',
            '/#([A-Za-zА-Яа-яЁ-ё0-9_]+)/u',
            '/((www|http:\/\/)[^ <">\)]+)/u',
            '/\[(id[0-9]+)\|(.+)[^,\.]/u',
        );
        $replacements = array(
            '<a href="https://vk.com/\1" target="_blank">\2</a>',
            '<a href="https://vk.com/\1" target="_blank">\2</a>',
            '<a href="https://vk.com/feed?q=#\1&section=search" target="_blank">#\1</a>',
            '<a href="\1" target="_blank">\1</a>',
            '<a href="https://vk.com/\1" target="_blank">\2</a>',
        );

        $this->ready->text = preg_replace($patterns, $replacements, $this->ready->text);
    }

    protected function setAuthor()
    {
        $this->ready->author = null;

        if ($this->ready->from_id > 0) {
            $api = new \App\Converter\Vk\API($this->app);
            $user = $api->usersGet($this->ready->from_id);
            $this->ready->author = $user->response[0]->first_name.' '.$user->response[0]->last_name;
        }
    }

    protected function setTitle()
    {
        $title = substr($this->ready->text, 0, strpos($this->ready->text, "<br>"));
        $title = strip_tags($title);
        if (strlen($title) == 0) {
            $title = strip_tags($this->ready->text);
            $title = substr($title, 0, strpos($title, "."));
        }

        $this->ready->title = $title;
    }

    protected function setAttachments()
    {
        if (property_exists($this->raw,'attachments')) {
            foreach($this->raw->attachments as $attachment)
            {
                $preprocessor = "\\App\\Converter\\Vk\\Preprocessor\\".ucfirst($attachment->type);

                if (class_exists($preprocessor)) {
                    $preprocessor = new $preprocessor($this->app);
                    $this->ready->text.= "<br><br>";
                    $this->ready->text.= $preprocessor->setRaw($attachment)->get();
                }
            }
        }
    }
}
