<?php
namespace Rakshazi\Social2Atom\Converter\Vk;

class API extends \Rakshazi\Social2Atom\Converter\General\API
{
    protected function init()
    {
        $this->setUrl('https://api.vk.com/method/');
        $this->setDefaultParams(array('https' => 1));
    }

    public function wallGet($id)
    {
        return $this->call(
            'wall.get',
            array(
                'owner_id' => ($id > 0) ? '-'.$id : $id,
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
