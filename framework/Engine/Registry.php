<?php
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 16.01.2016
 * Time: 15:46
 */
namespace LZ\Engine;

use LZ\Exception;
use LZ\File;

class Registry {

    const GLOBAL_PID = 0;

    protected static $_instance;

    protected $_configs = array();

    protected function __construct() {
        $this->_configs = array(
            self::GLOBAL_PID => new Config(),
            getmypid()       => new Config(),
        );
    }
    
    
    public static function get($moduleName) {
        return self::instance()->$moduleName;
    }

    public static function set($key, $pid, $value) {
        return self::instance()->_configs[$pid]->$key = $value;
    }

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __get($key) {
        $module = $this->_getModule($key);
        if (!is_null($module)) {
            return $module;
        }
        return $this->_getConfigProperty($key);
    }

    public function __set($key, $value) {
        $pid = getmypid();
        if (is_object($key)) {
            $config = $this->_getConfigProperty($key);
            if (isset($config['class'])) {
                if (isset($config['global']) && $config['global']) {
                    $pid = self::GLOBAL_PID;
                }
            } else if ($config) {
                $pid = self::GLOBAL_PID;
            }
        }
        $this->_configs[$pid]->$key = $value;
    }

    public function __isset($key) {
        return !is_null($this->_getConfigProperty($key)) || !is_null($this->_getModule($key));
    }

    public function __unset($key) {
        foreach ($this->_configs as $config) {
            unset($config[$key]);
        }
    }

    public function loadFile($fileName) {
        $file = File\Base::load($fileName);
        $this->addConfigs($file->toArray());
    }

    public function addConfigs($configs) {
        foreach ($configs as $key => $config) {
            $this->$key = $config;
        }
    }


    public static function __callStatic($moduleName, $args) {
        return self::instance()->$moduleName;
    }

    protected function _getConfigProperty($key) {
        foreach ($this->_configs as $pid => $config) {
            if (isset($config->$key)) {
                return $config->$key;
            }
        }
        return null;
    }

    protected function _getModule($key) {
        foreach ($this->_configs as $config) {
            if (isset($config->$key)) {
                return $config->$key;
            }
        }
        return null;
    }

}