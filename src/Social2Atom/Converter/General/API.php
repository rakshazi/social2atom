<?php
namespace Rakshazi\Social2Atom\Converter\General;

class API extends Preprocessor
{
    protected $url;
    protected $default_params = array();

    public function __construct($di)
    {
        parent::__construct($di);
        $this->init();
    }

    /**
     * Init default params and api url, only for child classes
     */
    protected function init(){}

    /**
     * Call API method and return result
     *
     * @param string $method API method name
     * @param array $params Method params
     * @param string $httpMethod HTTP Method
     * @param int $tries Just don't touch this, it's "break" for infinity loop
     *
     * @return mixed
     */
    protected function call($method, $params = array(), $httpMethod = "GET", $tries = 0)
    {
        $params = array_merge($this->default_params,$params);
        $url = $this->url . $method . '?' . http_build_query($params);
        call_user_func_array(array($this->di->get('\Curl\Curl', false),strtolower($httpMethod)), array($url));
        $curlError = $this->di->get('\Curl\Curl', false)->error;
        $apiError = property_exists($this->di->get('\Curl\Curl', false)->response, "error");
        if (($curlError || $apiError) && $tries < 10) {
            sleep(1);
            $tries++;
            return $this->call($method, $params, $httpMethod, $tries);
        }

        return $this->di->get('\Curl\Curl', false)->response;
    }

    protected function setUrl($url = '')
    {
        $this->url = $url;

        return $this;
    }

    protected function setDefaultParams($array = array())
    {
        $this->default_params = $array;

        return $this;
    }
}
