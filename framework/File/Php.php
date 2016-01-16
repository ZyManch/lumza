<?php
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 16.01.2016
 * Time: 16:00
 */
namespace LZ\File;

use LZ\Exception\File;

class Php extends Base {

    public function init() {
        $this->_params = include $this->_fileName;
    }

    public function toArray() {
        return $this->_params;
    }


}