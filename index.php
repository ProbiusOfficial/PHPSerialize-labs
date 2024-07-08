<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>从 0 开始的PHP反序列化引导靶场</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">

</head>

<body>
    <nav class="navbar navbar-light navbar-expand-md py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#"><img src="assets/img/tj.svg"><span>&nbsp;探姬</span></a>
        </div>
    </nav>
    <h1>从 0 开始的PHP反序列化入门靶场</h1>
    <div class="levels">
        <a class="level-link" href="level1/index.php">第一关: 类的实例化</a>
        <a class="level-link" href="level2/index.php">第二关: 对象中值的传递</a>
        <a class="level-link" href="level3/index.php">第三关: 对象中值的权限</a>
        <a class="level-link" href="level4/index.php">第四关: 序列化初体验</a>
        <a class="level-link" href="level5/index.php">第五关: 序列化的普通值规则</a>
        <a class="level-link" href="level6/index.php">第六关: 序列化的权限修饰规则</a>
        <a class="level-link" href="level7/index.php">第七关: 实例化和反序列化</a>
        <a class="level-link" href="level8/index.php">第八关: 构造函数和析构函数以及GC机制</a>
        <a class="level-link" href="level9/index.php">第九关: 构造函数的后门</a>
        <a class="level-link" href="level10/index.php">第十关: __wakeup()</a>
        <a class="level-link" href="level11/index.php">第十一关: __wakeup() CVE-2016-7124</a>
        <a class="level-link" href="level12/index.php">第十二关: __sleep()</a>
        <a class="level-link" href="level13/index.php">第十三关: __toString()</a>
        <a class="level-link" href="level14/index.php">第十四关: __invoke()</a>
        <a class="level-link" href="level15/index.php">第十五关: POP链前置</a>
        <a class="level-link" href="level16/index.php">第十六关: POP链构造</a>
        <a class="level-link" href="level17/index.php">第十七关: 字符串逃逸基础-无中生有</a>
    </div>
    <div class="footer">
        <p>© 2024 Probius | <a href="https://github.com/ProbiusOfficial/PHPSerialize-labs">GitHub</a></p>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>