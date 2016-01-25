<?php
namespace App\Converter\Vk\Preprocessor;

class Atom extends \App\Converter\General\Preprocessor
{
    protected function process()
    {
        $this->ready = array('feed' => array(), 'items' => array());

        $this->setFeed();
        $this->setItems();
    }

    public function get()
    {
        $this->process();
        $this->app->render('vk.xml', $this->ready);
    }

    protected function setFeed()
    {
        $description = explode("<br>", $this->raw['group']->response[0]->description);

        $this->ready['feed'] = array(
            'title' => $this->raw['group']->response[0]->name,
            'description' => $description[0],
            'origin_url' => 'https://vk.com/' . $this->raw['group']->response[0]->screen_name,
            'app_url' => $this->app->config('app.url'),
            'self_url' => $this->app->config('app.uri'),
        );
    }

    protected function setItems()
    {
        $this->ready['items'] = $this->raw['items'];
    }
}
