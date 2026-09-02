<?php
/*
--- HelloCTF - 反序列化靶场 关卡 6 : 序列化的权限修饰规则 ---
© ProbiusOfficial(@hello-ctf.com) · github.com/ProbiusOfficial/PHPSerialize-labs
*/

$LEVEL_NO = 6;
require __DIR__ . '/../template/_header.php';

$flag = "helloctf{P3rm1ssi0n_Modif_1s_1mp0rtant}";

class protectedKEY{
    protected $protected_key;

    function get_key(){
        return $this->protected_key;
    }
}

class privateKEY{
    private $private_key;

    function get_key(){
        return $this->private_key;
    }
}

echo "protected's serialize: ".urlencode(serialize(new protectedKEY()))."<br>";
echo "private's serialize: ".urlencode(serialize(new privateKEY()))."<br>";

$protected_key = unserialize($_POST['protected_key']);
$private_key = unserialize($_POST['private_key']);

if(isset($_POST['protected_key'])&&isset($_POST['private_key'])){
    if($protected_key->get_key() == "protected_key" && $private_key->get_key() == "private_key"){
        echo $flag;
    } else {
        echo "We Call it %00_Contr0l_Characters_NULL!";
    }
}

require __DIR__ . '/../template/_footer.php';
