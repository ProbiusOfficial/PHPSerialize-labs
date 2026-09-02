<?php
ob_start(); /* 顶层缓冲:保证 session_start 等仍可发送头部 */
/*
PHPSerialize-labs 统一关卡模板 · 页头
© ProbiusOfficial(@hello-ctf.com) · github.com/ProbiusOfficial/PHPSerialize-labs
用法:关卡文件中 $LEVEL_NO = N; require __DIR__.'/../template/_header.php';
*/

if (!defined('LAB_TEMPLATE')) {
    $LAB_ROOT = dirname(__DIR__);
    $LAB_CONFIG = require $LAB_ROOT . '/config/levels.php';
    $LEVEL_NO = isset($LEVEL_NO) ? (int)$LEVEL_NO : 0;
    $M = isset($LAB_CONFIG['levels'][$LEVEL_NO]) ? $LAB_CONFIG['levels'][$LEVEL_NO] : null;
    $LAB_TOTAL = (int)$LAB_CONFIG['site']['total'];

    /* 计算相邻关卡(按章节顺序) */
    $LAB_ORDER = array();
    foreach ($LAB_CONFIG['chapters'] as $ch) { foreach ($ch[1] as $no) { $LAB_ORDER[] = $no; } }
    $LAB_POS = array_search($LEVEL_NO, $LAB_ORDER);
    $LAB_PREV = ($LAB_POS !== false && $LAB_POS > 0) ? $LAB_ORDER[$LAB_POS - 1] : 0;
    $LAB_NEXT = ($LAB_POS !== false && $LAB_POS < count($LAB_ORDER) - 1) ? $LAB_ORDER[$LAB_POS + 1] : 0;

    $LAB_TITLE = $M ? ('Level ' . $LEVEL_NO . ' · ' . $M['title']) : 'PHPSerialize-labs';
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($LAB_TITLE); ?> - 从 0 开始的PHP反序列化引导靶场</title>
<link rel="icon" href="../assets/img/logo.svg">
<link rel="stylesheet" href="../assets/css/lab.css">
</head>
<body>

<div class="topbar">
  <div class="wrap">
    <a class="brand" href="../index.php"><img src="../assets/img/logo.svg" alt="logo">Serialize-labs</a>
    <span class="crumb"><b><?php echo 'Level ' . $LEVEL_NO . ' / ' . $LAB_TOTAL; ?></b> · <?php echo htmlspecialchars($M ? $M['title'] : ''); ?></span>
    <span class="sp"></span>
    <button class="iconbtn" data-runner-open="1">⌨ 运行器</button>
    <?php if ($LAB_PREV) { ?><a class="iconbtn" href="../Level<?php echo $LAB_PREV; ?>/">← Level <?php echo $LAB_PREV; ?></a><?php } ?>
    <a class="iconbtn" href="../index.php">目录</a>
    <?php if ($LAB_NEXT) { ?><a class="iconbtn" href="../Level<?php echo $LAB_NEXT; ?>/">Level <?php echo $LAB_NEXT; ?> →</a><?php } ?>
    <button class="iconbtn" id="themeBtn" title="切换明暗">◐</button>
  </div>
</div>

<div class="wrap">

<?php if ($M) { ?>
  <div class="lhead">
    <h1>Level <?php echo $LEVEL_NO; ?> · <?php echo htmlspecialchars($M['title']); ?>
      <span class="pill <?php echo $M['mode']; ?>"><?php echo $M['mode'] === 'challenge' ? '挑战' : '引导'; ?></span>
      <span class="stars"><?php echo str_repeat('★', $M['diff']) . str_repeat('☆', 3 - $M['diff']); ?></span>
      <?php foreach ($M['tags'] as $t) { ?><span class="pill tag"><?php echo htmlspecialchars($t); ?></span><?php } ?>
    </h1>
    <div class="goal">目标:<?php echo $M['goal']; /* 内含 code 标签,不做转义 */ ?></div>
  </div>

  <?php if ($M['mode'] === 'challenge') { ?>
  <div class="card know ch-mode">
    <h3>挑战关</h3>
    <p>本关几乎不提供讲解。唯一的提示在「提示」页,穷尽思路后再看;再不行,「解析」见。</p>
  </div>
  <?php } else if ($M['know']) { ?>
  <div class="card know">
    <h3>本关知识点</h3>
    <p><?php echo $M['know']; /* 内含 code 标签,不做转义 */ ?></p>
  </div>
  <?php } ?>
<?php } ?>

  <div class="card">
    <div class="tabs" id="lab-tabs">
      <button data-tab="out" class="on">关卡输出</button>
      <button data-tab="src">题目源码</button>
      <button data-tab="ans">作答</button>
      <button data-tab="hint">提示<span class="badge" id="lab-hint-count">0</span></button>
      <button data-tab="wp">解析</button>
    </div>
    <div class="panel" data-panel="out">
<?php
ob_start(); /* 从这里开始,关卡代码的所有 echo 进入「关卡输出」面板(以 iframe 隔离渲染) */
