<?php
/*
--- HelloCTF - 反序列化靶场 关卡 19 : 字符串逃逸·减少 ---
过滤使序列化字符串变短,而声明的长度不变 —— 解析器会"读多"一段内容
© ProbiusOfficial(@hello-ctf.com) · github.com/ProbiusOfficial/PHPSerialize-labs
*/

$LEVEL_NO = 19;
require __DIR__ . '/../template/_header.php';

error_reporting(0);

class FLAG {
    public $tj_a = '';
    public $helloctf_b = '';
}

function helloctf_filter($s) {
    return str_replace('getflag', '', $s); /* 过滤:内容中出现 getflag 就被删除 */
}

$o = new FLAG();
$o->tj_a = isset($_GET['a']) ? $_GET['a'] : '';
$o->helloctf_b = isset($_GET['b']) ? $_GET['b'] : '';

echo "原始序列化串: <pre>" . htmlspecialchars(serialize($o)) . "</pre>";

if ($o->helloctf_b === 'get_flag') {
    die("禁止直接提交 get_flag,想想办法~");
}

$filtered = helloctf_filter(serialize($o));
echo "过滤后序列化串: <pre>" . htmlspecialchars($filtered) . "</pre>";

$u = unserialize($filtered);
var_dump($u);

if ($u instanceof FLAG && $u->helloctf_b === 'get_flag') {
    include 'flag.php';
    echo $flag;
}

require __DIR__ . '/../template/_footer.php';
