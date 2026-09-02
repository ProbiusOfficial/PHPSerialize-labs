<?php
/*
--- HelloCTF - 反序列化靶场 关卡 22 : session 反序列化 ---
写入用 php_serialize 处理器,读取模拟 php 处理器:存储格式差异(key|value)造成注入
© ProbiusOfficial(@hello-ctf.com) · github.com/ProbiusOfficial/PHPSerialize-labs
*/

$LEVEL_NO = 22;
require __DIR__ . '/../template/_header.php';

error_reporting(0);

class FLAG {
    public $tj_name;
    public function __destruct() {
        if ($this->tj_name === 'get_flag') {
            include 'flag.php';
            echo $flag;
        }
    }
}

if (isset($_GET['a'])) {
    ini_set('session.serialize_handler', 'php_serialize'); /* 写入侧:php_serialize */
    session_start();
    $_SESSION['helloctf_data'] = $_GET['a'];
    session_write_close();
}

$session_dir = session_save_path() ? session_save_path() : sys_get_temp_dir();
$session_file = $session_dir . '/sess_' . session_id();
$raw = @file_get_contents($session_file);

echo "session 存储原文: <pre>" . htmlspecialchars($raw === false ? '(无,先通过 ?a= 写入)' : $raw) . "</pre>";
echo "读取侧按 php 处理器规则解析:以第一个 <b>|</b> 作为 key 与 value 的分隔<br>";

if ($raw !== false && ($pos = strpos($raw, '|')) !== false) {
    $value_part = substr($raw, $pos + 1);
    echo "解析出的 value: <pre>" . htmlspecialchars($value_part) . "</pre>";
    unserialize($value_part);
}

require __DIR__ . '/../template/_footer.php';
