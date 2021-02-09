<?php

/*
  PHP version 5
  Copyright (c) 2002-2014 ECISP.CN、EarcLink.COM
  警告：这不是一个免费的软件，请在许可范围内使用，请尊重知识产权，侵权必究，举报有奖

  作者：黄祥云 E-mail:6326420@qq.com  QQ:6326420 TEL:18665655030
  ESPCMS官网介绍：http://www.ecisp.cn 企业建站：http://www.earclink.cn
 */

class lib_menu extends connector {

	function lib_menu() {
		$this->softbase();
		parent::start_pagetemplate();

		$this->pagetemplate->libfile = true;
	}

	function call_menu($lng, $para, $filename = 'menu', $outHTML = null) {
		$para = $this->fun->array_getvalue($para);

		$lngpack = $lng ? $lng : $this->CON['is_lancode'];

		$lng = ($lng == 'big5') ? $this->CON['is_lancode'] : $lng;
		include admin_ROOT . 'datacache/' . $lng . '_pack.php';

		$db_table = db_prefix . 'typelist';
		$db_table2 = db_prefix . 'form_group';
		$db_table3 = db_prefix . 'bbs_typelist';
		$path = empty($para['path']) ? 0 : $para['path'];
		$current = empty($para['current']) ? 0 : $para['current'];

		$chacherray = array();
		$db_where = " WHERE lng='$lng' AND ismenu=1 AND isclass=1";
		$sql = 'SELECT * FROM ' . $db_table . $db_where . ' ORDER BY pid,tid';
		$chacherray = $this->dbcache->checkcache('typelist_array_' . $lng, false);
		if (!$chacherray) {
			$rs = $this->db->query($sql);
			while ($rsList = $this->db->fetch_assoc($rs)) {
				$rsList['title'] = $rsList['typename'];
				$rsList['path'] = 'article';

				$rsList['current'] = $rsList['tid'];
				$rsList['link'] = $this->get_link('type', $rsList, $lngpack);
				$levelArray = $this->get_type_array(0, 0, $rsList['tid'], $rsList['lng']);
				if (is_array($levelArray['list'])) {
					foreach ($levelArray['list'] as $key => $value) {
						$levelArray['list'][$key]['title'] = $value['typename'];
						$levelArray['list'][$key]['path'] = 'article';
						$levelArray['list'][$key]['current'] = $value['tid'];
						$levelArray['list'][$key]['link'] = $this->get_link('type', $value, $lngpack);
					}
				}
				$rsList['larray'] = $levelArray['list'];
				$array[] = $rsList;
			}
			$chacherray = $this->dbcache->cachesave('typelist_array_' . $lng, $array);
			$chacherray = $chacherray ? $chacherray : $array;
			unset($array);
		}
		$chacherray = is_array($chacherray) ? $chacherray : array();

		$formarray = array();
		$db_where = " WHERE lng='$lng' AND ismenu=1 AND isclass=1";
		$sql = 'SELECT * FROM ' . $db_table2 . $db_where . ' ORDER BY pid,fgid DESC';
		$formarray = $this->dbcache->checkcache('formgroup_array_' . $lng, false);
		if (!$formarray) {
			$rs = $this->db->query($sql);
			while ($rsList = $this->db->fetch_assoc($rs)) {
				$rsList['title'] = $rsList['formgroupname'];
				$rsList['path'] = 'form';
				$rsList['current'] = $rsList['fgid'];
				$rsList['link'] = $this->get_link('form', $rsList, $lngpack);
				$array[] = $rsList;
			}
			$formarray = $this->dbcache->cachesave('formgroup_array_' . $lng, $array);
			$formarray = $formarray ? $formarray : $array;
			unset($array);
		}
		$formarray = is_array($formarray) ? $formarray : array();

		$forumlink = array();
		$db_where = " WHERE lng='$lng' AND ismenu=1 AND isclass=1";
		$sql = 'SELECT * FROM ' . $db_table3 . $db_where . ' ORDER BY pid,btid DESC';
		$forumlink = $this->dbcache->checkcache('forum_array_' . $lng, false);
		if (!$forumlink) {
			$rs = $this->db->query($sql);
			while ($rsList = $this->db->fetch_assoc($rs)) {
				$rsList['title'] = $rsList['typename'];
				$rsList['path'] = 'forum';
				$rsList['current'] = $rsList['btid'];
				$rsList['link'] = $this->get_link('forum', $rsList, $lngpack);
				$array[] = $rsList;
			}
			$forumlink = $this->dbcache->cachesave('forum_array_' . $lng, $array);
			$forumlink = $forumlink ? $forumlink : $array;
			unset($array);
		}
		$forumlink = is_array($forumlink) ? $forumlink : array();

		$memberlink = array();
		if ($this->CON['member_menu']) {
			$memberlink[0]['title'] = $LANPACK['membertitle'];
			$memberlink[0]['path'] = 'member';
			$memberlink[0]['current'] = 0;
			$memberlink[0]['link'] = $this->get_link('memberlogin', array(), $lngpack);
		}

		$orderlink = array();
		if ($this->CON['order_menu']) {
			$orderlink[0]['title'] = $LANPACK['ordertitle'];
			$orderlink[0]['path'] = 'order';
			$orderlink[0]['current'] = 0;
			$orderlink[0]['link'] = $this->get_link('order', array(), $lngpack);
		}

		$enquirylink = array();
		if ($this->CON['enquiry_menu']) {
			$enquirylink[0]['title'] = $LANPACK['enquirytitle'];
			$enquirylink[0]['path'] = 'enquiry';
			$enquirylink[0]['current'] = 0;
			$enquirylink[0]['link'] = $this->get_link('enquiry', array(), $lngpack);
		}
		$menuarray = array();
		if (is_array($chacherray) && is_array($formarray)) {
			$menuarray = array_merge_recursive($chacherray, $formarray);
		} else {
			$menuarray = is_array($chacherray) ? $chacherray : $menuarray;
		}

		$homelink = array();
		$homelink[0]['title'] = $LANPACK['hometitle'];
		$homelink[0]['path'] = 'index';
		$homelink[0]['current'] = 0;
		$homelink[0]['link'] = $this->get_link('home', array(), $lngpack);
		$menuarray = array_merge_recursive($homelink, $menuarray, $forumlink, $memberlink, $orderlink, $enquirylink);

		$this->pagetemplate->assign('array', $menuarray);
		$this->pagetemplate->assign('lng', $lng);
		$this->pagetemplate->assign('lngpack', $LANPACK);
		$this->pagetemplate->assign('current', $current);
		$this->pagetemplate->assign('path', $path);

		if (!empty($outHTML)) {
			$output = $this->pagetemplate->fetch(null, null, $outHTML);
		} else {
			$output = $this->pagetemplate->fetch($lng . '/lib/' . $filename);
		}
		return $output;
	}

}

?>
