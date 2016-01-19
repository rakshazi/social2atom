<?php
namespace App\Controller;

Class Vk extends \Nius\Core\Controller
{
    protected $apiURL;
    protected $info;
    protected $curl;

    public function init()
    {
        $this->apiURL = 'https://api.vk.com/method/';
        $this->curl = new \Curl\Curl();
    }

    public function index()
    {
        echo "<h2>Use it as ".$this->app->config('app.url').'/vk/[groupname]</h2>';
    }

    public function get($domain)
    {    
        $this->info = $this->groupsGetById($domain);
        $items = $this->wallGet($domain);
        $atomXML = $this->generateAtom($this->info, $items);
    }

    protected function call($url)
    {
        $this->curl->get($url);

        if($this->curl->error) {
            return $this->call($url);
        }

        return $this->curl->response;
    }

    protected function wallGet($domain)
    {
        $url = $this->apiURL.'wall.get?domain='.$domain.'&count='.$this->app->config('default.vk.count');
        $raw = $this->call($url);
        $items = array();
        foreach($raw->response as $item)
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
        $description = explode("<br>",$raw->response[0]->description);

        return array(
            'title' => $raw->response[0]->name,
            'description' => $description[0],
            'url' => 'https://vk.com/'.$domain,
        );
    }

    protected function userGet($id)
    {
        if($id > 0)
        {
            $url = $this->apiURL.'users.get?user_ids='.$id;
            $raw = $this->call($url);
            return $raw->response[0]->first_name.' '.$raw->response[0]->last_name;
        }else{
            return $this->info['title'];
        }
    }

    protected function convertLinks($text)
    {
        $patterns = ['/\[(club[0-9]+)\|(.+)\]/u','/#([A-Za-zА-Яа-яЁ-ё0-9_]+)/u','/((www|http:\/\/)[^ <]+)/u'];
        $replacements = [
            '<a href="https://vk.com/\1" target="_blank">\2</a>',
            '<a href="https://vk.com/feed?q=#\1&section=search" target="_blank">#\1</a>',
            '<a href="\1" target="_blank">\1</a>'
        ];

        return preg_replace($patterns, $replacements, $text);
    }

    protected function preprocessPost($item)
    {
        $item->text = $this->convertLinks($item->text);
        $item->author = $this->userGet($item->from_id);
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

    protected function handleAttachmentVideo($attachments)
    {
        return "<b>Video available only on post source</b>";
    }

    protected function handleAttachmentLink($attachments)
    {
        $data = '';
        foreach($attachments as $attachment) {
            $data.= '<a href="'.$attachment->link->url.'">';
            $data.= $attachment->link->title;
            $data.= '</a>';
        }

        return $data;
    }

    protected function handleAttachmentPhoto($attachments)
    {
        $data = '';
        foreach($attachments as $attachment) {
            $data.= '<img src="'.$attachment->photo->src_big.'" title="'.$attachment->photo->text.'"/>';
        }

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
