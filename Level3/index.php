<?php

/*
--- HelloCTF - 反序列化靶场 关卡 3 : 对象中值的权限 --- 

HINT：尝试将flag传递出来~

# -*- coding: utf-8 -*-
# @Author: 探姬
# @Date:   2024-07-01 20:30
# @Repo:   github.com/ProbiusOfficial/PHPSerialize-labs
# @email:  admin@hello-ctf.com
# @link:   hello-ctf.com

*/

class FLAG{
    public $public_flag = "HelloCTF{se3_me_";
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
} else {
    highlight_file(source);
    echo "Trying to get FLAG...<br>";
    echo "Public Flag: ".$target->public_flag."<br>";

    echo "Protected Flag: Error: Cannot access protected property FLAG::$protected_flag in ? <br>";
    echo "Private Flag: Error: Cannot access private property FLAG::$private_flag in ? <br>";

    echo "...Wait,where is the flag? <br>";
}
?>
