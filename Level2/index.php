<?php
/*
--- HelloCTF - 反序列化靶场 关卡 2 : 对象中值的传递 ---
© ProbiusOfficial(@hello-ctf.com) · github.com/ProbiusOfficial/PHPSerialize-labs
*/

$LEVEL_NO = 2;
require __DIR__ . '/../template/_header.php';

error_reporting(0);

$flag_string = "helloctf{I_giv3_t0_y0u&y0u_giv3_t0_me}";

class FLAG{
    public $free_flag = "???";

    function get_free_flag(){
        return $this->free_flag;
    }
}
$target = new FLAG();

$code = $_POST['code'];

if(isset($code)){
    eval($code);
}
echo "Now Flag is ". $target->get_free_flag() ."<br>";

require __DIR__ . '/../template/_footer.php';
