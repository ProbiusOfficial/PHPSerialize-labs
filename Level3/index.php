<?php
/*
--- HelloCTF - 反序列化靶场 关卡 3 : 对象中值的权限 ---
© ProbiusOfficial(@hello-ctf.com) · github.com/ProbiusOfficial/PHPSerialize-labs
*/

$LEVEL_NO = 3;
require __DIR__ . '/../template/_header.php';

class FLAG{
    public $public_flag = "helloctf{se3_me_";
    protected $protected_flag = "4nd_g3t_";
    private $private_flag = "mmmme}";

    function get_protected_flag(){
        return $this->protected_flag;
    }

    function get_private_flag(){
        return $this->private_flag;
    }
}

class SubFLAG extends FLAG{
    function show_protected_flag(){
        return $this->protected_flag;
    }

    function show_private_flag(){
        return $this->private_flag;
    }
}

$target = new FLAG();
$sub_target = new SubFLAG();

$code = $_POST['code'];

if(isset($code)){
    eval($code);
}

require __DIR__ . '/../template/_footer.php';
