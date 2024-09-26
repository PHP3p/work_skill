<?php
//单例模式
class Singleton {
    private static $instance = null;

    private function __construct() {
        // 构造函数私有化，防止外部直接实例化
    }

    private function __clone() {
        // 禁止克隆
    }

    // 注意：没有定义 __wakeup() 方法

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // 假设我们有一个状态需要被序列化
    private $state = 'initial';

    public function setState($newState) {
        $this->state = $newState;
    }

    public function getState() {
        return $this->state;
    }
}

// 使用单例
$instance1 = Singleton::getInstance();
var_dump($instance1);
$instance1->setState('new state');

// 序列化单例对象
$serialized = serialize($instance1);

// 反序列化单例对象
// 注意：这里我们没有调用 __wakeup()，因为PHP会自动处理
$instance2 = unserialize($serialized);
var_dump($instance2);
// 检查 $instance2 是否是 $instance1 的同一个实例（实际上，它们不是“同一个”实例，
// 因为反序列化会创建一个新的对象副本，但其状态被恢复）
// 但由于单例的构造函数是私有的，且我们不会直接通过 new 关键字来创建新实例，
// 因此 $instance2 的 `state` 属性将被恢复为 'new state'，这模拟了单例的行为（尽管不严格）
echo $instance2->getState(); // 输出: new state

// 然而，重要的是要理解，在严格的单例定义下，$instance1 和 $instance2 并不指向内存中的同一个对象。
// 单例模式主要确保全局访问点的唯一性，而不是通过序列化/反序列化来维护对象的同一性。
die; 
