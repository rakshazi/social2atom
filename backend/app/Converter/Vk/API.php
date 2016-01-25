<?php
namespace App\Converter\Vk;

class API extends \App\Converter\General
{
    protected $url = 'https://api.vk.com/method/';

    public function __construct($app)
    {
        parent::__construct($app);
    }

    protected function call($method, $params = array())
    {
        $params['https'] = 1;
        $url = $this->url . $method . '?' . http_build_query($params);
        $this->app->loadVendor('\Curl\Curl')->get($url);
        if (
            $this->app->loadVendor('\Curl\Curl')->error ||
            property_exists($this->app->loadVendor('\Curl\Curl')->response, "error")
        ) {
            sleep(1);
            return $this->call($method, $params);
        }

        return $this->app->loadVendor('\Curl\Curl')->response;
    }

    public function wallGet($domain)
    {
        $count = ($this->app->config('vk.count')) ? $this->app->config('vk.count') : 100;
        return $this->call(
            'wall.get',
            array(
                'domain' => $domain,
                'count' => $count,
            )
        );
    }

    public function videoGet($owner_id, $video_id)
    {
        return $this->call(
            'video.get',
            array(
                'videos' => $owner_id . '_' . $video_id,
                'access_token' => $this->app->config('vk.token'),
            )
        );
    }

    public function usersGet($id)
    {
        return $this->call(
            'users.get',
            array(
                'user_ids' => $id,
            )
        );
    }

    public function groupsGetById($domain)
    {
        return $this->call(
            'groups.getById',
            array(
                'group_id' => $domain,
                'fields' => 'description',
            )
        );
    }
}
