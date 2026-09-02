# WriteUP · Level 6


同样是演示和考察序列化中不同类型变量的不同格式，但这里比较特殊 —— 因为引入了控制修饰符。

在对象的序列化和反序列化中，不同控制修饰符，序列化出来的字符串是不同的：

```PHP
<?php 

class Demo{
    public $a;
    protected $b;
    private $c;
}

echo urlencode(serialize(new Demo()));
# O%3A4%3A%22Demo%22%3A3%3A%7Bs%3A1%3A%22a%22%3BN%3Bs%3A4%3A%22%00%2A%00b%22%3BN%3Bs%3A7%3A%22%00Demo%00c%22%3BN%3B%7D
# O:4:"Demo":3:{s:1:"a";N;s:4:"%00*%00b";N;s:7:"%00Demo%00c";N;}
```

这里的 `%00` 是一个**不可见**的控制字符-`NULL`，对比不难看出对应的规则：

- **protected（受保护）：**  `%00*%00变量名`
- **private（私有）：** `%00类名%00变量名`

所以在序列化和反序列化的题目中 我们提倡在输出EXP的时候添加一个 `urlencode()` 以避免不可见字符的干扰。

在本题中只需要给对应的变量赋值即可，考察点是在输出的格式上面，由于不可见控制字符的带入，需要使用URL编码来避免丢失。

```PHP
<?php 
class protectedKEY{
    protected $protected_key = "protected_key";
}
class privateKEY{
    private $private_key = "private_key";
}

$exp = "protected_key=".urlencode(serialize(new protectedKEY))."&private_key=".urlencode(serialize(new privateKEY));

echo $exp;
```

