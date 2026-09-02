<?php
/*
--- HelloCTF - 反序列化靶场 关卡 5 : 序列化的普通值规则 ---
© ProbiusOfficial(@hello-ctf.com) · github.com/ProbiusOfficial/PHPSerialize-labs
*/

$LEVEL_NO = 5;
require __DIR__ . '/../template/_header.php';

class a_class{
    public $a_value = "HelloCTF";
}
$a_array = array('a'=>"Hello",'b'=>"CTF");
$a_string = "HelloCTF";
$a_number = 678470;
$a_boolean = true;
$a_null = null;

echo "See How to serialize:<br>";
echo "a_object: ".serialize(new a_class())."<br>";
echo "a_array: ".serialize($a_array)."<br>";
echo "a_string: ".serialize($a_string)."<br>";
echo "a_number: ".serialize($a_number)."<br>";
echo "a_boolean: ".serialize($a_boolean)."<br>";
echo "a_null: ".serialize($a_null)."<br>";
echo "Now your turn!<br>";

$your_object = $_POST['o'];
$your_string = $_POST['s'];
$your_array = $_POST['a'];
$your_number = $_POST['i'];
$your_boolean = $_POST['b'];
$your_NULL = $_POST['n'];

$your_object = unserialize($your_object);
$your_array = unserialize($your_array);
$your_string = unserialize($your_string);
$your_number = unserialize($your_number);
$your_boolean = unserialize($your_boolean);
$your_NULL = unserialize($your_NULL);

$flag = "helloctf{Gre4t,y0u_can_als0_ser4l1ze2se_1n_y0ur_m1nd!}";

if(
    $your_boolean &&
    $your_NULL == null &&
    $your_string == "IWANT" &&
    $your_number == 1 &&
    $your_object->a_value == "FLAG" &&
    $your_array['a'] == "Plz" && $your_array['b'] == "Give_M3"
){
    echo $flag;
}
else{
    echo "You really know how to serialize?";
}

require __DIR__ . '/../template/_footer.php';
