<?php
/*
--- HelloCTF - 反序列化靶场 关卡 21 : 引用的利用 ---
序列化串中的 R 是 Reference —— 两个属性指向同一份值
© ProbiusOfficial(@hello-ctf.com) · github.com/ProbiusOfficial/PHPSerialize-labs
*/

$LEVEL_NO = 21;
require __DIR__ . '/../template/_header.php';

error_reporting(0);

class FLAG {
    public $tj_token;
    public $helloctf_token;

    public function __wakeup() {
        $this->tj_token = md5(rand(100000, 999999)); /* 反序列化时被刷新为随机值 */
    }

    public function __destruct() {
        if ($this->tj_token === $this->helloctf_token) {
            include 'flag.php';
            echo $flag;
        } else {
            echo "token mismatch: " . $this->tj_token . " vs " . $this->helloctf_token . "<br>";
        }
    }
}

unserialize($_POST['o']);

require __DIR__ . '/../template/_footer.php';
