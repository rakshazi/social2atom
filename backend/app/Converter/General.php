<?php
namespace App\Converter;

class General
{
    /**
     * @var \App\App
     */
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }
}
