<?php

/*
  PHP version 5
  Copyright (c) 2002-2014 ECISP.CN、EarcLink.COM
  警告：这不是一个免费的软件，请在许可范围内使用，请尊重知识产权，侵权必究，举报有奖

  作者：黄祥云 E-mail:6326420@qq.com  QQ:6326420 TEL:18665655030
  ESPCMS官网介绍：http://www.ecisp.cn 企业建站：http://www.earclink.cn
 */

class lib_list extends connector {

	function lib_list() {
		$this->softbase();
		parent::start_pagetemplate();

		$this->pagetemplate->libfile = true;
	}

	function call_list($lng, $para, $filename = 'list', $outHTML = null) {
		$para = $this->fun->array_getvalue($para);
		$lngpack = $lng ? $lng : $this->CON['is_lancode'];
		$lng = ($lng == 'big5') ? $this->CON['is_lancode'] : $lng;
		include admin_ROOT . 'datacache/' . $lng . '_pack.php';

		$mid = intval($para['mid']);

		$limit = empty($para['max']) ? 20 : intval($para['max']);

		$recommend = intval($para['dlid']);

		$tid = intval($para['tid']);

		$sid = intval($para['sid']);

		$rank = intval($para['rank']);
		$rank = $rank == 2 ? 'ASC' : 'DESC';

		$sort = intval($para['sort']);
		$sort = empty($sort) ? 1 : $sort;
		switch ($sort) {
			case 1:
				$ordertype = 'did ' . $rank;
				break;
			case 2:
				$ordertype = 'pid ' . $rank;
				break;
			case 3:
				$ordertype = 'pid ' . $rank . ',did ' . $rank;
				break;
			case 4:
				$ordertype = 'click ' . $rank;
				break;
			case 5:
				$ordertype = 'addtime ' . $rank;
				break;
		}

		$linkdid = $para['linkdid'];
		if (!empty($linkdid)) {

			$str_linkid = $this->fun->format_array_text($linkdid, '/');
			if (is_array($str_linkid)) {
				$linkdid = implode(',', $str_linkid);
			}
		}

		$db_table = db_prefix . 'document';
		$db_where = ' WHERE isclass=1';

		if (!empty($mid)) {
			$db_where.=" AND mid=$mid";
		}

		if (!empty($linkdid)) {
			$db_where.=" AND did IN ($linkdid)";
		}

		if (!empty($recommend) && empty($linkdid)) {
			$db_where.=" AND FIND_IN_SET('$recommend',recommend)";
		}
		if (!empty($tid) && empty($linkdid) && !empty($mid)) {
			$db_where.=" AND " . $this->get_typeid($tid, 'tid', 0, $mid, 0, $lng);
		}
		if (!empty($sid) && empty($linkdid)) {
			$db_where.=" AND sid=$sid";
		}
		if (!empty($lng) && (empty($tid) && empty($sid) && empty($linkdid))) {
			$db_where.=" AND lng='$lng'";
		}

		$timekey = time();
		$sql = "SELECT * FROM $db_table $db_where ORDER BY $ordertype LIMIT 0,$limit";
		$rs = $this->db->query($sql);
		while ($rsList = $this->db->fetch_assoc($rs)) {
			$rsList['ctitle'] = empty($rsList['color']) ? $rsList['title'] : "<font color='" . $rsList['color'] . "'>" . $rsList['title'] . "</font>";
			$typeread = $this->get_type($rsList['tid']);
			$rsList['typename'] = $typeread['typename'];
			$rsList['typelink'] = $this->get_link('type', $typeread, $lngpack);

			$rsList['pageclass'] = $typeread['pageclass'];
			$rsList['buylink'] = $this->get_link('buylink', $rsList, $lngpack);
			$rsList['enqlink'] = $this->get_link('enqlink', $rsList, $lngpack);
			$rsList['link'] = $this->get_link('doc', $rsList, $lngpack);

			$nowtimekey = $timekey - $rsList['addtime'];

			$rsList['timekey'] = $nowtimekey < 86400 ? 1 : 0;

			$attr = $this->get_document_attr($rsList['did']);
			$array[] = is_array($attr) ? array_merge($attr, $rsList) : $rsList;
		}
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
