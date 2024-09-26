<?php
class SingletonLazy {  
    private static $instance = null;  
    private function __construct() {}  
    private function __clone() {}  
    private function __wakeup() {}  
  
    public static function getInstance() {  
        if (self::$instance === null) {  
            self::$instance = new self();  
        }  
        return self::$instance;  
    }  
}  
  
// 使用  
$instance1 = SingletonLazy::getInstance();  
$instance2 = SingletonLazy::getInstance();  
// $instance1 和 $instance2 是同一个实例

class SingletonEager {  
    private static $instance = new self();  
    private function __construct() {}  
    private function __clone() {}  
    private function __wakeup() {}  
  
    public static function getInstance() {  
        return self::$instance;  
    }  
}  
  
// 使用  
$instance1 = SingletonEager::getInstance();  
$instance2 = SingletonEager::getInstance();  
// $instance1 和 $instance2 是同一个实例

//note 
# 在PHP中选择哪种模式主要取决于你的具体需求。如果你希望尽可能早地创建实例并且不关心资源的浪费（或者确信实例一定会被使用），那么饿汉模式可能是更好的选择。如果你希望延迟实例的创建并减少不必要的资源消耗，那么懒汉模式可能更适合你。不过，在PHP中，由于GIL的存在，线程安全通常不是主要考量点
