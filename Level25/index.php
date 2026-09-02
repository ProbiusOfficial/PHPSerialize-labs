<?php
/*
--- HelloCTF - 反序列化靶场 关卡 25 : 原生类 · Error / Exception ---
© ProbiusOfficial(@hello-ctf.com) · github.com/ProbiusOfficial/PHPSerialize-labs
*/

$LEVEL_NO = 25;
require __DIR__ . '/../template/_header.php';

error_reporting(0);

if (isset($_POST['o'])) {
    $u = unserialize($_POST['o']);

    if ($u instanceof Exception) {
        $out = (string)$u;   // Exception 自带 __toString
        echo $out;

        if (stripos($out, '<script') !== false && stripos($out, 'alert') !== false) {
            include 'flag.php';
            echo $flag;
        }
    }
}

require __DIR__ . '/../template/_footer.php';
