<?php
namespace Rakshazi;
/**
 * Usage:
 * <code>
 * $s2a = new \Rakshazi\Social2Atom;
 * echo $s2a->convert('https://vk.com/apiclub');
 * </code>
 */
class Social2Atom
{
    protected $instances = array();
    protected $converters = array(
        'vk.com' => 'Vk',
        'facebook.com' => 'Facebook',
    );
    protected $config = array();

    public function __construct()
    {
        $url = 'http://example.com/';
        if(isset($_SERVER['HTTP_HOST'])) {
            $url = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'];
        }
        $this->config = array(
            'app.views' => dirname(dirname(__FILE__)).'/views/',
            'app.url' => $url,
            'app.uri' => $url.$_SERVER['PHP_SELF'],
            'vk.count' => 100,
        );
    }

    /**
     * Return instance of class
     *
     * @param string $class Full class name with namespace
     * @param bool $addDI Add \Rakshazi\Social2Atom to class constructor or not
     *
     * @return object|null
     */
    protected function getInstance($class, $addDI = true)
    {
        if (isset($this->instances[$class])) {
            return $this->instances[$class];
        }

        return $this->initInstance($class, $addDI);
    }

    /**
     * Init instance of class
     *
     * @param string $class Full class name with namespace
     * @param bool $addDI Add \Rakshazi\Social2Atom to class constructor or not
     *
     * @return object|null
     */
    protected function initInstance($class, $addDI)
    {
        if (class_exists($class)) {
            $this->instances[$class] = ($addDI) ? new $class($this) : new $class;

            return $this->getInstance($class);
        }

        return null;
    }

    /**
     * Get converter name and data source from URL
     *
     * @param string $url Social network url, eg: https://vk.com/apiclub
     *
     * @return array
     * @throws \Exception If something going wrong
     */
    protected function getInfoFromUrl($url)
    {
        $data = parse_url($url);
        $info = array();
        if (isset($this->converters[$data['host']])) {
            $info['converter'] = $this->converters[$data['host']];
        }
        if ($data['path']) {
            $info['source'] = substr($data['path'], 1);
        }

        if ($info['converter'] && $info['source']) {
            return $info;
        }

        throw new \Exception("Couldnot get info from url '$url'.");
    }

    /**
     * Return instance of class
     *
     * @param string $class Class name, may be short for social2atom converters
     * @param bool $addDI Add \Rakshazi\Social2Atom to class constructor or not
     *
     * @return object|null
     */
    public function get($class, $addDI = true)
    {
        if ($addDI) {
            $class = "\\Rakshazi\\Social2Atom\\Converter\\" . ucfirst($class);
        }

        return $this->getInstance($class, $addDI);
    }

    /**
     * Set config for all converters
     */
    public function setConfig($config = array())
    {
        $this->config = array_merge($this->config, $config);

        return $this;
    }

    /**
     * Set or get info from config
     *
     * @param string $key Config key
     * @param mixed $value Config value, if empty config for $key will be returned
     *
     * @return mixed
     */
    public function config($key, $value = null)
    {
        if ($value !== null) {
            $this->config[$key] = $value;

            return null;
        }

        if (isset($this->config[$key])) {
            return $this->config[$key];
        }

        return null;
    }

    /**
     * Run converter for specified url and return XML string as result
     *
     * @return string
     */
    public function convert($url)
    {
        $info = $this->getInfoFromUrl($url);
        $content = $this->get($info['converter'])->setSource($info['source'])->convert();

        return $content;
    }
}
