<?php

/*
  PHP version 5
  Copyright (c) 2002-2014 ECISP.CN、EarcLink.COM
  警告：这不是一个免费的软件，请在许可范围内使用，请尊重知识产权，侵权必究，举报有奖

  作者：黄祥云 E-mail:6326420@qq.com  QQ:6326420 TEL:18665655030
  ESPCMS官网介绍：http://www.ecisp.cn 企业建站：http://www.earclink.cn
 */

class lib_messlist extends connector {

	function lib_messlist() {
		$this->softbase();
		parent::start_pagetemplate();

		$this->pagetemplate->libfile = true;
	}

	function call_messlist($lng, $para, $filename = 'messlist', $outHTML = null) {
		$para = $this->fun->array_getvalue($para);
		$lngpack = $lng ? $lng : $this->CON['is_lancode'];
		$lng = ($lng == 'big5') ? $this->CON['is_lancode'] : $lng;
		include admin_ROOT . 'datacache/' . $lng . '_pack.php';

		$did = intval($para['did']);
		$ismess = intval($para['ismess']);
		$ismess = empty($ismess) ? 0 : $ismess;

		$mid = intval($para['mid']);
		$mid = empty($mid) ? 0 : $mid;

		if (!$did || !$ismess || !$mid) return false;

		$model_ismessage = $this->get_modelview($mid, 'ismessage');
		if (!$model_ismessage) return false;

		$limit = intval($para['max']) ? intval($para['max']) : 5;








		$read['did'] = $did;
		$link = $this->get_link('messlist', $read, $lngpack);
		$messform = $this->get_link('messform', $read, $lngpack);

		$ec_member_username = $this->member_cookieview('username');
		if ($ec_member_username) {
			$reMem = $this->get_member($ec_member_username);
			$this->pagetemplate->assign('member', $reMem);
		}
		$this->pagetemplate->assign('ajaxurl', $this->get_link('ajaxurl', array(), $lngpack));
		$this->pagetemplate->assign('seccodelink', $this->get_link('seccode'));
		$this->pagetemplate->assign('bbs_isseccode', $this->CON['bbs_isseccode']);
		$this->pagetemplate->assign('link', $link);
		$this->pagetemplate->assign('messform', $messform);

		$this->pagetemplate->assign('did', $did);
		$this->pagetemplate->assign('ismess', $ismess);
		$this->pagetemplate->assign('max', $limit);
		$this->pagetemplate->assign('num', $countnum);
		$this->pagetemplate->assign('lng', $lng);
		$this->pagetemplate->assign('lngpack', $LANPACK);
		$this->pagetemplate->assign('tokenkey', $this->fun->token());
		if (!empty($outHTML)) {
			$output = $this->pagetemplate->fetch(null, null, $outHTML);
		} else {
			$output = $this->pagetemplate->fetch($lng . '/lib/' . $filename);
		}
		return $output;
	}


}

?>
