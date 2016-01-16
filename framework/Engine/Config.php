<?php
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 16.01.2016
 * Time: 16:39
 */
namespace LZ\Engine;

class Config {

    protected $_config = array();

    protected $_modules = array();



    public function __get($key) {
        $module = $this->_getModule($key);
        if (!is_null($module)) {
            return $module;
        }
        return $this->_getConfigProperty($key);
    }

    public function __set($key, $value) {
        if (is_object($key)) {
            $this->_modules[$key] = $value;
        } else {
            $this->_config[$key] = $value;
        }
    }

    public function __isset($key) {
        return !is_null($this->_getConfigProperty($key)) || !is_null($this->_getModule($key));
    }

    public function __unset($key) {
        if (isset($this->_config[$key])) {
            unset($this->_config[$key]);
        }
        if (isset($this->_modules[$key])) {
            unset($this->_modules[$key]);
        }
    }

    protected function _getConfigProperty($key) {
        $keys = explode('.', $key);
        $mainKey = array_shift($key);
        if (!isset($this->_config[$mainKey])) {
            return null;
        }
        $config = $this->_config[$mainKey];
        foreach ($keys as $key) {
            if (!isset($config[$key])) {
                return null;
            }
            $config = $config[$key];
        }
        return $config;
    }

    protected function _getModule($key) {
        if (isset($this->_modules[$key])) {
            return $this->_modules[$key];
        }
        if (!isset($this->_config[$key])) {
            return null;
        }
        $config = $this->_config[$key];
        if (!isset($config['class'])) {
            return null;
        }
        $args = (isset($config['args']) ? $config['args'] : array());
        $properties = (isset($config['properties']) ? $config['properties'] : array());
        $calls = (isset($config['call']) ? $config['call'] : array());
        $reflection = new \ReflectionClass($config['class']);
        $module = $reflection->newInstanceArgs($args);
        foreach ($properties as $property => $value) {
            $module->$property = $value;
        }
        foreach ($calls as $method => $params) {
            call_user_func_array(array($module, $method), $params);
        }
        if (!isset($config['fabric']) || !$config['fabric']) {
            $this->_modules[$key] = $module;
        }
        return $module;
    }

}