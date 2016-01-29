<?php
namespace Rakshazi\Social2Atom\Converter\Facebook;

class API extends \Rakshazi\Social2Atom\Converter\General\API
{
    protected function init()
    {
        $this->setUrl('https://graph.facebook.com/v2.5/');
        $this->setDefaultParams(array('access_token' => $this->di->config('facebook.token')));
    }

    public function page($domain)
    {
        return $this->call(
            $domain,
            array(
                'fields' => 'about,link,name,feed.limit('.$this->di->config('facebook.count').')'
            )
        );
    }
}
