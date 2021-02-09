<?php

/*
  PHP version 5
  Copyright (c) 2002-2014 ECISP.CN、EarcLink.COM
  警告：这不是一个免费的软件，请在许可范围内使用，请尊重知识产权，侵权必究，举报有奖

  作者：黄祥云 E-mail:6326420@qq.com  QQ:6326420 TEL:18665655030
  ESPCMS官网介绍：http://www.ecisp.cn 企业建站：http://www.earclink.cn
 */

class lib_login extends connector {

	function lib_login() {
		$this->softbase();
		parent::start_pagetemplate();

		$this->pagetemplate->libfile = true;
	}

	function call_login($lng, $para, $filename = 'member_login', $outHTML = null) {
		$para = $this->fun->array_getvalue($para);
		$lngpack = $lng ? $lng : $this->CON['is_lancode'];
		$lng = ($lng == 'big5') ? $this->CON['is_lancode'] : $lng;
		include admin_ROOT . 'datacache/' . $lng . '_pack.php';

		$mlink = $this->memberlink(array(), $lng);
		$this->pagetemplate->assign('lngpack', $LANPACK);
		$this->pagetemplate->assign('mlink', $mlink);
		$this->pagetemplate->assign('seccodelink', $this->get_link('seccode'));
		$this->pagetemplate->assign('mem_isseccode', $this->CON['mem_isseccode']);

		$ec_member_username = $this->member_cookieview('username');

		$ec_member_username_id = $this->member_cookieview('userid');

		if (!empty($ec_member_username) && !empty($ec_member_username_id) && !$this->CON['is_html']) {
			$this->pagetemplate->assign('username', $ec_member_username);
			$output = $this->pagetemplate->fetch($lng . '/lib/member_info');
		} else {
			$output = $this->pagetemplate->fetch($lng . '/lib/member_login');
		}
		return $output;
	}


}

?>
