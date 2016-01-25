<?php

namespace App;

/**
 * Class App
 * @package App
 */
class App extends \Nius\Core\App
{
    protected $converterInstances = array();
    protected $cache;

    public function __construct($config = array())
    {
        $config = include ROOT . '/config.php';
        $config['templates.path'] = ROOT . '/frontend/';
        parent::__construct($config);

        $this->config('app.url', $this->request->getUrl());
        $this->config('app.uri', $this->request->getUrl() . $this->request->getPath());

        $this->setCache();
    }

    /**
     * {@inheritdoc}
     */
    public function setRoutes()
    {
        $this->get('/vk', '\App\Controller\Vk:index');
        $this->get('/vk/:domain', '\App\Controller\Vk:get');
        $this->get('/callback/vk', '\App\Controller\Callback:vk');

        parent::setRoutes();
    }

    public function setCache()
    {
        $this->cache = new \Nius\Cache();
        $this->cache->setConfig(array(
            'dir' => ($this->config('cache.dir')) ? $this->config('cache.dir') : ROOT . '/cache',
            'lifetime' => ($this->config('cache.lifetime')) ? $this->config('cache.lifetime') : 86400,
        ));
    }

    protected function getConverterInstance($class, $addApp = true)
    {
        if (isset($this->converterInstances[$class])) {
            return $this->converterInstances[$class];
        }

        if (class_exists($class)) {
            $this->converterInstances[$class] = ($addApp) ? new $class($this) : new $class;

            return $this->getConverterInstance($class);
        }

        return null;
    }

    public function load($class)
    {
        $class = "\\App\\Converter\\" . ucfirst($class);

        return $this->getConverterInstance($class);
    }

    public function loadVendor($class)
    {
        return $this->getConverterInstance($class, false);
    }

    /**
     * Create a closure that instantiates (or gets from container) and then calls
     * the action method.
     *
     * Also if the methods exist on the controller class, call setApp(), setRequest()
     * and setResponse() passing in the appropriate object.
     *
     * @param  string $name controller class name and action method name separated by a colon
     * @author Rob Allen <rob@akrabat.com>
     * @return closure
     */
    protected function createControllerClosure($name)
    {
        list($controllerName, $actionName) = explode(':', $name);
        // Create a callable that will find or create the controller instance
        // and then execute the action
        $app = $this;
        $callable = function () use ($app, $controllerName, $actionName) {
            $cache = $app->cache->get($app->request);
            if ($cache) {
                echo $cache;
                return $cache;
            }
            // Try to fetch the controller instance from Slim's container
            if ($app->container->has($controllerName)) {
                $controller = $app->container->get($controllerName);
            }
            // not in container, assume it can be directly instantiated
            if (!isset($controller)) {
                $controller = new $controllerName($app);
            }
            // Set the app, request and response into the controller if we can
            if (method_exists($controller, 'setApp')) {
                $controller->setApp($app);
            }
            if (method_exists($controller, 'setRequest')) {
                $controller->setRequest($app->request);
            }
            if (method_exists($controller, 'setResponse')) {
                $controller->setResponse($app->response);
            }
            // Call init in case the controller wants to do something now that
            // it has an app, request and response.
            if (method_exists($controller, 'init')) {
                $controller->init();
            }
            ob_start();
            call_user_func_array(array($controller, $actionName), func_get_args());
            $result = ob_get_contents();
            ob_end_clean();

            $app->cache->set($app->request, $result);

            echo $result;
        };
        return $callable;
    }
}
