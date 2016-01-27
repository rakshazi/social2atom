<?php
namespace Rakshazi\Social2Atom\Converter\Vk;

class Post extends \Rakshazi\Social2Atom\Converter\General\Preprocessor
{
    protected function process()
    {
        $this->ready = $this->raw;
        $this->convertEncoding();
        $this->setLinks();
        $this->setDate();
        $this->setAuthor();
        $this->setTitle();
        $this->setAttachments();
    }

    protected function convertEncoding()
    {
        $this->ready->text = mb_convert_encoding($this->ready->text, "UTF-8");
    }

    protected function setLinks()
    {
        $patterns = array(
            '/((www|http:\/\/|https:\/\/)[^ <">\)]+)/u',
            '/\[(club[0-9]+)\|(.+)\]/u',
            '/\[(id[0-9]+)\|(.+)\]/u',
            '/#([A-Za-zА-Яа-яЁ-ё0-9_]+)/u',
            '/\[(id[0-9]+)\|(.+)[^,\.]/u',
        );
        $replacements = array(
            '<a href="\1" target="_blank">\1</a>',
            '<a href="https://vk.com/\1" target="_blank">\2</a>',
            '<a href="https://vk.com/\1" target="_blank">\2</a>',
            '<a href="https://vk.com/feed?q=#\1&section=search" target="_blank">#\1</a>',
            '<a href="https://vk.com/\1" target="_blank">\2</a>',
        );

        $this->ready->text = preg_replace($patterns, $replacements, $this->ready->text);
    }

    protected function setDate()
    {
        $this->ready->date = date("Y-m-d\TH:i:sP", $this->raw->date);
    }

    protected function setAuthor()
    {
        $this->ready->author = null;

        if ($this->ready->from_id > 0) {
            $user = $this->di->get("vk\API")->usersGet($this->ready->from_id);
            $this->ready->author = $user->response[0]->first_name . ' ' . $user->response[0]->last_name;
        }
    }

    protected function setTitle()
    {
        $text = strip_tags($this->ready->text);
        $size = 200; //symbols for title
        $this->ready->title = mb_substr($text,0,mb_strrpos(mb_substr($text,0,$size,'utf-8'),' ','utf-8'),'utf-8');
    }

    protected function setAttachments()
    {
        if (property_exists($this->raw, 'attachments')) {
            foreach ($this->raw->attachments as $attachment) {
                $preprocessor = $this->di->get('vk\\' . ucfirst($attachment->type));

                if ($preprocessor) {
                    $this->ready->text .= "<br><br>";
                    $this->ready->text .= $preprocessor->setRaw($attachment)->get();
                }
            }
        }
    }
}
