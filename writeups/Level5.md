# WriteUP · Level 5


演示和考察序列化中 不同类型变量的不同格式。

而从结果上理解，反序列化其实和参数创建是一个等同的过程 —— 比如下面的例子：

```PHP
$a_string = "HelloCTF"; /*<=等价于=>*/ $a_string = unserialize('s:8:"HelloCTF";');
```

所以该题目按照后面部分的要求编写对应的变量进行序列化，将字符串赋给对应参数即可。

```PHP
<?php 

class a_class{
    public $a_value = "HelloCTF";
}

$your_object = new a_class();
$your_boolean = true;
$your_NULL = null;
$your_string = "IWANT";
$your_number = 1;
$your_object->a_value = "FLAG";
$your_array = array('a'=>"Plz",'b'=>"Give_M3");

$exp = "o=".serialize($your_object)."&s=".serialize($your_string)."&a=".serialize($your_array)."&i=".serialize($your_number)."&b=".serialize($your_boolean)."&n=".serialize($your_NULL);

echo $exp;
```

