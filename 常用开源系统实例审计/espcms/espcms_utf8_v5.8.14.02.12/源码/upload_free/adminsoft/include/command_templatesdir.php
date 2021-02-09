<?php

/*
  PHP version 5
  Copyright (c) 2002-2013 ECISP.CN、EarcLink.COM
  警告：这不是一个免费的软件，请在许可范围内使用，请尊重知识产权，侵权必究，举报有奖！

  作者：黄祥云 E-mail:6326420@qq.com  QQ:6326420 TEL:18665655030
  ESPCMS官网介绍：http://www.ecisp.cn 企业建站：http://www.earclink.cn
 */

$TEMPLATESDIR = array(
    'index' => '',
    'article' => 'article',
    'form' => 'form',
    'member' => 'member',
    'forum' => 'forum',
    'order' => 'member',
);

$TEMPNAMELIST = array(
    'typelist' => 'article_list',
    'typeread' => 'article_read',
    'typeindex' => 'article_index',
    'subjectlist' => 'subject_list',
    'subjectindex' => 'subject_index',
);

$SUBURLLIST = array(
    0 => array('id' => '{sid}_{pageid}', 'name' => 'dirname/1000'),
    1 => array('id' => '{sid}_list_{pageid}', 'name' => 'dirname/1000_list'),
    2 => array('id' => '{dirname}_list_{pageid}', 'name' => 'dirname/dirname_list'),
);

$TYPEURLLIST = array(
    0 => array('id' => '{tid}_{pageid}', 'name' => 'dirname/1000'),
    1 => array('id' => '{tid}_list_{pageid}', 'name' => 'dirname/1000_list'),
    2 => array('id' => '{dirname}_list_{pageid}', 'name' => 'dirname/dirname_list'),
);

$TYPEURLREAD = array(
    0 => array('id' => '{did}', 'name' => 'dirname/1'),
    1 => array('id' => '{datetime}{did}', 'name' => 'dirname/201101010101251'),
    2 => array('id' => '{data}/{did}', 'name' => 'dirname/20110101/1'),
    3 => array('id' => '{y}/{m}/{d}/{did}', 'name' => 'dirname/2011/01/01/1'),
    4 => array('id' => '{y}/{m}{d}/{did}', 'name' => 'dirname/2011/0101/1'),
);
?>
