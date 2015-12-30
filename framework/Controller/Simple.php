<?php
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 23.12.2015
 * Time: 17:19
 */
namespace LZ\Controller;

abstract class Simple extends Base  {

    public $defaultAction = 'index';

    public function execute(\LZ\Request\Base $request) {

    }


    public function getRouteTree() {
        $controllerUrl = $this->_getControllerUrl();
        $reflection = new \ReflectionClass($this);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        $result = array();
        foreach ($methods as $method) {
            $methodName = $method->getName();
            if (substr($methodName,0,6)=='action') {
                $result[substr($methodName, 6)] = get_called_class();
            }
        }
        return array(
            $controllerUrl => $result
        );
    }

    protected function _getControllerUrl() {
        $class = get_called_class();
        return $class;
    }

}