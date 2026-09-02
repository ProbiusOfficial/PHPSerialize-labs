<?php
/*
--- HelloCTF - 反序列化靶场 关卡 23 : phar 反序列化 ---
phar:// 流被文件函数触碰时,metadata 会被自动反序列化
本目录附带 build_phar.php 辅助构建脚本与构建好的 helloctf.phar
© ProbiusOfficial(@hello-ctf.com) · github.com/ProbiusOfficial/PHPSerialize-labs
*/

$LEVEL_NO = 23;
require __DIR__ . '/../template/_header.php';

error_reporting(0);

class FLAG {
    public $helloctf_cmd;
    public function __destruct() {
        eval($this->helloctf_cmd);
    }
}

if (isset($_GET['file'])) {
    is_dir($_GET['file']); /* 文件操作函数触碰 phar:// 流 → metadata 反序列化 */
    echo "已对 " . htmlspecialchars($_GET['file']) . " 执行 is_dir()";
}

require __DIR__ . '/../template/_footer.php';
