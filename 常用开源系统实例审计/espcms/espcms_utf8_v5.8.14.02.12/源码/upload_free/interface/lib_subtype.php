<?php

/*
  PHP version 5
  Copyright (c) 2002-2014 ECISP.CN、EarcLink.COM
  警告：这不是一个免费的软件，请在许可范围内使用，请尊重知识产权，侵权必究，举报有奖

  作者：黄祥云 E-mail:6326420@qq.com  QQ:6326420 TEL:18665655030
  ESPCMS官网介绍：http://www.ecisp.cn 企业建站：http://www.earclink.cn
 */

class lib_subtype extends connector {

	function lib_subtype() {
		$this->softbase();
		parent::start_pagetemplate();

		$this->pagetemplate->libfile = true;
	}

	function call_subtype($lng, $para, $filename = 'subtype', $outHTML = null) {
		$para = $this->fun->array_getvalue($para);
		$lngpack = $lng ? $lng : $this->CON['is_lancode'];
		$lng = ($lng == 'big5') ? $this->CON['is_lancode'] : $lng;
		include admin_ROOT . 'datacache/' . $lng . '_pack.php';
		$mid = intval($para['mid']);
		if (empty($mid)) {
			return null;
		}
		$limit = intval($para['max']);
		$limit = empty($limit) ? 0 : $limit;
		$subtype = $this->get_subjectlist_array(0, $mid, $lng, 1, $limit);
		if ($subtype['num'] > 0 && is_array($subtype['list'])) {
			$subtypelist = $subtype['list'];
			foreach ($subtypelist as $key => $value) {
				$value['link'] = $this->get_link('subtype', $value, $lngpack);
				$value['title'] = $value['subjectname'];
				$subtypelist[$key] = $value;
			}
		}
		$this->pagetemplate->assign('array', $subtypelist);
		$this->pagetemplate->assign('lng', $lng);
		$this->pagetemplate->assign('lngpack', $LANPACK);
		if (!empty($outHTML)) {
			$output = $this->pagetemplate->fetch(null, null, $outHTML);
		} else {
			$output = $this->pagetemplate->fetch($lng . '/lib/' . $filename);
		}
		return $output;
	}



	function field_subtype($sid, $returnname = 'subjectname', $lng = '') {
		if (empty($sid)) return false;
		$lngpack = $lng ? $lng : $this->CON['is_lancode'];
		$lng = ($lng == 'big5') ? $this->CON['is_lancode'] : $lng;

		$returnname = empty($returnname) ? 'subjectname' : $returnname;
		$read = $this->get_subjectlist_purview($sid);
		$read['link'] = $this->get_link('subtype', $read, $lngpack);
		$read['title'] = $value['subjectname'];
		return $read[$returnname];
	}

}

?>
