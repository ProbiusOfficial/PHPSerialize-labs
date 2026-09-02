# WriteUP · Level 3


考察 控制修饰符：

- **public（公有）：** 公有的类成员可以在任何地方被访问。
- **protected（受保护）：** 受保护的类成员则可以被其自身以及其子类和父类访问。(可继承)
- **private（私有）：** 私有的类成员则只能被其定义所在的类访问。(不可继承)

这里 SubFLAG 继承了 FLAG，除开 public 修饰的值，对于另外两个：

- `protected $protected_flag` 可以通过 `get_protected_flag()` / `get_private_flag()` 访问，因为受保护的变量是可以被继承的。
- `private $private_flag`则只能通过 `get_private_flag()` 进行访问，因为私有变量不能被继承。

而对象中函数的调用和值的访问类似，也通过 `->` 符号实现：`$对象名 -> 函数名();`

**POST提交：**

code=`echo $target->public_flag.$target->get_protected_flag().$target->get_private_flag();`

code=`echo $target->public_flag.$sub_target->show_protected_flag().$target->get_private_flag();`

