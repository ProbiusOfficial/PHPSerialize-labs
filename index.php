<?php
/*
--- HelloCTF - 反序列化靶场 · 首页 ---
© ProbiusOfficial(@hello-ctf.com) · github.com/ProbiusOfficial/PHPSerialize-labs
*/

$LAB_CONFIG = require __DIR__ . '/config/levels.php';
$LAB_SITE = $LAB_CONFIG['site'];
$LAB_LEVELS = $LAB_CONFIG['levels'];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($LAB_SITE['title']); ?></title>
<link rel="icon" href="assets/img/logo.svg">
<link rel="stylesheet" href="assets/css/lab.css">
</head>
<body>

<div class="topbar">
  <div class="wrap">
    <a class="brand" href="index.php"><img src="assets/img/logo.svg" alt="logo">HelloCTF</a>
    <span class="crumb"><?php echo htmlspecialchars($LAB_SITE['title']); ?></span>
    <span class="sp"></span>
    <button class="iconbtn" data-runner-open="1">⌨ 运行器</button>
    <a class="iconbtn" href="exerciseCollection/">进阶练习</a>
    <button class="iconbtn" id="themeBtn" title="切换明暗">◐</button>
  </div>
</div>

<div class="wrap">

  <div class="card hero">
    <h1><?php echo htmlspecialchars($LAB_SITE['title']); ?></h1>
    <p><?php echo $LAB_SITE['total']; ?> 个关卡,从类的实例化一路走到字符串逃逸、session / phar / 原生类。每一关:读源码 → 找到可控点 → 构造 payload → 拿 flag。</p>
    <div class="how">
      <div><b>① 答题方式</b>每关「作答」页有输入框,直接提交;也支持 curl / HackBar。</div>
      <div><b>② 卡住了?</b>先看「提示」(逐条展开);引导关讲得细,挑战关只给一条。</div>
      <div><b>③ 真不会?</b>「解析」页有思路和 EXP;「⌨ 运行器」帮你本地构造 payload,无需安装 PHP。</div>
    </div>
    <div class="progline">
      <span id="progText">已完成 0 / <?php echo $LAB_SITE['total']; ?></span>
      <div class="progbar"><i id="progBar"></i></div>
      <span style="color:var(--ink-3)">右键关卡卡片可标记完成</span>
    </div>
  </div>

<?php foreach ($LAB_CONFIG['chapters'] as $ch) { list($chName, $chNos) = $ch; ?>
  <div class="chapter"><h2><?php echo htmlspecialchars($chName); ?></h2><small><?php echo count($chNos); ?> 关</small></div>
  <div class="lvgrid">
  <?php foreach ($chNos as $no) { $m = $LAB_LEVELS[$no]; ?>
    <a class="card lv" href="Level<?php echo $no; ?>/" data-lab-no="<?php echo $no; ?>">
      <span class="no">L<?php echo $no; ?></span>
      <div class="t">
        <b><?php echo htmlspecialchars($m['title']); ?></b>
        <span><?php echo $m['mode'] === 'challenge' ? '挑战关 · 提示极少' : '引导关'; ?></span>
      </div>
      <div class="meta">
        <span class="stars"><?php echo str_repeat('★', $m['diff']) . str_repeat('☆', 3 - $m['diff']); ?></span>
        <span class="pill <?php echo $m['mode']; ?>"><?php echo $m['mode'] === 'challenge' ? '挑战' : '引导'; ?></span>
      </div>
    </a>
  <?php } ?>
  </div>
<?php } ?>

  <div class="chapter"><h2>进阶 · 真题实战</h2><small>练习题集</small></div>
  <div class="lvgrid">
    <a class="card lv" href="exerciseCollection/">
      <span class="no">EX</span>
      <div class="t"><b>赛事真题改编练习</b><span>天山固网 2024 等真题,纯挑战无引导</span></div>
      <span class="pill challenge">纯挑战</span>
    </a>
  </div>

  <div class="footer">
    <p>© 2024 Probius · <a href="https://github.com/ProbiusOfficial/PHPSerialize-labs" target="_blank">GitHub</a> · <a href="https://hello-ctf.com/hc-labs/" target="_blank">hello-ctf.com 配套靶场</a> · 前置:PHP 基础 / 面向对象</p>
  </div>

</div>

<script>
var LAB_PAGE = {page:"home", total:<?php echo (int)$LAB_SITE['total']; ?>};
</script>
<script src="assets/js/lab.js"></script>
</body>
</html>
