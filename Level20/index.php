<?php
/*
--- HelloCTF - 反序列化靶场 关卡 20 : 字符串逃逸·增多 ---
过滤使序列化字符串变长,内容"溢出"声明的长度 —— 后面的结构被挤出去
© ProbiusOfficial(@hello-ctf.com) · github.com/ProbiusOfficial/PHPSerialize-labs
*/

$LEVEL_NO = 20;
require __DIR__ . '/../template/_header.php';

error_reporting(0);

class FLAG {
    public $tj_a = '';
    public $helloctf_b = '';
}

function helloctf_filter($s) {
    return str_replace('x', 'helloctf', $s); /* 过滤:x 会被替换为更长的 helloctf */
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
