<?php
/*
--- HelloCTF - 反序列化靶场 关卡 26 : 魔术方法跳板 __get / __call ---
© ProbiusOfficial(@hello-ctf.com) · github.com/ProbiusOfficial/PHPSerialize-labs
*/

$LEVEL_NO = 26;
require __DIR__ . '/../template/_header.php';

error_reporting(0);

class TJ_TRIGGER {
    public $helloctf_obj;

    public function __get($name) {
        $this->helloctf_obj->run();   // 调用一个不存在的方法
    }
}

class HELLOCTF_CALL {
    public $tj_fn;
    public $probiusofficial_arg;

    public function __call($name, $args) {
        call_user_func($this->tj_fn, $this->probiusofficial_arg);
    }
}

if (isset($_POST['o'])) {
    $obj = unserialize($_POST['o']);
    $noop = $obj->tj_anything;   // 读取一个不存在的属性
}

require __DIR__ . '/../template/_footer.php';
