<?php
/*
--- HelloCTF - 反序列化靶场 关卡 14 : __invoke() ---
当尝试以调用函数的方式调用一个对象时,__invoke() 方法会被自动调用。例如 $obj()。
© ProbiusOfficial(@hello-ctf.com) · github.com/ProbiusOfficial/PHPSerialize-labs
*/

$LEVEL_NO = 14;
require __DIR__ . '/../template/_header.php';

class FLAG{
    function __invoke($x) {
        if ($x == 'get_flag') {
            include 'flag.php';
            echo $flag;
        }
    }
}

$obj = new FLAG();

if(isset($_POST['o'])) {
    eval($_POST['o']);
}

require __DIR__ . '/../template/_footer.php';
