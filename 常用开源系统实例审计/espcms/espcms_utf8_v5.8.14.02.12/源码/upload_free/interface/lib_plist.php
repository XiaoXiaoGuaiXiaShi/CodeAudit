<?php

/*
  PHP version 5
  Copyright (c) 2002-2014 ECISP.CN、EarcLink.COM
  警告：这不是一个免费的软件，请在许可范围内使用，请尊重知识产权，侵权必究，举报有奖

  作者：黄祥云 E-mail:6326420@qq.com  QQ:6326420 TEL:18665655030
  ESPCMS官网介绍：http://www.ecisp.cn 企业建站：http://www.earclink.cn
 */

class lib_plist extends connector {

	function lib_plist() {
		$this->softbase();
		parent::start_pagetemplate();

		$this->pagetemplate->libfile = true;
	}

	function call_plist($lng, $para, $filename = 'plist', $outHTML = null) {
		$para = $this->fun->array_getvalue($para);
		$lngpack = $lng ? $lng : $this->CON['is_lancode'];
		$lng = ($lng == 'big5') ? $this->CON['is_lancode'] : $lng;
		include admin_ROOT . 'datacache/' . $lng . '_pack.php';

		$did = intval($para['did']);
		if (empty($did)) {
			return false;
		}

		$class = intval($para['class']);
		$class = empty($class) ? 0 : $class;

		$db_table = db_prefix . 'document';

		$read = $this->get_documentview($did);
		if (!$read['tid']) {
			return false;
		}
		if ($class) {
			$sql = "SELECT * FROM $db_table WHERE isclass=1 AND tid = $read[tid] AND did > $did ORDER BY did ASC LIMIT 0,1";
		} else {
			$sql = "SELECT * FROM $db_table WHERE isclass=1 AND tid = $read[tid] AND did < $did ORDER BY did DESC LIMIT 0,1";
		}
		$rslist = $this->db->fetch_first($sql);
		if (is_array($rslist)) {
			$typeread = $this->get_type($rslist['tid']);
			$rslist['typename'] = $typeread['typename'];
			$rslist['typelink'] = $this->get_link('type', $typeread, $lngpack);
			$rslist['pageclass'] = $typeread['pageclass'];

			$rslist['link'] = $this->get_link('doc', $rslist, $lngpack);
			$rslist['buylink'] = $this->get_link('buylink', $rslist, $lngpack);
			$rsList['enqlink'] = $this->get_link('enqlink', $rsList, $lngpack);
			$rslist['ctitle'] = empty($rslist['color']) ? $rslist['title'] : "<font color='" . $rslist['color'] . "'>" . $rslist['title'] . "</font>";
		}
		$this->pagetemplate->assign('read', $rslist);
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
