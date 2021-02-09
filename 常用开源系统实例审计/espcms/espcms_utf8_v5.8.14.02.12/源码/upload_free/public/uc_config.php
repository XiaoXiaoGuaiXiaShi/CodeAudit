<?php

/*
  PHP version 5
  Copyright (c) 2002-2013 ECISP.CN、EarcLink.COM
  警告：这不是一个免费的软件，请在许可范围内使用，请尊重知识产权，侵权必究，举报有奖！

  作者：黄祥云 E-mail:6326420@qq.com  QQ:6326420 TEL:18665655030
  ESPCMS官网介绍：http://www.ecisp.cn 企业建站：http://www.earclink.cn
 */
define('UC_CONNECT', 'mysql');
define('UC_DBHOST', $CONFIG['mem_ucdbhost']);
define('UC_DBUSER', $CONFIG['mem_ucdbuser']);
define('UC_DBPW', $CONFIG['mem_ucdbpw']);
define('UC_DBNAME', $CONFIG['mem_ucdbname']);
define('UC_DBCHARSET', $CONFIG['mem_ucdbchart']);
define('UC_DBTABLEPRE', $CONFIG['mem_ucdbname'] . '.' . $CONFIG['mem_ucdbtable']);
define('UC_KEY', $CONFIG['mem_uckey']);
define('UC_API', $CONFIG['mem_ucapi']);
define('UC_CHARSET', $CONFIG['mem_ucchart']);
define('UC_IP', '');
define('UC_APPID', $CONFIG['mem_ucapiid']);
define('UC_PPP', '20');
?>