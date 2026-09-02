<?php
/*
--- HelloCTF - 反序列化靶场 关卡 7 : 实例化和反序列化 ---
© ProbiusOfficial(@hello-ctf.com) · github.com/ProbiusOfficial/PHPSerialize-labs
*/

$LEVEL_NO = 7;
require __DIR__ . '/../template/_header.php';

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
    echo "'\$Instantiate_object->backdoor()' will output:";
    $Instantiate_object->backdoor();

    echo "'\$Unserialize_object->backdoor()' will output:";
    $Unserialize_object->backdoor();
}

require __DIR__ . '/../template/_footer.php';
