<?php

/*
  PHP version 5
  Copyright (c) 2002-2014 ECISP.CN、EarcLink.COM
  警告：这不是一个免费的软件，请在许可范围内使用，请尊重知识产权，侵权必究，举报有奖

  作者：黄祥云 E-mail:6326420@qq.com  QQ:6326420 TEL:18665655030
  ESPCMS官网介绍：http://www.ecisp.cn 企业建站：http://www.earclink.cn
 */

class lib_lng extends connector {

	function lib_lng() {
		$this->softbase();
		parent::start_pagetemplate();

		$this->pagetemplate->libfile = true;
	}

	function call_lng($lng, $para, $filename = 'lng', $outHTML = null) {
		if ($this->CON['is_alonelng']) return false;
		$para = $this->fun->array_getvalue($para);

		$lngpack = $lng ? $lng : $this->CON['is_lancode'];

		$lng = ($lng == 'big5') ? $this->CON['is_lancode'] : $lng;
		include admin_ROOT . 'datacache/' . $lng . '_pack.php';
		$db_table = db_prefix . 'lng';
		$rsList = $this->get_lng_array(null, 1);
		if (count($rsList['list']) > 0) {
			foreach ($rsList['list'] as $key => $value) {
				$rsList['list'][$key]['link'] = $this->get_link('lng', $value, $value['lng']);
			}
		}
		$this->pagetemplate->assign('lng', $lng);
		$this->pagetemplate->assign('lngpack', $LANPACK);
		$this->pagetemplate->assign('array', $rsList['list']);
		if (!empty($outHTML)) {
			$output = $this->pagetemplate->fetch(null, null, $outHTML);
		} else {
			$output = $this->pagetemplate->fetch($lng . '/lib/' . $filename);
		}
		return $output;
	}


}

?>
