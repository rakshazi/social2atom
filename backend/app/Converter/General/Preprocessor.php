<?php
namespace App\Converter\General;

class Preprocessor
{
    /**
     * @var \App\App
     */
    protected $app;

    /**
     * Raw data
     * @var mixed
     */
    protected $raw;

    /**
     * Ready (prepared) data
     * @var mixed
     */
    protected $ready;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function setRaw($raw)
    {
        $this->raw = $raw;

        return $this;
    }

    protected function process()
    {
        return true;
    }

    public function get()
    {
        $this->process();

        return $this->ready;
    }
}
