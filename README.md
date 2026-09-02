# PHPSerialize-labs

hello-ctf.com 基础靶场计划之一。使用 PHP 编写的 CTF 反序列化引导靶场,内容覆盖面向对象基础、序列化格式、魔术方法、POP 链、字符串逃逸、session 与 phar 反序列化、原生类利用。

原始版本完整保留在本仓库的 [main 分支](https://github.com/ProbiusOfficial/PHPSerialize-labs/tree/main)。

## 关卡

| 序号 | 章节 | 知识点 | 模式 |
| --- | --- | --- | --- |
| 1-3 | 面向对象基础 | 类的实例化、属性传递、访问控制 | 引导 |
| 4-6 | 序列化入门 | serialize 基本类型与权限修饰规则 | 引导 |
| 7-9 | 反序列化 | 实例化与反序列化的差异、构造与析构函数、GC 机制 | 引导 |
| 10-14 | 魔术方法 | __wakeup、__sleep、__toString、__invoke | 引导 |
| 11 | 魔术方法 | CVE-2016-7124 绕过 __wakeup | 挑战 |
| 15-16 | POP 链 | 链的原理与构造 | 挑战 |
| 17-20 | 字符串逃逸 | 无中生有、尾部判定、过滤增多与减少 | 引导 |
| 21 | POP 链 | 序列化引用(R)的利用 | 引导 |
| 22 | session | 处理器差异导致的 session 反序列化 | 引导 |
| 23 | phar | metadata 反序列化 | 引导 |
| 24 | 原生类 | DirectoryIterator、SplFileObject | 引导 |
| 25 | 原生类 | Error / Exception 与 __toString | 引导 |
| 26 | 魔术方法 | __get 与 __call 跳板链 | 引导 |

每关的 WriteUP 位于 [writeups/](writeups/) 目录,关卡页的「解析」面板内也可直接查看。

计划中:字符串逃逸综合、命名空间与反序列化、综合大题、练习题集扩充。欢迎通过 PR 提交题目或题解,格式参考现有关卡。

## 页面功能

- 每关提供作答表单,可在页面内直接提交 payload;同时给出等价的 curl 命令。
- 「运行器」是一个与靶场同版本的 PHP 运行环境,用于构造和验证 payload。
- 「提示」逐条展开,挑战关仅提供一条。
- 「解析」包含思路、exp.php 参考代码(可一键载入运行器)与完整解析。

## 部署

Docker:

    docker run -p 8080:80 -d ghcr.io/probiusofficial/phpserialize-labs

Docker Compose:

    docker compose up -d

访问 http://localhost:8080/。

本地部署:将网站根目录指向本目录,PHP 5.4 至 7.x 均可运行。

## PHP 版本说明

本分支使用 php:8.2-apache。new 分支默认使用 php:5.5-apache:复现 Level 11 的 CVE-2016-7124(仅影响 PHP 5.6.25 / 7.0.10 之前的版本),并支持 Level 22 所需的 php_serialize 处理器(PHP 5.5.4 引入)。

本分支上 Level 11 与 Level 23 不再成立:前者是 CVE 已在新版修复,后者是 PHP 8 起文件函数不再触发 phar metadata 自动反序列化。两关仅作新旧行为对照,其余关卡解题方式与 new 分支一致。

切回 new 分支:git checkout new (docs(php8): 说明 Level 11/23 在 PHP 8 上的行为差异)

## 相关链接

- 配套教程与更多靶场:https://hello-ctf.com/hc-labs/
- 在线练习:NSSCTF 平台,来源筛选 HelloCTF
- PHP 手册:https://www.php.net/manual/zh/

## 学习资源

- Y4tacker,《PHP 反序列化这一篇就够了》:https://github.com/Y4tacker/Web-Security/blob/9ac18c13c650ca193531baeb945e2af4d767f61d/Unserialize/PHP/php%E5%8F%8D%E5%BA%8F%E5%88%97%E5%8C%96.md
- 橙子科技 PHP 反序列化教程(Bilibili):https://www.bilibili.com/video/BV1R24y1r71C
- ctfshow web 入门系列(web254 起):https://ctf.show/challenges
