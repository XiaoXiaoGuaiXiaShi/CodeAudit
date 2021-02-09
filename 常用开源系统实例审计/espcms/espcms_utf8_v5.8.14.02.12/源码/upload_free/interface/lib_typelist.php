<?php

/*
  PHP version 5
  Copyright (c) 2002-2014 ECISP.CN、EarcLink.COM
  警告：这不是一个免费的软件，请在许可范围内使用，请尊重知识产权，侵权必究，举报有奖

  作者：黄祥云 E-mail:6326420@qq.com  QQ:6326420 TEL:18665655030
  ESPCMS官网介绍：http://www.ecisp.cn 企业建站：http://www.earclink.cn
 */

class lib_typelist extends connector {

	function lib_typelist() {
		$this->softbase();
		parent::start_pagetemplate();

		$this->pagetemplate->libfile = true;
	}

	function call_typelist($lng, $para, $filename = 'typelist', $outHTML = null) {
		$para = $this->fun->array_getvalue($para);
		$lngpack = $lng ? $lng : $this->CON['is_lancode'];
		$lng = ($lng == 'big5') ? $this->CON['is_lancode'] : $lng;
		include admin_ROOT . 'datacache/' . $lng . '_pack.php';

		$tid = intval($para['utid']);

		$now_tid = intval($para['tid']);
		$level = intval($para['level']);
		$level = empty($level) ? 0 : $level;
		if (empty($tid)) {

			$tid = !empty($now_tid) ? $now_tid : $tid;
			if (empty($tid)) {
				return false;
			}
		}
		$tid = empty($tid) ? 0 : $tid;
		$db_table = db_prefix . 'typelist';
		$typeview = $this->get_type($tid);
		$typearray = $this->get_typelist(array(), 0, $tid, $now_tid, $typeview['lng'], $level, 1);
		$now_level = $typearray[$tid]['level'];
		unset($typearray[$tid]);

		if ($typeview['upid'] > 0) {

			foreach ($typearray as $key => $value) {
				$typearray[$key]['level'] = $value['level'] - $now_level;
			}
		}
		if (count($typearray) > 0 && is_array($typearray)) {
			foreach ($typearray as $key => $value) {
				$value['link'] = $this->get_link('type', $value, $lngpack);
				$value['rsslink'] = $this->get_link('typerss', $value, $lngpack);
				$typelist[] = $value;
			}
		}
		$this->pagetemplate->assign('nowtid', $now_tid);
		$this->pagetemplate->assign('array', $typelist);
		$this->pagetemplate->assign('lng', $lng);
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
