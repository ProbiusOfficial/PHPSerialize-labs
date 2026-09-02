<?php
/*
--- HelloCTF - 反序列化靶场 关卡 12 : __sleep() ---
serialize() 会检查类中是否存在 __sleep(),存在则先调用再执行序列化。
__sleep() 必须返回数组,数组元素决定哪些属性将被序列化;
返回父类私有属性需使用 "\0类名\0属性名" 格式;未返回内容则序列化为 NULL 并产生 E_NOTICE。
© ProbiusOfficial(@hello-ctf.com) · github.com/ProbiusOfficial/PHPSerialize-labs
*/

$LEVEL_NO = 12;
require __DIR__ . '/../template/_header.php';

class FLAG {

    private $f = 'clean_';
    private $l = 'up_';
    protected $a = '4nd_';
    public  $g = 'select_variab1es}';
    public $x,$y,$z;

    public function __sleep() {
        echo "If you serialize FLAG, you will just get x,y,z<br>";
        return ['x','y','z'];
    }
}

class CHALLENGE extends FLAG {

    public $h = 'helloctf{',$e = 'Th3_',$l = '__sleep_function_',$I = '_is_',$o = 'called_',$c = 'before_',$t = 'serialization_',$f = 't0_';
    public $chance;

    function chance() {
        if(isset($_GET['chance'])){
            return $_GET['chance'];
        }
        else{
            return 'you shuold use it';
        }
    }
    public function __sleep() {

        $array_list = ['h','e','l','I','o','c','t','f','f','l','a','g'];
        $_=array_rand($array_list);$__=array_rand($array_list);
        echo "Now __sleep()'s return parameters is array('".$array_list[$_]."','".$array_list[$__]."','".$this->chance()."')<br>";
        return array($array_list[$_],$array_list[$__],$this->chance());
    }

}

/* FLAG is $h + $e + $l + $I + $o + $c + $t + $f + $f + $l + $a + $g */

$FLAG = new FLAG();
echo serialize($FLAG);

echo "<br>------ 每次请求会随机返回两个属性，你也可以用 chance 来指定你想要的属性 ------<br>";

echo serialize(new CHALLENGE());

require __DIR__ . '/../template/_footer.php';
