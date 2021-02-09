<?php

/*
  PHP version 5
  Copyright (c) 2002-2014 ECISP.CN、EarcLink.COM
  警告：这不是一个免费的软件，请在许可范围内使用，请尊重知识产权，侵权必究，举报有奖

  作者：黄祥云 E-mail:6326420@qq.com  QQ:6326420 TEL:18665655030
  ESPCMS官网介绍：http://www.ecisp.cn 企业建站：http://www.earclink.cn
 */

class lib_tags extends connector {

	function lib_tags() {
		$this->softbase();
		parent::start_pagetemplate();

		$this->pagetemplate->libfile = true;
	}

	function call_tags($lng, $para, $filename = 'tags', $outHTML = null) {
		$para = $this->fun->array_getvalue($para);
		$lngpack = $lng ? $lng : $this->CON['is_lancode'];
		$lng = ($lng == 'big5') ? $this->CON['is_lancode'] : $lng;
		include admin_ROOT . 'datacache/' . $lng . '_pack.php';
		$limit = intval($para['max']);
		$limit = empty($limit) ? 20 : $limit;

		$mid = intval($para['mid']);
		$mid = empty($mid) ? 0 : $mid;

		$tid = intval($para['tid']);

		$istop = intval($para['istop']);
		$istop = empty($istop) ? 0 : $istop;
		$db_where = ' WHERE isclass=1';
		if (!empty($tid)) {
			$db_where.=" AND tid=$tid";
		}
		if (!empty($istop)) {
			$db_where.=" AND istop=1";
		}
		if (!empty($mid) && empty($tid)) {
			$db_where.=" AND mid=$mid";
		}
		if (!empty($lng) && (empty($tid) && empty($mid))) {
			$db_where.=" AND lng='$lng'";
		}
		$db_table = db_prefix . 'keylink';
		$countnum = $this->db_numrows($db_table, $db_where);
		$sql = "SELECT * FROM $db_table $db_where ORDER BY pid,kid DESC LIMIT 0,$limit";
		$rs = $this->db->query($sql);
		while ($rsList = $this->db->fetch_assoc($rs)) {
			$rsList['num'] = $countnum;
			$rsList['link'] = $rsList['islink'] == 1 ? $rsList['linkurl'] : $this->get_link('taglink', array('key' => $rsList['keywordname']), $lngpack);
			$rsList['title'] = $rsList['keywordname'];
			$array[] = $rsList;
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
