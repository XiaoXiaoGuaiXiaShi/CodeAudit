<?php

/*
  PHP version 5
  Copyright (c) 2002-2014 ECISP.CN、EarcLink.COM
  警告：这不是一个免费的软件，请在许可范围内使用，请尊重知识产权，侵权必究，举报有奖

  作者：黄祥云 E-mail:6326420@qq.com  QQ:6326420 TEL:18665655030
  ESPCMS官网介绍：http://www.ecisp.cn 企业建站：http://www.earclink.cn
 */

class lib_form extends connector {

	function lib_form() {
		$this->softbase();
		parent::start_pagetemplate();

		$this->pagetemplate->libfile = true;
	}

	function call_form($lng, $para, $filename = 'form', $outHTML = null) {
		$para = $this->fun->array_getvalue($para);
		$lngpack = $lng ? $lng : $this->CON['is_lancode'];
		$lng = ($lng == 'big5') ? $this->CON['is_lancode'] : $lng;
		include admin_ROOT . 'datacache/' . $lng . '_pack.php';
		$fgid = intval($para['fgid']);
		if (empty($fgid)) {
			return false;
		}

		$did = intval($para['did']);
		$did = empty($did) ? 0 : $did;
		$form = $this->get_form_purview($fgid);
		$form['action'] = $this->get_link('acform', $form, $lngpack);
		$attrread = $this->get_formatt($fgid);
		$this->pagetemplate->assign('form', $form);
		$this->pagetemplate->assign('array', $attrread);

		$this->pagetemplate->assign('seccodelink', $this->get_link('seccode'));
		$this->pagetemplate->assign('lng', $lng);
		$this->pagetemplate->assign('did', $did);
		$this->pagetemplate->assign('tokenkey', $this->fun->token());
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
