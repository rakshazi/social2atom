<?php

namespace App;

/**
 * Class App
 * @package App
 */
Class App extends \Nius\Core\App
{
    public function __construct($config = array())
    {
        $config = include ROOT.'/config.php';
        $config['templates.path'] = ROOT.'/frontend/';
        parent::__construct($config);

        $this->config('app.url',$this->request->getUrl());
        $this->config('app.uri',$this->request->getUrl().$this->request->getPath());
    }

    /**
     * {@inheritdoc}
     */
    public function setRoutes()
    {
        // $this->get('/', '\App\Controller\General:index');
        $this->get('/vk','\App\Controller\Vk:index');
        $this->get('/vk/:domain', '\App\Controller\Vk:get');

        parent::setRoutes();
    }
}
