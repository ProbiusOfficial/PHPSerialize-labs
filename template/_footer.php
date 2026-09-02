<?php
/*
PHPSerialize-labs 统一关卡模板 · 页脚
捕获关卡代码输出并渲染 源码/作答/提示/解析 面板
© ProbiusOfficial(@hello-ctf.com) · github.com/ProbiusOfficial/PHPSerialize-labs
*/

$LAB_DEMO = ob_get_clean();
$LAB_ROOT = dirname(__DIR__);
if (!isset($LAB_CONFIG)) { $LAB_CONFIG = require $LAB_ROOT . '/config/levels.php'; }
if (!isset($LEVEL_NO)) { $LEVEL_NO = 0; }
$M = isset($LAB_CONFIG['levels'][$LEVEL_NO]) ? $LAB_CONFIG['levels'][$LEVEL_NO] : null;
$LAB_TOTAL = (int)$LAB_CONFIG['site']['total'];

/* 有提交或有关卡输出 →「关卡输出」为激活页;否则展示源码 */
$LAB_HIDE_OUT = (trim($LAB_DEMO) === '' && empty($_POST));
$LAB_ACTIVE = (!empty($_POST)) ? 'out' : ($LAB_HIDE_OUT ? 'src' : 'out');

/* 当前页 URL(用于 curl 示例) */
$LAB_URL = 'http';
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') { $LAB_URL .= 's'; }
$LAB_URL .= '://' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost') . strtok(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/', '?');

/* 读取展示用源码文件 */
$LAB_SOURCES_JS = array();
if ($M && isset($M['sources'])) {
    foreach ($M['sources'] as $s) {
        $p = $LAB_ROOT . '/' . $s[1];
        $LAB_SOURCES_JS[] = array('label' => $s[0], 'code' => file_exists($p) ? file_get_contents($p) : '// 未找到:' . $s[1]);
    }
}

/* 完整题解(writeups/LevelN.md) */
$LAB_WP_MD = '';
$LAB_WP_FILE = $LAB_ROOT . '/writeups/Level' . $LEVEL_NO . '.md';
if (is_file($LAB_WP_FILE)) { $LAB_WP_MD = file_get_contents($LAB_WP_FILE); }

/* Code Runner 路径(页面所在目录的上一级) */
$LAB_DIRNAME = str_replace('\\', '/', dirname(isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '/'));
$LAB_RUNNER = ($LAB_DIRNAME === '' || $LAB_DIRNAME === '/' || $LAB_DIRNAME === '.') ? '/runner.php' : $LAB_DIRNAME . '/../runner.php';

