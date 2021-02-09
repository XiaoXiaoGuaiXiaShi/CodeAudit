<?php

/*
  PHP version 5
  Copyright (c) 2002-2013 ECISP.CN、EarcLink.COM
  警告：这不是一个免费的软件，请在许可范围内使用，请尊重知识产权，侵权必究，举报有奖！

  作者：黄祥云 E-mail:6326420@qq.com  QQ:6326420 TEL:18665655030
  ESPCMS官网介绍：http://www.ecisp.cn 企业建站：http://www.earclink.cn
 */
if (isset($modulesid) && $modulesid == TRUE) {
	$i = isset($modules) ? count($modules) : 0;

	$modules[$i]['plugname'] = '货到付款';

	$modules[$i]['code'] = basename(__FILE__, '.php');

	$modules[$i]['desc'] = '货到付款支付的城市有北京、上海、广州、深圳、武汉、长春、重庆';

	$modules[$i]['is_cod'] = '1';

	$modules[$i]['is_online'] = '0';

	$modules[$i]['pay_fee'] = '0';

	$modules[$i]['website'] = 'http://www.ecisp.cn';

	$modules[$i]['version'] = '1.0.1';
	$modules[$i]['config'] = array();
	return;
}


class downpay {

	function downpay() {

	}

	function __construct() {
		$this->downpay();
	}

	function get_code($order, $payment, $return_url, $notify_url) {
		return '货到付款';
	}

	function respond($payment=null, $orderread=array()) {
		return false;
	}

}
?>