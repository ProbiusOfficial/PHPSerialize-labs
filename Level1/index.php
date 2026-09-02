<?php
/*
--- HelloCTF - 反序列化靶场 关卡 1 : 类的实例化 ---
© ProbiusOfficial(@hello-ctf.com) · github.com/ProbiusOfficial/PHPSerialize-labs
*/

$LEVEL_NO = 1;
require __DIR__ . '/../template/_header.php';

class FLAG{
    public $flag_string = "helloctf{OK_Now_y0u_c4n_se3_me}";

    function __construct(){
        echo $this->flag_string;
    }
}

$code = $_POST['code'];

if(isset($code)){
    if (stripos($code, "new") === false) {
        echo "Not This level!";
    } else {
       eval($code);
    }
}

require __DIR__ . '/../template/_footer.php';
