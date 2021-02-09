<?php

/*
  PHP version 5
  Copyright (c) 2002-2013 ECISP.CN、EarcLink.COM
  警告：这不是一个免费的软件，请在许可范围内使用，请尊重知识产权，侵权必究，举报有奖！
  作者：黄祥云 E-mail:6326420@qq.com  QQ:6326420 TEL:18665655030
  ESPCMS官网介绍：http://www.ecisp.cn 企业建站：http://www.earclink.cn
 */

class mainpage extends connector {

	function mainpage() {
		$this->softbase(false);
		$this->mlink = $this->memberlink(array(), admin_LNG);
	}

	function in_index() {
		parent::start_pagetemplate();
		exit('抱歉，免费版无WAP功能！');
	}

	function picpath($pic = null) {
		if (!empty($pic)) {
			if (stripos($pic, 'http')) {
				return $pic;
			} else {
				return str_replace('../', admin_ROOT . '/', $pic);
			}
		} else {
			return $pic;
		}
	}



	function getMimeType($file) {
		return is_dir($file) ? $file : $this->mime($file);
	}



	function mime($file) {

		$file = realpath($file);

		$options = pathinfo($file, PATHINFO_EXTENSION);
		return $options;
	}

}

?>