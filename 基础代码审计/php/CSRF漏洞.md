
## 1 审计经验

#### 1.1 业务功能点
- 1）CSRF主要是用于越权操作，所有漏洞自然在有权限控制的地方，像管理后台、会员中心、论坛帖子以及交易管理等。

#### 1.2 审计思路
- 1）查看核心文件中是否有验证token和referer相关的代码。

#### 1.3 漏洞防范
防御CSRF漏洞的最主要问题是解决可信的问题。
- 1）增加token/referer验证避免img标签请求的水坑攻击。
- 2）增加验证码。


## 2 常见实例

#### 2.1 代码审计

#### 2.2 开源实例
- 1）Discuz CSRF：WooYun-2014-64886

## 参考：
1、《代码审计  企业级Web代码安全架构》 尹毅
2、