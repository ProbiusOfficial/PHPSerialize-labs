# WriteUP · Level 22 · session 反序列化

## 原理

- 写入侧:`session.serialize_handler = php_serialize`,整个 `$_SESSION` 数组被
  `serialize()` 成一个串存储,用户内容里即使有 `|` 也只是字符串的一部分;
- 读取侧:按 **php** 处理器规则解析——存储格式是 `key|serialize(value)`,
  以**第一个 `|`** 作为 key 与 value 的分界。

当写入的值以 `|` 开头时,读取侧会把 `|` 后面的内容当成某个 key 的序列化值
直接 `unserialize`,造成对象注入。

## EXP

```
?a=|O:4:"FLAG":1:{s:7:"tj_name";s:8:"get_flag";}
```

URL 编码后提交(`|` = `%7C`)。再次访问页面,读取侧还原出 FLAG 对象,
析构时校验通过输出 flag。

