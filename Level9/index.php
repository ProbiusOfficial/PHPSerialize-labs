<?php
/*
--- HelloCTF - 反序列化靶场 关卡 9 : 构造函数的后门 ---
动态容器 flag 位于 /flag
© ProbiusOfficial(@hello-ctf.com) · github.com/ProbiusOfficial/PHPSerialize-labs
*/

$LEVEL_NO = 9;
require __DIR__ . '/../template/_header.php';

error_reporting(0);

class FLAG {
    var $flag_command = "echo 'HelloCTF';";
    public function __destruct()
    {
        eval ($this->flag_command);
    }
}

if(isset($_POST['o']))
{
    unserialize($_POST['o']);
}

require __DIR__ . '/../template/_footer.php';
