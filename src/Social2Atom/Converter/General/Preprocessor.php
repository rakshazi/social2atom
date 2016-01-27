<?php
namespace Rakshazi\Social2Atom\Converter\General;

class Preprocessor
{
    /**
     * @var \Rakshazi\Social2Atom
     */
    protected $di;

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

    public function __construct($di)
    {
        $this->di = $di;
    }

    /**
     * Set raw data for preprocessor
     *
     * @param mixed $raw
     *
     * @return $this
     */
    public function setRaw($raw)
    {
        $this->raw = $raw;

        return $this;
    }

    /**
     * Processing raw data
     *
     * @return bool
     */
    protected function process()
    {}

    /**
     * Return ready data
     *
     * @return mixed
     */
    public function get()
    {
        $this->process();

        return $this->ready;
    }
}
