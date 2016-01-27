<?php
namespace Rakshazi\Social2Atom\Converter\Vk;

class API extends \Rakshazi\Social2Atom\Converter\General\Preprocessor
{
    protected $url = 'https://api.vk.com/method/';

    protected function call($method, $params = array(), $tries = 0)
    {
        $params['https'] = 1;
        $url = $this->url . $method . '?' . http_build_query($params);
        $this->di->get('\Curl\Curl')->get($url);
        $curlError = $this->di->get('\Curl\Curl')->error;
        $apiError = property_exists($this->di->get('\Curl\Curl')->response, "error");
        if (($curlError || $apiError) && $tries < 10) {
            sleep(1);
            $tries++;
            return $this->call($method, $params, $tries);
        }

        return $this->di->get('\Curl\Curl')->response;
    }

    public function wallGet($id)
    {
        return $this->call(
            'wall.get',
            array(
                'owner_id' => $id,
                'count' => $this->di->config('vk.count'),
            )
        );
    }

    public function videoGet($owner_id, $video_id)
    {
        return $this->call(
            'video.get',
            array(
                'videos' => $owner_id . '_' . $video_id,
                'access_token' => $this->di->config('vk.token'),
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
