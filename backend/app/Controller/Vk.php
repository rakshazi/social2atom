<?php
namespace App\Controller;

Class Vk extends \Nius\Core\Controller
{
    protected $apiURL;

    public function init()
    {
        $this->apiURL = 'https://api.vk.com/method/';
    }

    public function index()
    {
        echo "<h2>Use it as ".$this->app->config('app.url').'/vk/[groupname]</h2>';
    }

    public function get($domain)
    {
        $items = $this->wallGet($domain);
        $info = $this->groupsGetById($domain);
        $atomXML = $this->generateAtom($info, $items);
    }

    protected function call($url)
    {
        $opts = array(
            'http' => array(
                'method'=>"GET",
                'header'=>"Content-Type: text/html; charset=utf-8"
            )
        );
        $context = stream_context_create($opts);

        return file_get_contents($url, false, $context);
    }

    protected function wallGet($domain)
    {
        $url = $this->apiURL.'wall.get?domain='.$domain.'&count='.$this->app->config('default.vk.count');
        $raw = $this->call($url);
        $data = json_decode($raw);
        $items = array();
        foreach($data->response as $item)
        {
            if (is_object($item)) {
                $items[$item->id] = $this->preprocessPost($item);
            }
        }

        return $items;
    }

    protected function groupsGetById($domain)
    {
        $url = $this->apiURL.'groups.getById?group_id='.$domain.'&fields=description';
        $raw = $this->call($url);
        $data = json_decode($raw);
        $description = explode("<br>",$data->response[0]->description);

        return array(
            'title' => $data->response[0]->name,
            'description' => $description[0],
            'url' => 'https://vk.com/'.$domain,
        );
    }

    protected function preprocessPost($item)
    {
        $raw = $item->text;
        $patterns = ['/\[(club[0-9]+)\|(.+)\]/u','/#([A-Za-zА-Яа-я0-9_]+)/u','/((www|http:\/\/)[^ ]+)/u'];
        $replacements = [
            '<a href="https://vk.com/\1" target="_blank">\2</a>',
            '<a href="https://vk.com/feed?q=#\1&section=search" target="_blank">#\1</a>',
            '<a href="\1" target="_blank">\1</a>'
        ];
        $item->text = preg_replace($patterns, $replacements, $raw);
        $item->title = substr($item->text, 0, strpos($item->text, "<br>"));

        if (property_exists($item,'attachments'))
        {
            foreach($item->attachments as $attachment)
            {
                $handler = "handleAttachment".ucfirst($attachment->type);
                if (method_exists($this,$handler))
                {
                    $item->text.= "<br><br>";
                    $item->text.= call_user_func(array($this,$handler),array($attachment));
                }
            }
        }

        return $item;
    }

    protected function handleAttachmentVideo($attachment)
    {
        return "<b>Video available only on post source</b>";
    }

    protected function handleAttachmentLink($attachment)
    {
        $data = '<a href="'.$attachment[0]->link->url.'">';
        $data.= $attachment[0]->link->title;
        $data.= '</a>';

        return $data;
    }

    protected function handleAttachmentPhoto($attachment)
    {
        $data = '<img src="'.$attachment[0]->photo->src_big.'"';
        $data.= 'title="'.$attachment[0]->photo->text.'"';
        $data.= ' style="max-width: 800px;display: block;margin-left: auto;margin-right: auto"/>';

        return $data;
    }

    protected function generateAtom($info, $items)
    {
        $vars = array(
            'title' => $info['title'],
            'description' => $info['description'],
            'url' => $info['url'],
            'items' => $items,
            'app_url' => $this->app->config('app.url'),
            'self_url' => $this->app->config('app.uri'),
        );
        $this->app->render('vk.xml',$vars);
    }
}
