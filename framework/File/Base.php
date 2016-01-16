<?php
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 16.01.2016
 * Time: 16:00
 */
namespace LZ\File;

use LZ\Exception\File;

abstract class Base {

    protected $_fileName;

    protected $_params = array();

    public function __construct($fileName) {
        $this->_fileName = $fileName;
    }

    abstract public function init();
    
    
    abstract public function toArray();


    /**
     * @param $fileName
     * @return Base
     * @throws File
     */
    public static function load($fileName) {
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        $class = 'LZ\File\\'.ucfirst(strtolower($ext));
        if (!class_exists($class)) {
            throw new File('Unknown file extension:'.$ext);
        }
        return new $class($fileName);
    }

}