<?php

/*
  PHP version 5
  Copyright (c) 2002-2014 ECISP.CN、EarcLink.COM
  警告：这不是一个免费的软件，请在许可范围内使用，请尊重知识产权，侵权必究，举报有奖

  作者：黄祥云 E-mail:6326420@qq.com  QQ:6326420 TEL:18665655030
  ESPCMS官网介绍：http://www.ecisp.cn 企业建站：http://www.earclink.cn
 */

class lib_bbslist extends connector {

	function lib_bbslist() {
		$this->softbase();
		parent::start_pagetemplate();

		$this->pagetemplate->libfile = true;
	}

	function call_bbslist($lng, $para, $filename = 'bbslist', $outHTML = null) {
		$para = $this->fun->array_getvalue($para);
		$lngpack = $lng ? $lng : $this->CON['is_lancode'];
		$lng = ($lng == 'big5') ? $this->CON['is_lancode'] : $lng;
		include admin_ROOT . 'datacache/' . $lng . '_pack.php';

		$btid = intval($para['btid']);
		if (!$btid) {
			return null;
		}

		$limit = empty($para['max']) ? 15 : intval($para['max']);
		$db_table = db_prefix . 'bbs';
		$db_where = " WHERE upbid=0 AND isclass=1 AND btid=$btid";
		$sql = "SELECT * FROM $db_table $db_where ORDER BY bid DESC LIMIT 0,$limit";
		$rs = $this->db->query($sql);
		while ($rsList = $this->db->fetch_assoc($rs)) {
			$rsList['link'] = $this->get_link('forumread', $rsList, $lngpack);
			$array[] = $rsList;
		}
		$this->pagetemplate->assign('tokenkey', $this->fun->token());
		$this->pagetemplate->assign('array', $array);
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