$LAB_PAYLOAD = array(
    'page' => 'level',
    'runner' => $LAB_RUNNER,
    'wpExp' => ($M && isset($M['wp']['exp'])) ? $M['wp']['exp'] : '',
    'no' => $LEVEL_NO,
    'total' => $LAB_TOTAL,
    'activeTab' => $LAB_ACTIVE,
    'hideOut' => $LAB_HIDE_OUT,
    'keyLines' => isset($M['key_lines']) ? $M['key_lines'] : array(),
    'sources' => $LAB_SOURCES_JS,
);
?>
<?php if (trim($LAB_DEMO) !== '') { ?>
      <iframe class="lab-out-frame" srcdoc="<?php echo htmlspecialchars($LAB_DEMO, ENT_QUOTES); ?>"></iframe>
<?php } else { echo ' '; } ?>
    </div>

    <div class="panel" data-panel="src" <?php echo $LAB_ACTIVE === 'src' ? '' : 'hidden'; ?>>
      <div class="codehead">
        <span class="sp"></span>
        <button class="iconbtn" onclick="labCopy(this,'lab-raw-src')">复制当前源码</button>
      </div>
      <div id="lab-source-panel">
        <div class="subtabs" style="padding:10px 16px 0"></div>
        <textarea id="lab-raw-src" hidden></textarea>
        <div class="knotes"></div>
        <div class="code-holder"><div class="code"></div></div>
      </div>
    </div>

    <div class="panel" data-panel="ans" hidden>
      <?php if ($M && isset($M['params'])) { ?>
      <div class="ansrow">本关接收参数:
        <?php foreach ($M['params'] as $p) { ?>
          <code><?php echo htmlspecialchars($p['method']); ?> <?php echo htmlspecialchars($p['name']); ?></code>
        <?php } ?>
        <span class="sp" style="flex:1"></span>
        <button class="btn ghost" data-runner-open="1" style="padding:6px 14px">⌨ 打开运行器构造 payload</button>
      </div>
      <form method="<?php echo htmlspecialchars($M['params'][0]['method']); ?>" action="">
        <?php foreach ($M['params'] as $i => $p) { ?>
          <?php if ($i > 0 && $p['method'] !== $M['params'][0]['method']) continue; ?>
          <label style="font-size:12.5px;color:var(--ink-2);display:block;margin:8px 0 4px"><?php echo htmlspecialchars($p['name']); ?> — <?php echo htmlspecialchars($p['desc']); ?></label>
          <?php if ($p['method'] === 'GET') { ?>
          <input style="width:100%;font:13px/1.6 var(--mono);color:var(--ink);background:var(--code-bg);border:1px solid var(--line);border-radius:10px;padding:10px 12px" name="<?php echo htmlspecialchars($p['name']); ?>" placeholder="<?php echo htmlspecialchars($p['name']); ?>">
          <?php } else { ?>
          <textarea name="<?php echo htmlspecialchars($p['name']); ?>" placeholder="<?php echo htmlspecialchars($p['desc']); ?>"></textarea>
          <?php } ?>
        <?php } ?>
        <div style="display:flex;gap:10px;margin-top:10px">
          <button class="btn" type="submit">提交到关卡</button>
          <button class="btn ghost" type="reset">清空</button>
        </div>
      </form>
      <div class="toolnote">偏好命令行?等价 curl(把 VALUE 换成你的 payload):
        <div class="cmd">
          <?php
          $curl = 'curl -s' . ($M['params'][0]['method'] === 'GET' ? 'G' : 'X POST') . ' ';
          foreach ($M['params'] as $p) { $curl .= "--data-urlencode '" . $p['name'] . "=VALUE' "; }
          $curl .= "'" . $LAB_URL . "'";
          ?>
          <code><?php echo htmlspecialchars($curl); ?></code>
        </div>
        <ul>
          <li><b>HackBar</b>(浏览器插件):勾选 Post data,填 <code><?php echo htmlspecialchars($M['params'][0]['name']); ?>=...</code> 后 Execute;</li>
          <li><b>F12 控制台</b>:<code>fetch(location.href,{method:'POST',body:'<?php echo htmlspecialchars($M['params'][0]['name']); ?>='+encodeURIComponent(payload)})</code>;</li>
          <li>payload 里有不可见字符(<code>%00</code>)时务必 URL 编码。</li>
        </ul>
      </div>
      <?php } ?>
    </div>

    <div class="panel" data-panel="hint" hidden>
      <?php if ($M && isset($M['hints'])) { $isCh = ($M['mode'] === 'challenge'); ?>
        <?php foreach ($M['hints'] as $i => $h) { ?>
        <div class="hint<?php echo $isCh ? ' challenge' : ''; ?>">
          <button type="button">提示 <?php echo ($i + 1); ?> / <?php echo count($M['hints']); ?><?php echo $isCh ? ' · 挑战关提示' : ''; ?></button>
          <div class="body" hidden><?php echo $h; /* 内含 code 标签 */ ?></div>
        </div>
        <?php } ?>
      <?php } ?>
    </div>

    <div class="panel" data-panel="wp" hidden>
      <?php if ($M && isset($M['wp'])) { ?>
      <div class="gate">
        <p><b>解析包含完整答案。</b>确认你已经尝试过,或者确实卡住了?</p>
        <label><input type="checkbox"> 我已自己尝试过本关</label><br>
        <button class="btn" disabled>展开解析</button>
      </div>
      <div class="wp" hidden>
        <h4>思路</h4>
        <p><?php echo $M['wp']['idea']; ?></p>
        <?php if ($M['wp']['exp']) { ?><h4>EXP(exp.php 参考代码,可一键载入运行器)</h4><pre><?php echo htmlspecialchars($M['wp']['exp']); ?></pre>
        <button class="btn ghost" data-wp-load="1" style="margin-top:8px">⌨ 一键载入运行器</button><?php } ?>
        <?php if ($LAB_WP_MD !== '') { require $LAB_ROOT . '/template/_markdown.php'; ?>
        <h4>完整解析</h4>
        <div class="wp-md"><?php echo lab_markdown($LAB_WP_MD); ?></div>
        <?php } ?>
        <a class="more" href="../writeups/Level<?php echo $LEVEL_NO; ?>.md" target="_blank">查看完整 WriteUP ↗</a>
      </div>
      <?php } ?>
    </div>
  </div>
</div>

<div class="footer">
  <p>© 2024 Probius · <a href="https://github.com/ProbiusOfficial/PHPSerialize-labs" target="_blank">GitHub</a> · <a href="https://hello-ctf.com" target="_blank">hello-ctf.com</a> · <a href="../exerciseCollection/">进阶练习</a></p>
</div>

<script>
var LAB_PAGE = <?php echo json_encode($LAB_PAYLOAD, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
</script>
<script src="../assets/js/lab.js"></script>
</body>
</html>
