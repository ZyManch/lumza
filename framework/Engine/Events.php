<?php
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 16.01.2016
 * Time: 11:56
 */
namespace LZ\Engine;

use LZ\Exception;

class Events {

    const GLOBAL_PID = 0;

    protected static $_objects = array();

    protected static $_classes = array();


    public static function on($object, $event, $func, $onlyCurrentRequest = true) {
        if (!$event) {
            throw new Exception\Event('Event can`t be blank');
        }
        $pid = self::GLOBAL_PID;
        if ($onlyCurrentRequest) {
            $pid = getmypid();
            self::$_classes[$pid]['close']['LZ\Request\Base\Request'][] = function() use ($pid) {
                if (isset(self::$_classes[$pid])) {
                    unset(self::$_classes[$pid]);
                }
                if (isset(self::$_objects[$pid])) {
                    unset(self::$_objects[$pid]);
                }
            };
        }
        if (is_object($object)) {
            $class = get_class($object);
            if(!isset(self::$_objects[$pid][$event][$class])) {
                self::$_objects[$pid][$event][$class] = array();
            }
            self::$_objects[$pid][$event][$class][] = array($object, $func);
        } else {
            if (!isset(self::$_classes[$pid][$event][$object])) {
                self::$_classes[$pid][$event][$object] = array();
            }
            self::$_classes[$pid][$event][$object][] = $func;
        }
    }

    public static function trigger($object, $event, $properties = array()) {
        if (!is_object($object)) {
            throw new Exception\Event('Can triggered only objects');
        }
        $e = new Event($object, $event, $properties);
        $e->canceled = false;
        foreach (self::_getFunctionsForEvent($object, $event) as $func) {
            if (is_callable($func)) {
                if (!$func($e)) {
                    $e->canceled = true;
                }
            } else {
                if (!call_user_func($func, $e)) {
                    $e->canceled = true;
                }
            }
        }
        return !$event->canceled;
    }

    protected static function _getFunctionsForEvent($object, $event) {
        $result = array();
        $class = get_class($object);
        foreach (array(self::GLOBAL_PID,getmypid()) as $pid) {
            foreach (self::_extractObjects($pid,$event,$class) as $item) {
                if ($item[0] == $object) {
                    $result[] = $item[1];
                }
            }
            foreach (self::_extractClasses($pid,$event) as $class => $items) {
                if ($object instanceof $class) {
                    $result = array_merge($result, $items);
                }
            }
        }
        return $result;
    }

    protected static function _extractObjects($pid, $event, $class) {
        if (!isset(self::$_objects[$pid][$event][$class])) {
            return array();
        }
        return self::$_objects[$pid][$event][$class];
    }

    protected static function _extractClasses($pid, $event) {
        if (!isset(self::$_classes[$pid][$event])) {
            return array();
        }
        return self::$_classes[$pid][$event];
    }

}