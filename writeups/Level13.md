# WriteUP · Level 13


本关考验你魔法方法中的 __toString() 方法，你将有该方法的对象，打印出来，得到 Flag 方可过关，你明白吗（雾

__toString() 方法用于一个类被当成字符串时应怎样回应。例如 echo $obj; 应该显示些什么。

题目已经完成了类的实例化：`$obj = new FLAG();`

所以我们只需要 POST 提交 `o=echo $obj;` 即可。

