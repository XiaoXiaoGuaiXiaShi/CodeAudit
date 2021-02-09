<?php

/*
  PHP version 5
  Copyright (c) 2002-2014 ECISP.CN、EarcLink.COM
  警告：这不是一个免费的软件，请在许可范围内使用，请尊重知识产权，侵权必究，举报有奖

  作者：黄祥云 E-mail:6326420@qq.com  QQ:6326420 TEL:18665655030
  ESPCMS官网介绍：http://www.ecisp.cn 企业建站：http://www.earclink.cn
 */

class lib_mlink extends connector {

	function lib_mlink() {
		$this->softbase();
		parent::start_pagetemplate();

		$this->pagetemplate->libfile = true;
	}



	function field_mlink($type, $returnname = 'typename', $lng = '') {
		$lngpack = $lng ? $lng : $this->CON['is_lancode'];
		$lng = ($lng == 'big5') ? $this->CON['is_lancode'] : $lng;
		if ($type == 1) {
			$link = $this->memberlink($read, $lngpack);
			return $link[$returnname];
		} else {
			$link = $this->get_link($returnname, $read, $lngpack);
			return $link;
		}
	}


}

?>
