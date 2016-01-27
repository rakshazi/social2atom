<?php
namespace Rakshazi\Social2Atom\Converter\Vk;

class Atom extends \Rakshazi\Social2Atom\Converter\General\Preprocessor
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

        ob_start();
        extract($this->ready);
        include $this->di->config('app.views').'vk.xml';
        $content = ob_get_clean();

        return $content;
    }

    protected function setFeed()
    {
        $description = explode("<br>", $this->raw['group']->response[0]->description);

        $this->ready['feed'] = array(
            'title' => $this->raw['group']->response[0]->name,
            'description' => $description[0],
            'origin_url' => 'https://vk.com/' . $this->raw['group']->response[0]->screen_name,
            'app_url' => $this->di->config('app.url'),
            'self_url' => $this->di->config('app.uri'),
        );
    }

    protected function setItems()
    {
        $this->ready['items'] = $this->raw['items'];
    }
}
