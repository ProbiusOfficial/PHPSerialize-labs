<?php
/*
--- HelloCTF - 反序列化靶场 关卡 10 : __wakeup() ---
© ProbiusOfficial(@hello-ctf.com) · github.com/ProbiusOfficial/PHPSerialize-labs
*/

$LEVEL_NO = 10;
require __DIR__ . '/../template/_header.php';

error_reporting(0);

class FLAG{
    function __wakeup() {
        include 'flag.php';
        echo $flag;
    }
}

if(isset($_POST['o']))
{
    unserialize($_POST['o']);
}

require __DIR__ . '/../template/_footer.php';
