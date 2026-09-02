## About

> hello-ctf.com 基础靶场计划,访问 [[hello-ctf.com 配套靶场]](https://hello-ctf.com/hc-labs/) 探索更多靶场。

PHPSerialize-labs 是一个使用 PHP 语言编写的,用于学习 CTF 中 PHP 反序列化的**引导式入门靶场**。

- **引导关 + 挑战关双模式**:多数关卡逐步引导,少数关卡(POPC 链、CVE、真题)只给最少提示;
- **页面即答题**:每个关卡页内置作答表单、curl 命令与 HackBar 指引,无需自己搭请求环境;
- **内置 PHP 序列化运行器**:浏览器里写类定义直接生成 serialize payload,无需安装 PHP;
- **渐进式提示 + 防剧透解析**:提示逐条展开,WriteUP 二次确认后可达。

> 本仓库 main 分支为全新 UI 版;**原始版本完整保留在 [old 分支](https://github.com/ProbiusOfficial/PHPSerialize-labs/tree/old)**(含完整 WriteUP 于 README)。

在开始学习序列化和反序列化之前,请先完成一些前导课程:

- PHP 环境配置
- PHP 语法基础
- PHP 面向对象编程

若您对以上内容不熟悉,推荐您阅读菜鸟教程中 [PHP面向对象](https://www.runoob.com/php/php-oop.html) 部分。

## 关卡信息

| 序号 | 章节 | 知识点 | 模式 | 难度 |
| :-- | :-- | :--------------------------- | :-- | :-- |
| Level 1 | 面向对象基础 | 类的实例化 | 引导 | ★ |
| Level 2 | 面向对象基础 | 对象中值的传递 | 引导 | ★ |
| Level 3 | 面向对象基础 | 对象中值的权限 | 引导 | ★ |
| Level 4 | 序列化入门 | 序列化初体验 | 引导 | ★ |
| Level 5 | 序列化入门 | 序列化的普通值规则 | 引导 | ★★ |
| Level 6 | 序列化入门 | 序列化的权限修饰规则 | 引导 | ★★ |
| Level 7 | 反序列化与魔术方法 | 实例化和反序列化 | 引导 | ★★ |
| Level 8 | 反序列化与魔术方法 | 构造函数和析构函数以及GC机制 | 引导 | ★★ |
| Level 9 | 反序列化与魔术方法 | 构造函数的后门 | 引导 | ★★ |
| Level 10 | 反序列化与魔术方法 | __wakeup() | 引导 | ★ |
| Level 11 | 反序列化与魔术方法 | __wakeup() CVE-2016-7124 | 挑战 | ★★ |
| Level 12 | 反序列化与魔术方法 | __sleep() | 引导 | ★★ |
| Level 13 | 反序列化与魔术方法 | __toString() | 引导 | ★ |
| Level 14 | 反序列化与魔术方法 | __invoke() | 引导 | ★ |
| Level 15 | POP 链与引用 | POP 链前置 | 挑战 | ★★ |
| Level 16 | POP 链与引用 | POP 链构造 | 挑战 | ★★★ |
| Level 17 | 字符串逃逸 | 字符串逃逸基础-无中生有 | 引导 | ★★★ |
| Level 18 | 字符串逃逸 | 字符串逃逸基础-尾部判定 | 引导 | ★★★ |
| Level 19 | 字符串逃逸 | 字符串逃逸-减少 | 引导 | ★★★ |
| Level 20 | 字符串逃逸 | 字符串逃逸-增多 | 引导 | ★★★ |
| Level 21 | POP 链与引用 | 引用的利用 | 引导 | ★★ |
| Level 22 | 真实攻击面 | session 反序列化 | 引导 | ★★★ |
| Level 23 | 真实攻击面 | phar 反序列化 | 引导 | ★★★ |
| Level 24 | 真实攻击面 | 原生类利用 | 引导 | ★★ |

每关的完整 WriteUP 在 [writeups/](writeups/) 目录(按关卡编号命名),关卡页「解析」页也有直达链接。

> 计划中的内容:
>
> - 字符串逃逸综合 / 序列化布尔特性
> - 命名空间与反序列化
> - 综合大题(上传 + phar + POP 组合)
> - 练习题集扩充(欢迎 PR 提交真题改编,格式参考 [exerciseCollection/](exerciseCollection/))

## 部署

### Docker 部署(推荐)

```bash
docker run -p 8080:80 -d ghcr.io/probiusofficial/phpserialize-labs
```

启动后访问:<http://localhost:8080/>

> 镜像基于 PHP 5.4:Level 11 考察的 CVE-2016-7124 仅在该版本线上成立,请勿自行更换高版本镜像。

### Docker Compose 部署

```bash
git clone --depth 1 https://github.com/ProbiusOfficial/PHPSerialize-labs.git
cd PHPSerialize-labs
docker-compose up -d
```

启动后访问:<http://localhost:8080/>

### 本地部署(PHPStudy 等)

1. 下载并安装 [PHPStudy](https://www.xp.cn/phpstudy#phpstudy)
2. 将网站根目录设置为 `PHPSerialize-labs` 目录
3. 启动 Apache 服务(PHP 5.4 - 7.x 均可,PHP 8 不兼容,见下方说明)
4. 在浏览器中访问 <http://localhost/>

### 如何答题

1. 进入关卡,阅读「关卡输出」里的演示和「题目源码」;
2. 在「提示」页逐条展开提示(挑战关只有一条);
3. 需要构造序列化串时,点「⌨ 运行器」写类定义直接生成;
4. 在「作答」页提交 payload(或复制页面给出的 curl 命令);
5. 真不会再看「解析」。

### PHP 版本兼容性说明

- 靶场教学目标环境为 **PHP 5.4**(CVE-2016-7124 复现依赖);
- main 分支已修复全部 PHP 8 致命语法问题(裸常量、裸数组键等),但 **Level 11 的考点在高版本 PHP 上不再成立**,属预期行为;
- 旧版代码请见 old 分支。

## 合作平台

题目已上线 [【NSSCTF平台】](https://www.nssctf.cn/problem) 可在来源中选择 **HelloCTF** 或直接搜索 **反序列化靶场**。

# 推荐的学习资源

- [[PHP反序列化这一篇就够了- Y4tacker]](https://github.com/Y4tacker/Web-Security/blob/9ac18c13c650ca193531baeb945e2af4d767f61d/Unserialize/PHP/php%E5%8F%8D%E5%BA%8F%E5%88%97%E5%8C%96.md)

  > 最详细的PHP反序列化一文教程。

- [Bilibili-橙子科技-PHP反序列化漏洞学习](https://www.bilibili.com/video/BV1R24y1r71C)

  > 为爱发电最强的一集,陈腾师傅的课应该是圈里面讲的最细的了,而且是一套完整体系,通俗易懂,很推荐各位看x
  > 这个视频还有一套配套的靶场:[mcc0624/php_ser_Class](https://github.com/mcc0624/php_ser_Class)

- [ctfshow/web257-268](https://ctf.show/challenges#web254-713)

  > ctfshow的题目是圈内出名的体系化和梯度化,很适合新手入门,其WP在网络上很容易找到,生态很不错。
  > 当然ctfshow本身也有视频讲解:[Bilibili-ctfshow-Web257-268](https://www.bilibili.com/video/BV1D64y1m78f)

- [php-SER-labs-docker](https://github.com/ProbiusOfficial/php-SER-labs-docker)

  > 基于fine-1(这周末在做梦)师傅的靶场(<https://github.com/fine-1/php-SER-libs)添加的容器版本,在README中附带有WriteUp>

- [PHP 手册](https://www.php.net/manual/zh/)

  > PHP官方手册,遇事不决,看看说明书x
