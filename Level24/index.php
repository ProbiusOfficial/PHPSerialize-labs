<?php
/*
--- HelloCTF - 反序列化靶场 关卡 24 : 原生类利用 ---
没有 eval / system 的题,原生类(SplFileObject / DirectoryIterator)就是你的武器
© ProbiusOfficial(@hello-ctf.com) · github.com/ProbiusOfficial/PHPSerialize-labs
*/

$LEVEL_NO = 24;
require __DIR__ . '/../template/_header.php';

error_reporting(0);

class TJ_ITER {
    public $probiusofficial_path;
    public function __toString() {
        $out = '';
        foreach (new DirectoryIterator($this->probiusofficial_path) as $f) {
            $out .= $f->getFilename() . "\n";
        }
        return '<pre>' . htmlspecialchars($out) . '</pre>';
    }
}

class HELLOCTF_READER {
    public $tj_file;
    public function __toString() {
        return implode('<br>', iterator_to_array(new SplFileObject($this->tj_file)));
    }
}

if (isset($_POST['o'])) {
    echo unserialize($_POST['o']); /* echo 触发 __toString */
}

require __DIR__ . '/../template/_footer.php';
