<?php

/*
  PHP version 5
  Copyright (c) 2002-2014 ECISP.CN、EarcLink.COM
  警告：这不是一个免费的软件，请在许可范围内使用，请尊重知识产权，侵权必究，举报有奖

  作者：黄祥云 E-mail:6326420@qq.com  QQ:6326420 TEL:18665655030
  ESPCMS官网介绍：http://www.ecisp.cn 企业建站：http://www.earclink.cn
 */

class lib_search extends connector {

	function lib_search() {
		$this->softbase();
		parent::start_pagetemplate();

		$this->pagetemplate->libfile = true;
	}

	function call_search($lng, $para, $filename = 'search', $outHTML = null) {
		$para = $this->fun->array_getvalue($para);
		$lngpack = $lng ? $lng : $this->CON['is_lancode'];
		$lng = ($lng == 'big5') ? $this->CON['is_lancode'] : $lng;
		include admin_ROOT . 'datacache/' . $lng . '_pack.php';
		$link = $this->get_link('search', array(), $lngpack);

		$mid = intval($para['mid']);
		$mid = empty($mid) ? 0 : $mid;
		$tid = intval($para['tid']);
		$tid = empty($tid) ? 0 : $tid;

		$att = intval($para['att']);
		$att = empty($att) ? 0 : $att;

		if (!$mid) {
			$modelarray = $this->get_model(0, $lng, 1, 1);
			$this->pagetemplate->assign('modelarray', $modelarray['list']);
		}

		if ($att) {
			$modelatt = $this->get_modelattArray($mid, false);
			if (is_array($modelatt)) {
				$searchatt = array();
				foreach ($modelatt as $key => $value) {
					if ($value['issearch']) {
						if ($value['inputtype'] == 'select' || $value['inputtype'] == 'radio') {
							foreach ($value['attrvalue'] as $key2 => $value2) {
								if ($docview[$value['attrname']] == $value2['name']) {
									$modelatt[$key]['attrvalue'][$key2]['selected'] = 'selected';
								}
							}
						} elseif ($value['inputtype'] == 'checkbox') {
							$expvale = explode(',', $docview[$value['attrname']]);
							foreach ($value['attrvalue'] as $key2 => $value2) {
								if (in_array($value2['name'], $expvale)) {
									$modelatt[$key]['attrvalue'][$key2]['selected'] = 'selected';
								}
							}
						} else {
							$modelatt[$key]['attrvalue'] = $docview[$value['attrname']];
						}
						$searchatt[] = $modelatt[$key];
					}
				}
			}
		}

		if ($tid) {
			$typearray = $this->get_typelist(array(), 0, $tid, 0, $lng, 0, 1);
			$now_level = $typearray[$tid]['level'];
			unset($typearray[$tid]);

			if ($typeview['upid'] > 0) {

				foreach ($typearray as $key => $value) {
					$typearray[$key]['level'] = $value['level'] - $now_level;
				}
			}
			if (count($typearray) > 0 && is_array($typearray)) {
				foreach ($typearray as $key => $value) {
					$typelist[] = $value;
				}
			}
			$this->pagetemplate->assign('array', $typelist);
		}

		$this->pagetemplate->assign('link', $link);
		$this->pagetemplate->assign('searchatt', $searchatt);
		$this->pagetemplate->assign('lng', $lng);
		$this->pagetemplate->assign('lngpack', $LANPACK);
		$this->pagetemplate->assign('mid', $mid);
		$this->pagetemplate->assign('tid', $tid);
		if (!empty($outHTML)) {
			$output = $this->pagetemplate->fetch(null, null, $outHTML);
		} else {
			$output = $this->pagetemplate->fetch($lng . '/lib/' . $filename);
		}
		return $output;
	}


}

?>
