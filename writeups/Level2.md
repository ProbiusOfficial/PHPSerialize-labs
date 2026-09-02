# WriteUP · Level 2


考察对象的赋值操作，相比于面向过程，对对象中值的更改，需要通过 `->` 符号来指向可修改的变量，这里的可修改指的是 控制修饰符 public 对应的值，像 protected 和 private 修饰的值，需要使用更复杂的修改方法。

对于任何可以修改的值，我们使用 `$对象名 -> 对应值 = 值` .eg: `$object_name->a="a"`

所以在这个题目中，我们需要将 `$flag_string` 赋值给 `$free_flag` 以便我们后面的 `get_free_flag()` 函数将他输出出来。

**POST提交：**

code=`$target->free_flag=$flag_string;`

