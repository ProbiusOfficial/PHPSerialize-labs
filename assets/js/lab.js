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
     PHP Code Runner(纯前端)
     支持:类定义解析 → serialize(含 public/protected/private 规则)
           unserialize 反解 → 结构 dump
     ============================================================ */
  var RUNNER_SAMPLE = 'class FLAG {\n    public $cmd = "system(\'ls /\');";\n    protected $hidden = "secret";\n    private $note = 123;\n    public $tags = array("a", "b");\n}';
  var RUNNER_HTML =
    '<div class="card runner" id="labRunner">' +
    '  <header><b>⌨ PHP 序列化运行器</b><span style="font-size:12px;color:var(--ink-3)">本地构造 payload,无需安装 PHP</span><span class="sp"></span>' +
    '    <button class="iconbtn" data-runner-sample="1">示例</button><button class="iconbtn" data-runner-close="1">✕</button></header>' +
    '  <div class="grid">' +
    '    <div><label>PHP 类定义(支持 public / protected / private、字符串、数字、布尔、NULL、array)</label>' +
    '      <textarea id="runnerIn" spellcheck="false"></textarea>' +
    '      <div class="row"><button class="btn" data-runner-run="1">生成 serialize()</button>' +
    '      <button class="btn ghost" data-runner-enc="1">URL 编码开关:关</button></div></div>' +
    '    <div><label>输出</label><div class="out" id="runnerOut">—</div>' +
    '      <div class="row"><button class="btn ghost" id="runnerCopy">复制结果</button></div>' +
    '      <label>反解检查:粘贴序列化串 → 查看还原结构</label>' +
    '      <textarea id="runnerBack" style="min-height:80px" spellcheck="false" placeholder=\'O:4:"FLAG":1:{...}\'></textarea>' +
    '      <div class="row"><button class="btn ghost" data-runner-back="1">unserialize 检查</button></div>' +
    '      <div class="out" id="runnerBackOut" style="min-height:40px">—</div></div>' +
    "  </div>" +
    '  <div class="cheat"><b>类型速查</b>:<code>s:字节长度:"字符串";</code> · <code>i:整数;</code> · <code>d:浮点;</code> · <code>b:0/1;</code> · <code>N;</code>(NULL) · ' +
    "<code>a:个数:{键;值;...}</code> · <code>O:类名长度:\"类名\":属性数:{...}</code><br>" +
    "权限规则:<code>public → 变量名</code> · <code>protected → %00*%00变量名</code> · <code>private → %00类名%00变量名</code>(%00 为空字节,提交时需 URL 编码)</div>" +
    "</div>" +
    '<div class="runner-mask" id="runnerMask"></div>';

  /* —— PHP 值解析 —— */
  function parsePhpString(raw) {
    var q = raw[0], body = raw.slice(1, -1), out = "", i = 0;
    while (i < body.length) {
      var c = body[i];
      if (c === "\\" && i + 1 < body.length) {
        var nx = body[i + 1];
        if (nx === "\\") out += "\\";
        else if (nx === q) out += q;
        else if (q === '"') {
          if (nx === "n") out += "\n";
          else if (nx === "t") out += "\t";
          else if (nx === "r") out += "\r";
          else if (nx === "0") out += "\x00";
          else if (nx === "$") out += "$";
          else out += "\\" + nx;
        } else out += "\\" + nx;
        i += 2; continue;
      }
      out += c; i++;
    }
    return out;
  }

  function splitTop(str, sep) { /* 顶层逗号切分(忽略引号与括号内) */
    var parts = [], depth = 0, inStr = null, cur = "";
    for (var i = 0; i < str.length; i++) {
      var c = str[i];
      if (inStr) { cur += c; if (c === "\\") { cur += str[++i] || ""; continue; } if (c === inStr) inStr = null; continue; }
      if (c === '"' || c === "'") { inStr = c; cur += c; continue; }
      if (c === "(" || c === "[") depth++;
      if (c === ")" || c === "]") depth--;
      if (c === sep && depth === 0) { parts.push(cur); cur = ""; continue; }
      cur += c;
    }
    if (cur.trim() !== "") parts.push(cur);
    return parts;
  }

  function parseValue(raw) {
    raw = raw.trim();
    if (/^'.*'$/.test(raw) || /^".*"$/.test(raw)) return { t: "s", v: parsePhpString(raw) };
    if (/^-?\d+$/.test(raw)) return { t: "i", v: parseInt(raw, 10) };
    if (/^-?\d*\.\d+([eE][+-]?\d+)?$/.test(raw)) return { t: "d", v: parseFloat(raw) };
    if (/^true$/i.test(raw)) return { t: "b", v: 1 };
    if (/^false$/i.test(raw)) return { t: "b", v: 0 };
    if (/^null$/i.test(raw)) return { t: "N" };
    var m = raw.match(/^array\s*\(([\s\S]*)\)$/i);
    if (m) {
      var items = splitTop(m[1], ","), map = [], idx = 0;
      items.forEach(function (it) {
        if (!it.trim()) return;
        var a = splitTop(it, "=>");
        if (a.length === 2) {
          var k = parseValue(a[0]);
          map.push([k, parseValue(a[1])]);
        } else map.push([{ t: "i", v: idx++ }, parseValue(a[0])]);
        idx = map.length;
      });
      return { t: "a", v: map };
    }
    return { t: "s", v: raw }; /* 兜底:当作字符串 */
  }

  function parseClass(code) {
    var nameM = code.match(/class\s+(\w+)/i);
    if (!nameM) throw new Error("未找到 class 定义");
    var props = [], re = /((?:public|protected|private|var)\s+)?\$(\w+)\s*=\s*([\s\S]+?);/g, m;
    var body = code.slice(code.indexOf("{") + 1, code.lastIndexOf("}"));
    while ((m = re.exec(body))) {
      var vis = (m[1] || "public").replace(/\s+/g, "").toLowerCase();
      props.push({ vis: vis === "var" ? "public" : vis, name: m[2], value: parseValue(m[3]) });
    }
    return { name: nameM[1], props: props };
  }

  function byteLen(s) { return new TextEncoder().encode(s).length; }

  function ser(v) {
    switch (v.t) {
      case "s": return 's:' + byteLen(v.v) + ':"' + v.v + '";';
      case "i": return "i:" + v.v + ";";
      case "d": return "d:" + v.v + ";";
      case "b": return "b:" + v.v + ";";
      case "N": return "N;";
      case "a": {
        var out = "a:" + v.v.length + ":{";
        v.v.forEach(function (kv) { out += ser(kv[0]) + ser(kv[1]); });
        return out + "}";
      }
      case "o": {
        var out2 = 'O:' + byteLen(v.name) + ':"' + v.name + '":' + v.props.length + ":{";
        v.props.forEach(function (p) {
          var key = p.name;
          if (p.vis === "protected") key = "\x00*\x00" + p.name;
          if (p.vis === "private") key = "\x00" + v.name + "\x00" + p.name;
          out2 += "s:" + byteLen(key) + ':"' + key + '";' + ser(p.value);
        });
        return out2 + "}";
      }
    }
    return "";
  }

  function serializeClass(cls) {
    return ser({ t: "o", name: cls.name, props: cls.props });
  }

  /* —— unserialize 反解 —— */
  function uns(s) {
    var pos = 0;
    function val() {
      var c = s[pos];
      if (c === "N") { pos += 2; return { t: "N" }; }
      var m;
      if (c === "b") { m = s.slice(pos).match(/^b:(\d);/); pos += m[0].length; return { t: "b", v: +m[1] }; }
      if (c === "i") { m = s.slice(pos).match(/^i:(-?\d+);/); pos += m[0].length; return { t: "i", v: +m[1] }; }
      if (c === "d") { m = s.slice(pos).match(/^d:([^;]+);/); pos += m[0].length; return { t: "d", v: m[1] }; }
      if (c === "s") {
        m = s.slice(pos).match(/^s:(\d+):"/); var len = +m[1]; pos += m[0].length;
        var str = s.substr(pos, len); pos += len + 2; return { t: "s", v: str };
      }
      if (c === "a" || c === "O") {
        var isObj = c === "O";
        m = s.slice(pos).match(new RegExp("^" + c + ":(\\d+):\""));
        var nlen = +m[1]; pos += m[0].length;
        var name = s.substr(pos, nlen); pos += nlen + 2; /* :" 之后 */
        m = s.slice(pos).match(/^:(\d+):\{/); pos += m[0].length;
        var count = +m[1], items = [];
        for (var i = 0; i < count; i++) {
          var k = val(), v = val();
          items.push([k, v]);
        }
        pos++; /* } */
        return isObj ? { t: "o", name: name, props: items } : { t: "a", v: items };
      }
      if (c === "R" || c === "r") { m = s.slice(pos).match(/^[Rr]:(\d+);/); pos += m[0].length; return { t: "R", v: +m[1] }; }
      throw new Error("无法解析位置 " + pos + " 附近:" + s.slice(pos, pos + 20));
    }
    var out = val();
    return out;
  }

  function visName(k) {
    if (/^\x00\*\x00/.test(k)) return { vis: "protected", name: k.replace(/^\x00\*\x00/, "") };
    var m = k.match(/^\x00([^\x00]+)\x00(.+)$/);
    if (m) return { vis: "private", name: m[2] + "(来自类 " + m[1] + ")" };
    return { vis: "public", name: k };
  }

  function dump(v, indent) {
    var pad = new Array(indent + 1).join("  ");
    switch (v.t) {
      case "N": return "NULL";
      case "b": return v.v ? "true" : "false";
      case "i": case "d": return String(v.v);
      case "s": return '"' + v.v.replace(/\x00/g, "%00") + '"';
      case "R": return "(引用 → #" + v.v + ")";
      case "a": {
        if (!v.v.length) return "[]";
        var lines = ["["];
        v.v.forEach(function (kv) { lines.push(pad + "  " + dump(kv[0], 0) + " => " + dump(kv[1], indent + 1)); });
        lines.push(pad + "]");
        return lines.join("\n");
      }
      case "o": {
        var ls = [v.name + " {"];
        v.props.forEach(function (kv) {
          var n = visName(kv[0].v);
          ls.push(pad + "  " + n.vis + " $" + n.name + " = " + dump(kv[1], indent + 1));
        });
        ls.push(pad + "}");
        return ls.join("\n");
      }
    }
    return "?";
  }

  /* —— Runner 交互 —— */
  var urlencodeOn = false, lastOut = "";
  function initRunner() {
    document.body.insertAdjacentHTML("beforeend", RUNNER_HTML);
    document.addEventListener("click", function (e) {
      if (e.target.closest("[data-runner-open]")) { open(); return; }
      if (e.target.closest("[data-runner-close]") || e.target.id === "runnerMask") { close(); return; }
      if (e.target.closest("[data-runner-sample]")) {
        document.getElementById("runnerIn").value = RUNNER_SAMPLE;
        run(); return;
      }
      if (e.target.closest("[data-runner-run]")) { run(); return; }
      if (e.target.closest("[data-runner-enc]")) {
        urlencodeOn = !urlencodeOn;
        e.target.textContent = "URL 编码开关:" + (urlencodeOn ? "开" : "关");
        if (lastOut) document.getElementById("runnerOut").textContent = urlencodeOn ? fixedEncode(lastOut) : lastOut;
        return;
      }
      if (e.target.closest("[data-runner-back]")) {
        var out = document.getElementById("runnerBackOut");
        try { out.textContent = dump(uns(document.getElementById("runnerBack").value), 0); }
        catch (err) { out.textContent = "解析失败:" + err.message; }
      }
      if (e.target.id === "runnerCopy" && lastOut) {
        (navigator.clipboard ? navigator.clipboard.writeText(document.getElementById("runnerOut").textContent) : Promise.reject())
          .then(function () { e.target.textContent = "已复制"; setTimeout(function () { e.target.textContent = "复制结果"; }, 1500); });
      }
    });
    document.addEventListener("keydown", function (e) { if (e.key === "Escape") close(); });

    function open() { document.getElementById("labRunner").classList.add("open"); document.getElementById("runnerMask").classList.add("open"); }
    function close() { document.getElementById("labRunner").classList.remove("open"); document.getElementById("runnerMask").classList.remove("open"); }
    function fixedEncode(s) {
      var out2 = "";
      for (var i = 0; i < s.length; i++) {
        var c = s[i];
        if (/[A-Za-z0-9\-._~]/.test(c)) out2 += c;
        else {
          var bytes = new TextEncoder().encode(c);
          for (var j = 0; j < bytes.length; j++) out2 += "%" + ("0" + bytes[j].toString(16).toUpperCase()).slice(-2);
        }
      }
      return out2;
    }
    function run() {
      var out = document.getElementById("runnerOut");
      try {
        var cls = parseClass(document.getElementById("runnerIn").value);
        lastOut = serializeClass(cls);
        out.textContent = urlencodeOn ? fixedEncode(lastOut) : lastOut;
        out.textContent += "\n\n—— 属性数: " + cls.props.length;
      } catch (err) { out.textContent = "解析失败:" + err.message; }
    }
    document.getElementById("runnerIn").value = RUNNER_SAMPLE;
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

    var tb = document.getElementById("themeBtn");
    if (tb) tb.onclick = function () {
      var next = root.getAttribute("data-theme") === "dark" ? "light" : "dark";
      root.setAttribute("data-theme", next);
      localStorage.setItem("phplab-theme", next);
    };
  });
})();
