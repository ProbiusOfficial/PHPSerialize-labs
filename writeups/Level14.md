# WriteUP · Level 14


该关卡考察魔术方法 __invoke()，当尝试以调用函数的方式调用一个对象时，__invoke() 方法会被自动调用。例如 $obj()。

__invoke() 也可以接受参数，如题目所示：

```PHP
class FLAG{
    function __invoke($x) {
        if ($x == 'get_flag') {
            include 'flag.php';
            echo $flag;
        }
    }
}
$obj = new FLAG();
```

对象已经被实例化，我们需要给该对象传入 'get_flag' 字符串：

`o=$obj('get_flag')`,POST 提交即可。

