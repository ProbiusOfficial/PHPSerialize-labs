<?php
/*
--- PHPSerialize-labs · PHP Code Runner(教学用) ---
选手在此运行完整 PHP 代码,辅助构造序列化 payload
© ProbiusOfficial(@hello-ctf.com) · github.com/ProbiusOfficial/PHPSerialize-labs
*/

header('Content-Type: text/plain; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['code'])) {
    echo "PHP Code Runner:通过 POST code=<php代码> 调用";
    exit;
}

$helloctf_code = $_POST['code'];
/* 允许选手直接粘贴完整 PHP 文件:去掉开头的 <?php */
$helloctf_code = preg_replace('/^\s*<\?php/i', '', $helloctf_code);

ini_set('display_errors', '1');
error_reporting(E_ALL);

eval($helloctf_code);
