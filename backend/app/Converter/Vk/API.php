<?php
namespace App\Converter\Vk;

class API extends \App\Converter\General
{
    /**
     * @var \Curl\Curl
     */
    protected $curl;
    protected $url = 'https://api.vk.com/method/';

    public function __construct($app)
    {
        parent::__construct($app);
        $this->curl = new \Curl\Curl();
    }

    protected function call($method, $params = array())
    {
         $url = $this->url.$method.'?'.http_build_query($params);

         $this->curl->get($url);
         if ($this->curl->error) {
             return $this->call($method, $params);
         }

         return $this->curl->response;
    }

    public function wallGet($domain)
    {
        return $this->call(
            'wall.get',
            array(
                'domain' => $domain,
                'count' => $this->app->config('vk.count')
            )
        );
    }

    public function usersGet($id)
    {
        return $this->call(
            'users.get',
            array(
                'user_ids' => $id
            )
        );
    }

    public function groupsGetById($domain)
    {
        return $this->call(
            'groups.getById',
            array(
                'group_id' => $domain,
                'fields' => 'description'
            )
        );
    }
}
