<?php
namespace Rakshazi\Social2Atom\Converter\Vk;

class Feed extends \Rakshazi\Social2Atom\Converter\General\Feed
{
    protected function setInfo()
    {
        $info = parent::setInfo();
        $info->title = $this->raw['info']->response[0]->name;
        $info->description = $this->raw['info']->response[0]->description;
        $info->origin_url = 'https://vk.com/' . $this->raw['info']->response[0]->screen_name;

        return $info;
    }

    protected function replaceLinks($text)
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

        return preg_replace($patterns, $replacements, $text);
    }

    protected function setItems()
    {
        $items = array();
        foreach($this->raw['items'] as $data) {
            $url = $this->setInfo()->origin_url.'?w=wall'.$data->from_id.'_'.$data->id;
            $item = new \stdClass();
            $item->id = $url;
            $item->url = $url;
            $item->date = date("Y-m-d\TH:i:sP", $data->date);
            $item->author = $this->getAuthor($data);
            $item->title = $this->getTitle($data->text);
            $item->text = $this->replaceLinks($data->text);

            $items[] = $this->setAttachments($data, $item);
        }

        usort($items, function ($a, $b) {
            return ($a->date > $b->date) ? -1 : 1;
        });

        return $items;
    }

    protected function setDate()
    {
        $this->ready->date = date("Y-m-d\TH:i:sP", $this->raw->date);
    }

    protected function getAuthor($item)
    {
        $author = null;

        if (property_exists($item,'signer_id')) {
            $user = $this->di->get("vk\API")->usersGet($item->signer_id);
            $author = $user->response[0]->first_name . ' ' . $user->response[0]->last_name;
        }

        return $author;
    }

    protected function setAttachments($data, $item)
    {
        if (property_exists($data, 'attachments')) {
            foreach ($data->attachments as $attachment) {
                //This construction needed because audio files must be not in atom <content> tag,
                //but in separate <link> tag for each file
                if ($attachment->type == 'audio') {
                    $item->audios[] = $this->di->get('vk\\Audio')->setRaw($attachment)->get();
                    continue;
                }

                $preprocessor = $this->di->get('vk\\' . ucfirst($attachment->type));

                if ($preprocessor) {
                    $item->text .= "<br><br>";
                    $item->text .= $preprocessor->setRaw($attachment)->get();
                }
            }
        }

        return $item;
    }
}
