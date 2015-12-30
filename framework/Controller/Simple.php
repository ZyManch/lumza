<?php
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 23.12.2015
 * Time: 17:19
 */
namespace SF\Controller;

abstract class Simple extends Base  {

    public $defaultAction = 'index';

    public function execute(\SF\Request\Base $request) {

    }


    public function getRouteTree() {
        return array(
            ''
        );
    }

    protected function _getControllerUrl() {
        $class = get_called_class();
        return
    }

}