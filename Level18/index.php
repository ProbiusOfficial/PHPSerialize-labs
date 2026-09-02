<?php
/*
--- HelloCTF - 反序列化靶场 关卡 18 : 字符串逃逸基础·尾部判定 ---
规则特性:当成员属性的数量、名称长度、内容长度均一致时,程序以 ";}" 作为字符串结尾判定
© ProbiusOfficial(@hello-ctf.com) · github.com/ProbiusOfficial/PHPSerialize-labs
*/

$LEVEL_NO = 18;
require __DIR__ . '/../template/_header.php';

class Demo {
    public $a = "Hello";
    public $b = "CTF";
    public $key = 'GET_FLAG";}FAKE_FLAG';
}

class FLAG {

}

$serliseStringDemo = serialize(new Demo());
echo "SerliseStringDemo:'".$serliseStringDemo."'<br>";

echo "Change SOMETHING TO GET FLAG";

$target = isset($_GET['target']) ? $_GET['target'] : '';
$change = isset($_GET['change']) ? $_GET['change'] : '';

$serliseStringFLAG = str_replace($target, $change, $serliseStringDemo);

$FLAG = unserialize($serliseStringFLAG);

if ($FLAG instanceof FLAG && $FLAG->key == 'GET_FLAG') {
    include 'flag.php';
    echo $flag;
} else {
    echo "Your serliaze string is ".$serliseStringFLAG . "<br> And Here is ";
    var_dump($FLAG);
}

require __DIR__ . '/../template/_footer.php';
