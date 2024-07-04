<?php

/*
--- HelloCTF - 反序列化靶场 关卡 7 : 实例化和反序列化 --- 

HINT：可控的输入 简单的漏洞演示

# -*- coding: utf-8 -*-
# @Author: 探姬(@ProbiusOfficial)
# @Date:   2024-07-01 20:30
# @Repo:   github.com/ProbiusOfficial/PHPSerialize-labs
# @email:  admin@hello-ctf.com
# @link:   hello-ctf.com

*/

class FLAG{
    public $flag_command = "echo 'Hello CTF!<br>';";

    function backdoor(){
        eval($this->flag_command);
    }
}

$unserialize_string = 'O:4:"FLAG":1:{s:12:"flag_command";s:24:"echo \'Hello World!<br>\';";}'; // 注意看这里，与预定的命令不同

$Instantiate_object = new FLAG(); // 实例化的对象

$Unserialize_object = unserialize($unserialize_string); // 反序列化的对象






if(isset($_POST['o']))
{
     unserialize($_POST['o'])->backdoor();
}
else {
    highlight_file(demo);

    echo "<br>'\$Instantiate_object->backdoor()' will output:";
    $Instantiate_object->backdoor();

    echo "'\$Unserialize_object->backdoor()' will output:";
    $Unserialize_object->backdoor();

    highlight_file(source);
}



