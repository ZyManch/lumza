<?php
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 16.01.2016
 * Time: 12:15
 */
namespace LZ\Engine;

use LZ\Exception;

class Event {

    public $properties;

    public $object;

    public $event;

    public $canceled = false;

    public function __construct($object, $event, $properties = array()) {
        $this->object = $object;
        $this->event = $event;
        $this->properties = $properties;
    }

}