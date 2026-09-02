/* ============================================================
   PHPSerialize-labs 前端交互 + 内置 PHP Code Runner
   © ProbiusOfficial / hello-ctf.com
   依赖:window.LAB_PAGE(由 template/_footer.php 注入)
   ============================================================ */
(function () {
  "use strict";

  /* ───────── 主题 ───────── */
  var root = document.documentElement;
  var savedTheme = localStorage.getItem("phplab-theme");
  if (savedTheme) root.setAttribute("data-theme", savedTheme);
  else if (window.matchMedia && matchMedia("(prefers-color-scheme: dark)").matches)
    root.setAttribute("data-theme", "dark");

  /* ───────── PHP 语法高亮(逐行,含块注释状态) ───────── */
  var KW = /^(abstract|and|array|as|break|case|catch|class|const|continue|die|do|echo|else|elseif|empty|exit|extends|final|finally|for|foreach|function|global|if|implements|include|include_once|instanceof|isset|list|namespace|new|or|print|private|protected|public|require|require_once|return|static|switch|throw|try|unset|use|var|while|xor|true|false|null|TRUE|FALSE|NULL)$/i;
  var MAGIC = /^__(construct|destruct|wakeup|sleep|toString|invoke|call|get|set|isset|unset|clone|autoload)$/i;

  function esc(s) { return s.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;"); }

  function tokenize(src) {
    var lines = src.replace(/\n$/, "").split("\n"), out = [], inBlock = false;
    lines.forEach(function (line) {
      var html = "", i = 0, n = line.length, buf = "";
      function flush() { if (buf) { html += esc(buf); buf = ""; } }
      while (i < n) {
        if (inBlock) {
          var e = line.indexOf("*/", i);
          if (e < 0) { html += '<span class="tk-c">' + esc(line.slice(i)) + "</span>"; i = n; }
          else { html += '<span class="tk-c">' + esc(line.slice(i, e + 2)) + "</span>"; i = e + 2; inBlock = false; }
          continue;
        }
        var c = line[i];
        if (c === "/" && line[i + 1] === "*") {
          flush(); var e2 = line.indexOf("*/", i + 2);
          if (e2 < 0) { html += '<span class="tk-c">' + esc(line.slice(i)) + "</span>"; i = n; inBlock = true; }
          else { html += '<span class="tk-c">' + esc(line.slice(i, e2 + 2)) + "</span>"; i = e2 + 2; }
          continue;
        }
        if (c === "/" && line[i + 1] === "/") { flush(); html += '<span class="tk-c">' + esc(line.slice(i)) + "</span>"; i = n; continue; }
        if (c === "#") { flush(); html += '<span class="tk-c">' + esc(line.slice(i)) + "</span>"; i = n; continue; }
        if (c === '"' || c === "'") {
          flush(); var q = c, j = i + 1;
          while (j < n) { if (line[j] === "\\") { j += 2; continue; } if (line[j] === q) break; j++; }
          html += '<span class="tk-s">' + esc(line.slice(i, Math.min(j + 1, n))) + "</span>";
          i = Math.min(j + 1, n); continue;
        }
        if (c === "$") {
          flush(); var j2 = i + 1; while (j2 < n && /\w/.test(line[j2])) j2++;
          html += '<span class="tk-v">' + esc(line.slice(i, j2)) + "</span>"; i = j2; continue;
        }
        if (/\w/.test(c)) {
          flush(); var j3 = i; while (j3 < n && /[\w]/.test(line[j3])) j3++;
          var w = line.slice(i, j3);
          if (MAGIC.test(w)) html += '<span class="tk-m">' + esc(w) + "</span>";
          else if (KW.test(w)) html += '<span class="tk-k">' + esc(w) + "</span>";
          else if (/^\d+$/.test(w)) html += '<span class="tk-n">' + esc(w) + "</span>";
          else buf += w;
          i = j3; continue;
        }
        buf += c; i++;
      }
      flush(); out.push(html);
    });
    return out;
  }

  function renderCode(container, code, keyLines) {
    var toks = tokenize(code.replace(/^\s*\n/, ""));
    var keyMap = {};
    (keyLines || []).forEach(function (k) { for (var i = k.from; i <= k.to; i++) keyMap[i] = k.label; });
    var html = "", lastKey = null;
    toks.forEach(function (t, idx) {
      var no = idx + 1, cls = keyMap[no] ? "key" : "";
      html += '<div class="cl ' + cls + '"><span class="ln">' + no + '</span><span class="cd">' + (t || " ") + "</span></div>";
      if (keyMap[no] && keyMap[no] !== lastKey) {
        html += '<div class="knote"><i></i><span>' + esc(keyMap[no]) + "</span></div>";
        lastKey = keyMap[no];
      }
      if (!keyMap[no]) lastKey = null;
    });
    container.innerHTML = html;
  }

  /* ───────── Tab 切换(页面多个 .tabs 通用) ───────── */
  document.addEventListener("click", function (e) {
    var b = e.target.closest(".tabs > button");
    if (!b) return;
    var tabs = b.parentElement, box = tabs.parentElement;
    tabs.querySelectorAll("button").forEach(function (x) { x.classList.toggle("on", x === b); });
    box.querySelectorAll(":scope > .panel").forEach(function (p) {
      p.hidden = p.getAttribute("data-panel") !== b.getAttribute("data-tab");
    });
  });

  /* ───────── 提示渐进展开 ───────── */
  document.addEventListener("click", function (e) {
    var b = e.target.closest(".hint > button");
    if (b) b.nextElementSibling.hidden = !b.nextElementSibling.hidden;
  });

  /* ───────── 解析剧透门 ───────── */
  document.addEventListener("change", function (e) {
    if (!e.target.matches(".gate input[type=checkbox]")) return;
    var gate = e.target.closest(".gate");
    gate.querySelector(".btn").disabled = !e.target.checked;
  });
  document.addEventListener("click", function (e) {
    var b = e.target.closest(".gate .btn");
    if (!b || b.disabled) return;
    b.closest(".panel").querySelector(".gate").hidden = true;
    b.closest(".panel").querySelector(".wp").hidden = false;
  });

  /* ───────── 复制按钮 ───────── */
  window.labCopy = function (btn, id) {
    var el = document.getElementById(id);
    if (!el) return;
    var t = el.value !== undefined ? el.value : el.textContent;
    (navigator.clipboard ? navigator.clipboard.writeText(t) : Promise.reject())
      .then(function () { btn.textContent = "已复制"; setTimeout(function () { btn.textContent = "复制"; }, 1500); },
            function () { btn.textContent = "复制失败"; });
  };

  /* ───────── 源码渲染(LAB_PAGE.sources) ───────── */
  var PAGE = window.LAB_PAGE || {};
  function initSources() {
    var box = document.getElementById("lab-source-panel");
    if (!box || !PAGE.sources || !PAGE.sources.length) return;
    var sub = box.querySelector(".subtabs"), holder = box.querySelector(".code-holder"), kn = box.querySelector(".knotes");
    function show(i) {
      sub.querySelectorAll("button").forEach(function (x, j) { x.classList.toggle("on", j === i); });
      renderCode(holder.querySelector(".code"), PAGE.sources[i].code, PAGE.keyLines);
      var raw = document.getElementById("lab-raw-src");
      if (raw) raw.value = PAGE.sources[i].code;
      if (kn) kn.innerHTML = "";
    }
    PAGE.sources.forEach(function (s, i) {
      var b = document.createElement("button");
      b.textContent = s.label;
      b.onclick = function () { show(i); };
      sub.appendChild(b);
    });
    show(0);
  }

  /* ───────── 目录页进度 ───────── */
  function getDone() { try { return JSON.parse(localStorage.getItem("phplab-done") || "[]"); } catch (e) { return []; } }
  function setDone(a) { localStorage.setItem("phplab-done", JSON.stringify(a)); }
  function initHomeProgress() {
    var cards = document.querySelectorAll("[data-lab-no]");
    if (!cards.length) return;
    var done = getDone();
    function paint() {
      cards.forEach(function (c) {
        var no = +c.getAttribute("data-lab-no");
        var mark = c.querySelector(".done");
        if (done.indexOf(no) >= 0) { if (!mark) { mark = document.createElement("span"); mark.className = "done"; mark.textContent = "✔"; c.appendChild(mark); } }
        else if (mark) mark.remove();
      });
      var bar = document.getElementById("progBar"), txt = document.getElementById("progText"), total = +("" + (PAGE.total || 18));
      if (bar) bar.style.width = (done.length / total * 100) + "%";
      if (txt) txt.textContent = "已完成 " + done.length + " / " + total;
    }
    cards.forEach(function (c) {
      c.addEventListener("contextmenu", function (e) {
        e.preventDefault();
        var no = +c.getAttribute("data-lab-no"), d = getDone(), i = d.indexOf(no);
        if (i >= 0) d.splice(i, 1); else if (d.length < (PAGE.total || 18)) d.push(no);
        setDone(d); paint();
      });
    });
    paint();
  }

  /* ============================================================
     PHP Code Runner:完整 PHP 代码,POST 到 runner.php 真实执行
     ============================================================ */
  var RUNNER_SKELETON = [
    '<?php',
    '/**',
    ' * PHP Code Runner —— 在这里写完整的 PHP 代码,点「运行」查看输出',
    ' * 生成的 payload 可直接复制到「作答」页提交',
    ' */',
    '',
    'class FLAG {',
    '    public $cmd = "system(\'ls /\');";',
    '}',
    '',
    'echo serialize(new FLAG()), "\n";',
    '',
    '// 需要 URL 编码时:',
    '// echo urlencode(serialize(new FLAG())), "\n";',
    ''
  ].join("\n");

  var RUNNER_HTML =
    '<div class="card runner" id="labRunner">' +
    '  <header><b>PHP Code Runner</b><span style="font-size:12px;color:var(--ink-3)">完整的 PHP 运行环境(与靶场同版本)</span><span class="sp"></span>' +
    '    <button class="iconbtn" data-runner-sample="1">示例</button><button class="iconbtn" data-runner-close="1">✕</button></header>' +
    '  <div class="runner-pad">' +
    '    <textarea id="runnerIn" spellcheck="false"></textarea>' +
    '    <div class="row"><button class="btn" data-runner-run="1">▶ 运行</button>' +
    '    <button class="btn ghost" data-runner-copy="1">复制输出</button></div>' +
    '    <pre class="out" id="runnerOut">— 点击「运行」查看输出 —</pre>' +
    '  </div>' +
    '</div>' +
    '<div class="runner-mask" id="runnerMask"></div>';

  function initRunner() {
    document.body.insertAdjacentHTML("beforeend", RUNNER_HTML);
    document.getElementById("runnerIn").value = RUNNER_SKELETON;

    document.addEventListener("click", function (e) {
      if (e.target.closest("[data-runner-open]")) {
        document.getElementById("labRunner").classList.add("open");
        document.getElementById("runnerMask").classList.add("open");
        return;
      }
      if (e.target.closest("[data-runner-close]") || e.target.id === "runnerMask") {
        document.getElementById("labRunner").classList.remove("open");
        document.getElementById("runnerMask").classList.remove("open");
        return;
      }
      if (e.target.closest("[data-runner-sample]")) {
        document.getElementById("runnerIn").value = RUNNER_SKELETON;
        runRunner();
        return;
      }
      if (e.target.closest("[data-runner-run]")) { runRunner(); return; }
      if (e.target.closest("[data-wp-load]")) {
        /* 一键载入题解 exp.php 参考代码 */
        document.getElementById("runnerIn").value = (PAGE.wpExp || "").replace(/^\s*\n/, "");
        document.getElementById("labRunner").classList.add("open");
        document.getElementById("runnerMask").classList.add("open");
        return;
      }
      if (e.target.closest("[data-runner-run-x]")) { runRunner(); return; }
      if (e.target.closest("[data-runner-copy]")) {
        var out = document.getElementById("runnerOut").textContent;
        (navigator.clipboard ? navigator.clipboard.writeText(out) : Promise.reject())
          .then(function () { e.target.textContent = "已复制"; setTimeout(function () { e.target.textContent = "复制输出"; }, 1500); });
      }
    });

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") {
        document.getElementById("labRunner").classList.remove("open");
        document.getElementById("runnerMask").classList.remove("open");
      }
      if ((e.ctrlKey || e.metaKey) && e.key === "Enter" && document.getElementById("labRunner").classList.contains("open")) runRunner();
    });

    function runRunner() {
      var out = document.getElementById("runnerOut");
      out.textContent = "运行中…";
      var code = document.getElementById("runnerIn").value;
      var body = new URLSearchParams();
      body.append("code", code);
      fetch(PAGE.runner || "runner.php", { method: "POST", body: body })
        .then(function (r) { return r.text(); })
        .then(function (t) { out.textContent = t === "" ? "(无输出)" : t; })
        .catch(function (err) { out.textContent = "运行失败:" + err.message; });
    }
    window.labRunnerRun = runRunner;
  }

  /* ───────── 启动 ───────── */
  document.addEventListener("DOMContentLoaded", function () {
    initSources();
    initHomeProgress();
    initRunner();

    /* 关卡页:提示计数 / 隐藏空的「关卡输出」/ 激活目标 Tab */
    if (PAGE.page === "level") {
      var badge = document.getElementById("lab-hint-count");
      var hintCount = document.querySelectorAll(".hint").length;
      if (badge) { badge.textContent = hintCount; if (!hintCount) badge.style.display = "none"; }
      if (PAGE.hideOut) {
        var ob = document.querySelector('#lab-tabs button[data-tab="out"]');
        var op = document.querySelector('.panel[data-panel="out"]');
        if (ob) ob.style.display = "none";
        if (op) op.hidden = true;
      }
      if (PAGE.activeTab) {
        var tb = document.querySelector('#lab-tabs button[data-tab="' + PAGE.activeTab + '"]');
        if (tb && !tb.classList.contains("on")) tb.click();
      }
    }

    /* 关卡输出 iframe 高度自适应(phpinfo 等长内容) */
    document.querySelectorAll(".lab-out-frame").forEach(function (f) {
      var fit = function () {
        try {
          var d = f.contentDocument;
          if (d && d.body) f.style.height = (d.body.scrollHeight + 24) + "px";
        } catch (err) { f.style.height = "320px"; }
      };
      f.addEventListener("load", fit);
      setTimeout(fit, 60);
    });

    var tb = document.getElementById("themeBtn");
    if (tb) tb.onclick = function () {
      var next = root.getAttribute("data-theme") === "dark" ? "light" : "dark";
      root.setAttribute("data-theme", next);
      localStorage.setItem("phplab-theme", next);
    };
  });
})();
