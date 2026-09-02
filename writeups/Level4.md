# WriteUP · Level 4


一道用来考察序列化的套壳题目，序列化虽然不会标记函数，但是会完整的输出变量和变量内容。

题目已经使用 `$flag_is_here = new FLAG();` 实例化创建了一个对象，所以我们只需要序列化并且打印出来这一段字符串。

**POST提交：**

code=`echo serialize($flag_is_here);`

你会得到这样的字符串：

```PHP
O:4:"FLAG":3:{s:18:"FLAGflag1_string";s:8:"ser4l1ze";s:18:"FLAGflag2_number";i:2;s:18:"FLAGflag3_object";O:5:"FLAG3":1:{s:25:"FLAG3flag3_object_array";a:2:{i:0;s:3:"se3";i:1;s:2:"me";}}}
```

挑出对应部分拼接即可。

