<?php
/*
--- HelloCTF - 反序列化靶场 关卡 13 : __toString() ---
__toString() 方法用于一个类被当成字符串时应怎样回应。例如 echo $obj;
© ProbiusOfficial(@hello-ctf.com) · github.com/ProbiusOfficial/PHPSerialize-labs
*/

$LEVEL_NO = 13;
require __DIR__ . '/../template/_header.php';

class FLAG {
    function __toString() {
        echo "I'm a string ~~~";
        include 'flag.php';
        return $flag;
    }
}

$obj = new FLAG();

if(isset($_POST['o'])) {
    eval($_POST['o']);
}

require __DIR__ . '/../template/_footer.php';
