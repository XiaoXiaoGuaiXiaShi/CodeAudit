<?php

/*
  PHP version 5
  Copyright (c) 2002-2014 ECISP.CN、EarcLink.COM
  警告：这不是一个免费的软件，请在许可范围内使用，请尊重知识产权，侵权必究，举报有奖

  作者：黄祥云 E-mail:6326420@qq.com  QQ:6326420 TEL:18665655030
  ESPCMS官网介绍：http://www.ecisp.cn 企业建站：http://www.earclink.cn
 */

class lib_skin extends connector {

	function lib_skin() {
		$this->softbase();
		parent::start_pagetemplate();

		$this->pagetemplate->libfile = true;
	}

	function call_skin($lng, $para, $filename = null, $outHTML = null) {
		$para = $this->fun->array_getvalue($para);
		$lngpack = $lng ? $lng : $this->CON['is_lancode'];
		$lng = ($lng == 'big5') ? $this->CON['is_lancode'] : $lng;
		include admin_ROOT . 'datacache/' . $lng . '_pack.php';

		$pagetemplatefile = $lng . '/lib/' . $para['tempaltes'];
		if (empty($para['tempaltes'])) return null;
		$regcontent = $this->CON['mem_did'] ? explode(',', $this->CON['mem_did']) : 0;
		if (is_array($regcontent)) {
			$regtid = array();
			foreach ($regcontent as $key => $value) {
				$strReg = explode(':', $value);
				$regtid[$strReg[0]] = $strReg[1];
			}
		}
		if ($regtid[$lng]) {
			$typeread = $this->get_type($regtid[$lng]);
			$reglink = $this->get_link('type', $typeread, $lngpack);
			$this->pagetemplate->assign('reglink', $reglink);
		}

		$modelatt = $this->get_memberatt_array($lng, false);
		if (is_array($modelatt)) {
			foreach ($modelatt as $key => $value) {
				if ($value['inputtype'] == 'select' || $value['inputtype'] == 'radio') {
					foreach ($value['attrvalue'] as $key2 => $value2) {
						if ($key2 == 0) {
							$modelatt[$key]['attrvalue'][$key2]['selected'] = 'selected';
						}
					}
				} elseif ($value['inputtype'] == 'checkbox') {
					$expvale = explode(',', $rsMember[$value['attrname']]);
					foreach ($value['attrvalue'] as $key2 => $value2) {
						if (in_array($value2['name'], $expvale)) {
							$modelatt[$key]['attrvalue'][$key2]['selected'] = 'selected';
						}
					}
				}
			}
		}
		$this->pagetemplate->assign('modelatt', $modelatt);

		$this->pagetemplate->assign('tokenkey', $this->fun->token());
		$this->pagetemplate->assign('lngpack', $LANPACK);
		$this->pagetemplate->assign('mlink', $this->memberlink(array(), $lng));
		$this->pagetemplate->assign('userid', $para['userid']);
		$this->pagetemplate->assign('username', $para['username']);
		$this->pagetemplate->assign('password', $para['password']);
		$this->pagetemplate->assign('email', $para['email']);
		$this->pagetemplate->assign('isclass', $para['isclass']);
		$this->pagetemplate->assign('seccodelink', $this->get_link('seccode'));
		$this->pagetemplate->assign('mem_regisseccode', $this->CON['mem_regisseccode']);
		if (!empty($outHTML)) {
			$output = $this->pagetemplate->fetch(null, null, $outHTML);
		} else {
			$output = $this->pagetemplate->fetch($pagetemplatefile);
		}
		return $output;
	}

}

?>
