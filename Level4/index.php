<?php
/*
--- HelloCTF - 反序列化靶场 关卡 4 : 序列化初体验 ---
© ProbiusOfficial(@hello-ctf.com) · github.com/ProbiusOfficial/PHPSerialize-labs
*/

$LEVEL_NO = 4;
require __DIR__ . '/../template/_header.php';

class FLAG3{
    private $flag3_object_array = array("se3","me");
}

class FLAG{
     private $flag1_string = "ser4l1ze";
     private $flag2_number = 2;
     private $flag3_object;

    function __construct() {
        $this->flag3_object = new FLAG3();
    }
}

$flag_is_here = new FLAG();

$code = $_POST['code'];

if(isset($code)){
    eval($code);
}

require __DIR__ . '/../template/_footer.php';
