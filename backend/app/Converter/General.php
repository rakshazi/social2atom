<?php
namespace App\Converter;

Class General
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
