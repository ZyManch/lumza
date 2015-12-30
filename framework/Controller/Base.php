<?php
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 23.12.2015
 * Time: 17:19
 */
namespace SF\Controller;

abstract class Base  {
    
    abstract function execute(\SF\Request\Base $request);
    
    
    abstract function getRouteTree();

}