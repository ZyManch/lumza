<?php
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 23.12.2015
 * Time: 17:19
 */
namespace LZ\Controller;

abstract class Base  {
    
    abstract function execute(\LZ\Request\Base $request);
    
    
    abstract function getRouteTree();

}