<?php

/*
  PHP version 5
  Copyright (c) 2002-2014 ECISP.CN、EarcLink.COM
  警告：这不是一个免费的软件，请在许可范围内使用，请尊重知识产权，侵权必究，举报有奖

  作者：黄祥云 E-mail:6326420@qq.com  QQ:6326420 TEL:18665655030
  ESPCMS官网介绍：http://www.ecisp.cn 企业建站：http://www.earclink.cn
 */

class lib_bread extends connector {

	function lib_bread() {
		$this->softbase();
		parent::start_pagetemplate();

		$this->pagetemplate->libfile = true;
	}

	function call_bread($lng, $para, $filename = 'read', $outHTML = null) {
		$para = $this->fun->array_getvalue($para);
		$lngpack = $lng ? $lng : $this->CON['is_lancode'];
		$lng = ($lng == 'big5') ? $this->CON['is_lancode'] : $lng;
		include admin_ROOT . 'datacache/' . $lng . '_pack.php';

		$tid = intval($para['tid']);
		if (empty($tid)) {
			return false;
		}

		$typeread = $this->get_type($tid);
		if ($typeread['gotoid'] > 0) {

			$gotodid = $this->get_type($typeread['gotoid'], 'linkid');
			if ($gotodid) {

				$typeread['linkid'] = $gotodid;

				$typeread['nowtid'] = $typeread['tid'];
			}
		}
		$did = $typeread['linkid'];

		$read = $this->get_document($did);

		if ($typeread['nowtid']) {
			$read['tid'] = $typeread['nowtid'];
		}

		$read['link'] = $this->get_link('doc', $read, $lngpack);
		$read['buylink'] = $this->get_link('buylink', $read, $lngpack);
		$read['enqlink'] = $this->get_link('enqlink', $read, $lngpack);
		$read['ctitle'] = empty($read['color']) ? $read['title'] : "<font color='" . $read['color'] . "'>" . $read['title'] . "</font>";
		$read['content'] = html_entity_decode($read['content']);
		$docarray = array();
		$attr = $this->get_document_attr($rsList['did']);
		$docarray = is_array($attr) ? array_merge($attr, $read) : $read;
		unset($read);

		$this->pagetemplate->assign('read', $docarray);
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
