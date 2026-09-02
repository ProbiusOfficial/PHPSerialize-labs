<?php
/*
--- HelloCTF - 反序列化靶场 · 练习题集 ---
© ProbiusOfficial(@hello-ctf.com) · github.com/ProbiusOfficial/PHPSerialize-labs
*/

$EXERCISES = [
    ['天山固网2024-字符串逃逸', '字符串逃逸实战:call_user_func 无过滤 + 字符串变短逃逸', 'challenge', 3],
];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>进阶练习 - PHPSerialize-labs</title>
<link rel="icon" href="../assets/img/logo.svg">
<link rel="stylesheet" href="../assets/css/lab.css">
</head>
<body>

<div class="topbar">
  <div class="wrap">
    <a class="brand" href="../index.php"><img src="../assets/img/logo.svg" alt="logo">Serialize-labs</a>
    <span class="crumb">进阶练习 · 真题实战</span>
    <span class="sp"></span>
    <button class="iconbtn" data-runner-open="1">⌨ 运行器</button>
    <a class="iconbtn" href="../index.php">目录</a>
    <button class="iconbtn" id="themeBtn" title="切换明暗">◐</button>
  </div>
</div>

<div class="wrap">
  <div class="card hero">
    <h1>进阶练习 · 真题实战</h1>
    <p>学完 24 关后的练兵场:收录赛事真题改编,纯挑战模式,无引导无提示,和真实比赛一个手感。解题思路与 EXP 在各题目录的 readme.md。</p>
  </div>

  <div class="lvgrid">
  <?php foreach ($EXERCISES as $ex) { list($name, $desc, $mode, $diff) = $ex; ?>
    <a class="card lv" href="<?php echo rawurlencode($name); ?>/">
      <span class="no">EX</span>
      <div class="t"><b><?php echo htmlspecialchars($name); ?></b><span><?php echo htmlspecialchars($desc); ?></span></div>
      <div class="meta">
        <span class="stars"><?php echo str_repeat('★', $diff) . str_repeat('☆', 3 - $diff); ?></span>
        <span class="pill <?php echo $mode; ?>">纯挑战</span>
      </div>
    </a>
  <?php } ?>
  </div>

  <div class="footer">
    <p>© 2024 Probius · <a href="https://github.com/ProbiusOfficial/PHPSerialize-labs" target="_blank">GitHub</a> · <a href="https://hello-ctf.com/hc-labs/" target="_blank">hello-ctf.com</a></p>
  </div>
</div>

<script>
var LAB_PAGE = {page:"home", total:1, runner:"../runner.php"};
</script>
<script src="../assets/js/lab.js"></script>
</body>
</html>
