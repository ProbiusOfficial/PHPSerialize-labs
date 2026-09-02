<?php
/*
--- HelloCTF - 反序列化靶场 关卡 11 : __wakeup() Bypass ---
CVE-2016-7124 · PHP5 < 5.6.25 / PHP7 < 7.0.10
序列化字符串中属性个数大于真实属性个数时会跳过 __wakeup 的执行
© ProbiusOfficial(@hello-ctf.com) · github.com/ProbiusOfficial/PHPSerialize-labs
*/

$LEVEL_NO = 11;
require __DIR__ . '/../template/_header.php';

error_reporting(0);

include 'flag.php';

class FLAG {
    public $flag = "FAKEFLAG";

    public function  __wakeup(){
        global $flag;
        $flag = NULL;
    }
    public function __destruct(){
        global $flag;
        if ($flag !== NULL) {
            echo $flag;
        }else
        {
            echo "sorry,flag is gone!";
        }
    }
}

if(isset($_POST['o']))
{
    unserialize($_POST['o']);
}else {
    phpinfo();
}

require __DIR__ . '/../template/_footer.php';
