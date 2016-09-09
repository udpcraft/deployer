<?php

namespace Afanty\Web;

use Afanty\Config\Env;
use Afanty\Loader\Autoloader;
use Afanty\Routing\Router;
use Afanty\Helper\Singleton;


class Application extends Singleton
{
    private $_enableModules = [];
    
    //设置已经启用的模块
    public function setEnableModules($modules)
    {
        $this->_enableModules = $modules;
    }

    public function createProject()
    {
        //init env
        Env::init(ROOT_DIR);
        Env::load(ROOT_DIR . DS . '.env');

        //add project loader
        Autoloader::addnamespace(APP_LOADER_PREFIX, ROOT_DIR . DS . 'app');

        return $this;
    }

    public function run()
    {
        //get request resource path
        //dispatch router
        //render response        
        try {
            $router = Router::getInstance()->create();

            $currentModule = $router->getModule();

            if (! $this->_isModuleEnable($currentModule)) {
                throw new \Afanty\Exception\Request("module [$currentModule] is disabled");
            }

            //auth
            //login
            \Afanty\Events\Manager::handle();

            $router->dispatch();
        } catch (\Exception $e) {
            $this->_onError($e);
        }
    }

    private function _isModuleEnable($module)
    {
        if (! in_array($module, $this->_enableModules)) {
            return false;
        }
        
        return true;
    }

    /**
     *
     * @param Exception $e
     */
    private function _onError($e)
    {
        header('HTTP/1.1 ' . $e->getCode());
        header('sc-msg: ' . $e->getMessage());

        $logStr = date('Y-m-d H:i:s') .' -- ' . $e->getCode().' -- '.$e->getMessage();
        \Afanty\Logger\File::log('runtime', $logStr);
    }
}
