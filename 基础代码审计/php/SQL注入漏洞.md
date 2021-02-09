## 1 审计经验

#### 1.1 业务功能点
- 1）文件上传功能。一般程序员不会注意到对文件名进行过滤，但是又需要把文件名保存在数据库中，所以就会存在SQL注入漏洞。
- 2）登录页面。登录页面的注入现在大多是发生在HTTP头里面的client-ip和x-forward-for。
- 3）订单处理功能。由于订单涉及购物车等多个交互，所以经常会发生二次注入。

#### 1.2 审计思路
- 1）查找关键字：数据库操作关键字select from、update、insert、delete、mysql_connect、mysql_query、mysql_fetch_row等。
- 2）宽字节注入关键字： SET NAMES、character_set_client=gbk、mysql_set_charset('gbk')。
- 3）二次urldecode注入：参数过滤通常会加上反斜杠进行转义，而使用了urldecode或者rawurldecode函数，则会导致二次解码生成单引号而引发注入。

#### 1.3 过滤函数和类
- 1）addslashes函数：简单的检查string类型参数的函数，将单双引号、反斜杠及空字符进行转义。
- 2）mysql_[real_]escape_string函数：mysql_escape_string 和mysql_real_escape_string 函数都是对字符串进行过滤，在PHP4.0.3以上版本才存在，如下字符受影响：[\x00][\n ][\r ][\]['][" ][\xla], 两个函数唯一不一样的地方在于mysql_real_escape_string要接受的是一个连接句柄并根据当前字符集转义字符串，所以推荐使用mysql_real_escape_string。
- 3）intval 等字符转换：利用参数类型白名单的方式来防止漏洞，intval的作用是将变量转换成int类型。

#### 1.4 PDO prepare预编译
PHP pdo的prepare通过预编译的方式来处理数据库查询。
> 预处理语句（Prepared Statements):可以把它们想成是一种编译过的要执行的SQL语句模板，可以使用不同的变量参数定制它。查询只需要被解析（或准备）一次，但可以使用相同或不同的参数执行多次。当查询准备好（Prepared）之后，数据库就会分析，编译并优化它要执行查询的计划。传给预处理语句的参数不需要使用引号，底层驱动会为你处理这个。

```php
<?php
dbh = new PDO("mysql:host=localhost; dbname=demo", "user", "pass") ;
$dbh->exec("set names 'gbk'");
$sq1="select * from test where name = ? and password = ?";
$stmt = $dbh->prepare($sql) ;
$exeres = $stmt->execute(array ($name，Spass));

// 这段代码虽然使用了pdo的prepare方式来处理sql查询，但是当PHP版本<5.3.6之前还是存在宽字节SQL注入漏洞，原因在于这样的查询方式是使用了PHP本地模拟prepare，再把完整的SQL语句发送给MySQL服务器，并且有使用setnames 'gbk'语句，所以会有PHP和MySQL编码不一致的原因导致 SQL 注入，正确的写法应该是使用ATTR_EMULATE_PREPARES 来禁用PHP本地模拟prepare。

<?php
dbh = new PDO("mysql :host=localhost; dbname=demo", "user", "pass");
$dbh->setAttribute(PDO: :ATTR_EMULATE_PREPARES, false);  // 禁用PHP本地模拟prepare
$dbh->exec("set names 'utf8'");
$sql = "select * from test where name = ? and password = ?";
$stmt = $dbh->prepare($sq1);
$exeres = $stmt->execute(array($name, spass));
```

## 2 常见实例

#### 2.1 代码审计


#### 2.2 开源实例
- 1）espcms_utf8_v5.8.14.02.12


## 参考：
1、《代码审计  企业级Web代码安全架构》 尹毅
2、https://blog.csdn.net/qq_45521281/article/details/106203045