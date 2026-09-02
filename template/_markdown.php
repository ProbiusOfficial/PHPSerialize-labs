<?php
/*
极简 Markdown 渲染(题解专用):标题 / 代码块 / 行内代码 / 加粗 / 链接 / 列表 / 引用
© ProbiusOfficial(@hello-ctf.com) · github.com/ProbiusOfficial/PHPSerialize-labs
*/

function lab_markdown($md) {
    /* 1) 先摘出围栏代码块,避免内部被二次处理 */
    $fences = array();
    $md = preg_replace_callback('/```[a-zA-Z0-9]*\n(.*?)\n?```/s', function ($m) use (&$fences) {
        $fences[] = '<pre><code>' . htmlspecialchars($m[1]) . '</code></pre>';
        return "\x01F" . (count($fences) - 1) . "\x01";
    }, $md);

    /* 2) 转义 + 行内规则 */
    $md = htmlspecialchars($md);
    $md = preg_replace_callback('/`([^`\n]+)`/', function ($m) {
        return '<code>' . $m[1] . '</code>';
    }, $md);
    $md = preg_replace('/\*\*([^*\n]+)\*\*/', '<b>$1</b>', $md);
    $md = preg_replace('/\[([^\]]+)\]\(([^)\s]+)\)/', '<a href="$2" target="_blank" rel="noreferrer">$1</a>', $md);

    /* 3) 逐行重组块级结构 */
    $html = '';
    $inList = false;
    $inQuote = false;
    foreach (explode("\n", $md) as $line) {
        $t = trim($line);
        if (preg_match('/^\x01F(\d+)\x01$/', $t, $m)) {
            if ($inList) { $html .= '</ul>'; $inList = false; }
            if ($inQuote) { $html .= '</blockquote>'; $inQuote = false; }
            $html .= $fences[(int)$m[1]];
            continue;
        }
        if ($t === '') {
            if ($inList) { $html .= '</ul>'; $inList = false; }
            if ($inQuote) { $html .= '</blockquote>'; $inQuote = false; }
            continue;
        }
        if (preg_match('/^(#{1,4})\s+(.*)$/', $t, $m2)) {
            if ($inList) { $html .= '</ul>'; $inList = false; }
            if ($inQuote) { $html .= '</blockquote>'; $inQuote = false; }
            $lvl = strlen($m2[1]) + 2;
            $html .= '<h' . $lvl . '>' . $m2[2] . '</h' . $lvl . '>';
            continue;
        }
        if ($t === '---' || $t === '***') {
            if ($inList) { $html .= '</ul>'; $inList = false; }
            if ($inQuote) { $html .= '</blockquote>'; $inQuote = false; }
            $html .= '<hr>';
            continue;
        }
        if ($t[0] === '>') {
            if (!$inQuote) { $html .= '<blockquote>'; $inQuote = true; }
            $html .= '<p>' . ltrim($t, '> ') . '</p>';
            continue;
        }
        if (preg_match('/^[-*]\s+(.*)$/', $t, $m3)) {
            if (!$inList) { $html .= '<ul>'; $inList = true; }
            $html .= '<li>' . $m3[1] . '</li>';
            continue;
        }
        if ($inList) { $html .= '</ul>'; $inList = false; }
        if ($inQuote) { $html .= '</blockquote>'; $inQuote = false; }
        $html .= '<p>' . $t . '</p>';
    }
    if ($inList) { $html .= '</ul>'; }
    if ($inQuote) { $html .= '</blockquote>'; }
    return $html;
}
