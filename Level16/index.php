<?php
/*
--- HelloCTF - 反序列化靶场 关卡 16 : POP 链构造 ---
__wakeup() 反序列化时自动调用 / __invoke() 对象被当成函数时调用 / __toString() 对象被当成字符串时调用
试着把他们串起来吧ww
© ProbiusOfficial(@hello-ctf.com) · github.com/ProbiusOfficial/PHPSerialize-labs
*/

$LEVEL_NO = 16;
require __DIR__ . '/../template/_header.php';

class A {
    public $a;
    public function __invoke() {
            include $this->a;
            return $flag;
    }
}

class B {
    public $b;
    public function __toString() {
        $f = $this->b;
        return $f();
    }
}


class INIT {
    public $name;
    public function __wakeUp() {
        echo $this->name.' is awake!';
    }
}

if(isset($_POST['o'])) {
    unserialize($_POST['o']);
}

require __DIR__ . '/../template/_footer.php';
