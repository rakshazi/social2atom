<?php
namespace Rakshazi\Social2Atom\Converter;

class General
{
    /**
     * @var \Rakshazi\Social2Atom
     */
    protected $di;

    public function __construct($di)
    {
        $this->di = $di;
    }

    /**
     * Set source info
     *
     * @return $this
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Prepare and convert info
     */
    protected function process()
    {}

    /**
     * Return atom feed XML from prepared data
     *
     * @param array $data
     *
     * @return string
     */
    protected function getAtom($data)
    {
        ob_start();
        extract($data);
        include $this->di->config('app.views').'atom.xml';
        $content = ob_get_clean();

        return $content;
    }

    /**
     * Run convertation process and return result
     *
     * @return mixed
     */
    public function convert()
    {
        $this->process();

        return $this->ready;
    }
}
