<?php

/*
  PHP version 5
  Copyright (c) 2002-2014 ECISP.CN、EarcLink.COM
  警告：这不是一个免费的软件，请在许可范围内使用，请尊重知识产权，侵权必究，举报有奖

  作者：黄祥云 E-mail:6326420@qq.com  QQ:6326420 TEL:18665655030
  ESPCMS官网介绍：http://www.ecisp.cn 企业建站：http://www.earclink.cn
 */

class lib_im extends connector {

	function lib_im() {
		$this->softbase();
		parent::start_pagetemplate();

		$this->pagetemplate->libfile = true;
	}

	function call_im($lng, $para, $filename = 'im', $outHTML = null) {
		$para = $this->fun->array_getvalue($para);
		$lngpack = $lng ? $lng : $this->CON['is_lancode'];
		$lng = ($lng == 'big5') ? $this->CON['is_lancode'] : $lng;
		include admin_ROOT . 'datacache/' . $lng . '_pack.php';
		if (!$this->CON['is_imcall']) return null;
		$call['call_style'] = $this->CON['call_style'];
		$call['call_type'] = $this->CON['call_type'];
		$call['call_position'] = $this->CON['call_position'];
		$call_array = $this->get_calling_array(0, 1, $lng);
		$array = $call_array['list'];
		if (is_array($array)) {
			foreach ($array as $key => $value) {
				if ($value['type'] == 1) {
					$array[$key]['code'] = stripslashes(htmlspecialchars_decode($value['code']));
				}
			}
		}
		$this->pagetemplate->assign('bbslink', $this->get_link('forum', array(), $lngpack));
		$this->pagetemplate->assign('memberlink', $this->get_link('memberlogin', array(), $lngpack));
		$this->pagetemplate->assign('lng', $lng);
		$this->pagetemplate->assign('array', $array);
		$this->pagetemplate->assign('call', $call);
		$this->pagetemplate->assign('lngpack', $LANPACK);
		if (!empty($outHTML)) {
			$output = $this->pagetemplate->fetch(null, null, $outHTML);
		} else {
			$output = $this->pagetemplate->fetch($lng . '/lib/' . $filename);
		}
		return $output;
	}


}

?>
