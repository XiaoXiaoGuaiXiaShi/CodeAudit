<?php

/*
  PHP version 5
  Copyright (c) 2002-2014 ECISP.CN、EarcLink.COM
  警告：这不是一个免费的软件，请在许可范围内使用，请尊重知识产权，侵权必究，举报有奖

  作者：黄祥云 E-mail:6326420@qq.com  QQ:6326420 TEL:18665655030
  ESPCMS官网介绍：http://www.ecisp.cn 企业建站：http://www.earclink.cn
 */

class important extends connector {

	function important() {
		$this->softbase(true);
	}

	function onmoblietypelist() {
		parent::start_template();

		$MinPageid = intval($this->fun->accept('MinPageid', 'R'));

		$page_id = intval($this->fun->accept('page_id', 'R'));

		$countnum = intval($this->fun->accept('countnum', 'R'));

		$MaxPerPage = intval($this->fun->accept('MaxPerPage', 'R'));
		if (empty($MaxPerPage)) {
			$MaxPerPage = $this->CON['max_list'];
		}

		$lng = $this->sitelng;
		$lng = empty($lng) ? ($this->CON['is_alonelng'] && !empty($this->CON['home_lng'])) ? $this->CON['home_lng'] : $this->CON['default_lng']  : $lng;

		$isclass = intval($this->fun->accept('isclass', 'R'));
		if (!empty($isclass)) {
			if ($isclass == 2) $isclass = 0;
			$db_where.=' AND isclass=' . $isclass;
		}

		$limitkey = $this->fun->accept('limitkey', 'R');
		$limitkey = empty($limitkey) ? 'mobtid' : $limitkey;

		$limitclass = $this->fun->accept('limitclass', 'R');
		$limitclass = empty($limitclass) ? 'DESC' : $limitclass;

		$db_where = $this->db->wherestr($db_where);


		$db_table = db_prefix . 'moblie_type';
		if (!empty($countnum)) {
			$countnum = $this->db_numrows($db_table, $db_where);
			exit($countnum);
		}
		$sql = 'SELECT * FROM ' . $db_table . $db_where . ' ORDER BY ' . $limitkey . ' ' . $limitclass . ' LIMIT ' . $MinPageid . ',' . $MaxPerPage;
		$rs = $this->db->query($sql);
		while ($rsList = $this->db->fetch_assoc($rs)) {
			$array[] = $rsList;
		}
		$this->fun->setcookie($this->fun->noncefile() . 'pgid', $page_id, 600);
		$this->ectemplates->assign('array', $array);
		$this->ectemplates->display('mobliemain/moblie_type_list');
	}

	function onmoblietypeadd() {
		parent::start_template();
		$tab = $this->fun->accept('tab', 'R');
		$tab = empty($tab) ? 'true' : $tab;

		$lng = $this->sitelng;
		$lng = empty($lng) ? ($this->CON['is_alonelng'] && !empty($this->CON['home_lng'])) ? $this->CON['home_lng'] : $this->CON['default_lng']  : $lng;

		$input_default = $this->CON;
		$this->ectemplates->assign('defaultinput', $input_default);
		$this->ectemplates->assign('lng', $lng);
		$this->ectemplates->assign('path', admin_URL);
		$this->ectemplates->assign('tab', $tab);
		$this->ectemplates->display('mobliemain/moblie_type_add');
	}

	function onmoblietypeedit() {
		parent::start_template();
		$tab = $this->fun->accept('tab', 'R');
		$tab = empty($tab) ? 'true' : $tab;

		$type = $this->fun->accept('type', 'R');
		$type = empty($type) ? 'edit' : $type;
		$mobtid = intval($this->fun->accept('mobtid', 'R'));
		if (empty($mobtid)) exit('false');
		$db_table = db_prefix . 'moblie_type';
		$db_where = 'mobtid=' . $mobtid;
		$read = $this->db->fetch_first('SELECT * FROM ' . $db_table . ' WHERE ' . $db_where);
		$input_default = $this->CON;
		$this->ectemplates->assign('defaultinput', $input_default);
		$this->ectemplates->assign('read', $read);
		$this->ectemplates->assign('path', admin_URL);
		$this->ectemplates->assign('type', $type);
		$this->ectemplates->assign('tab', $tab);
		$this->ectemplates->display('mobliemain/moblie_type_edit');
	}

	function oninfosave() {
		$inputclass = $this->fun->accept('inputclass', 'P');
		$lng = $this->fun->accept('lng', 'P');
		$moblietypename = $this->fun->accept('moblietypename', 'P');
		$content = $this->fun->accept('content', 'P');
		$time = time();
		$db_table = db_prefix . 'moblie_type';
		if ($inputclass == 'add') {
			$db_field = 'moblietypename,content,isclass,addtime';
			$db_values = "'$moblietypename','$content',1,$time";
			$this->db->query('INSERT INTO ' . $db_table . ' (' . $db_field . ') VALUES (' . $db_values . ')');
			$this->writelog($this->lng['moblietypemain_add_log'], $this->lng['log_extra_ok']);
			exit('true');
		} elseif ($inputclass == 'edit') {
			$mobtid = $this->fun->accept('mobtid', 'P');
			if (empty($mobtid)) exit('false');
			$db_where = 'mobtid=' . $mobtid;
			$db_set = "moblietypename='$moblietypename',content='$content'";
			$this->db->query('UPDATE ' . $db_table . ' SET ' . $db_set . ' WHERE ' . $db_where);
			$this->writelog($this->lng['moblietypemain_edit_log'], $this->lng['log_extra_ok'] . ' id=' . $mlvid);
			exit('true');
		}
	}

	function onmoblietypedel() {
		$db_table = db_prefix . 'moblie_type';
		$db_table1 = db_prefix . 'moblie_list';
		$selectinfoid = $this->fun->accept('selectinfoid', 'P');
		if (empty($selectinfoid)) exit('false');
		$infoarray = explode(',', $selectinfoid);
		$count = count($infoarray) - 1;
		if ($count <= 0) exit('false');
		for ($i = 0; $i < $count; $i++) {
			$db_where = "mobtid=$infoarray[$i]";
			$this->db->query('DELETE FROM ' . $db_table . ' WHERE ' . $db_where);
			$this->db->query('DELETE FROM ' . $db_table1 . ' WHERE ' . $db_where);
		}
		$this->writelog($this->lng['moblietypemain_del_log'], $this->lng['log_extra_ok'] . ' id=' . $selectinfoid);
		exit('true');
	}

	function onsetting() {
		$db_table = db_prefix . 'moblie_type';
		$selectinfoid = $this->fun->accept('selectinfoid', 'P');
		$selectinfoid = $selectinfoid . '0';
		if (empty($selectinfoid)) exit('false');
		$value = $this->fun->accept('value', 'P');
		$dbname = $this->fun->accept('dbname', 'P');
		$db_set = "$dbname=$value";
		$db_where = "mobtid IN ( $selectinfoid )";
		$this->db->query('UPDATE ' . $db_table . ' SET ' . $db_set . ' WHERE ' . $db_where);
		$this->writelog($this->lng['moblietypemain_setting_log'], $this->lng['log_extra_ok'] . ' id=' . $selectinfoid);
		exit('true');
	}

	function onmoblielist() {
		parent::start_template();

		$MinPageid = intval($this->fun->accept('MinPageid', 'R'));

		$page_id = intval($this->fun->accept('page_id', 'R'));

		$countnum = intval($this->fun->accept('countnum', 'R'));

		$MaxPerPage = intval($this->fun->accept('MaxPerPage', 'R'));
		if (empty($MaxPerPage)) {
			$MaxPerPage = $this->CON['max_list'];
		}
		$mobtid = $this->fun->accept('mobtid', 'R');
		if (empty($mobtid)) exit('false');
		$db_where = " WHERE mobtid=$mobtid";

		$isclass = intval($this->fun->accept('isclass', 'R'));
		if (!empty($isclass)) {
			if ($isclass == 2) $isclass = 0;
			$db_where.=' AND isclass=' . $isclass;
		}
		$limitkey = $this->fun->accept('limitkey', 'R');
		$limitkey = empty($limitkey) ? 'moblid' : $limitkey;

		$limitclass = $this->fun->accept('limitclass', 'R');
		$limitclass = empty($limitclass) ? 'DESC' : $limitclass;
		$db_table = db_prefix . 'moblie_list';
		if (!empty($countnum)) {
			$countnum = $this->db_numrows($db_table, $db_where);
			exit($countnum);
		}
		$sql = 'SELECT * FROM ' . $db_table . $db_where . ' ORDER BY ' . $limitkey . ' ' . $limitclass . ' LIMIT ' . $MinPageid . ',' . $MaxPerPage;
		$rs = $this->db->query($sql);
		while ($rsList = $this->db->fetch_assoc($rs)) {
			$array[] = $rsList;
		}
		$this->fun->setcookie($this->fun->noncefile() . 'pgid', $page_id, 600);
		$this->ectemplates->assign('array', $array);
		$this->ectemplates->display('mobliemain/moblie_list');
	}

	function onmbsetting() {
		$db_table = db_prefix . 'moblie_list';
		$selectinfoid = $this->fun->accept('selectinfoid', 'P');
		$selectinfoid = $selectinfoid . '0';
		if (empty($selectinfoid)) exit('false');
		$value = $this->fun->accept('value', 'P');
		$dbname = $this->fun->accept('dbname', 'P');
		$db_set = "$dbname=$value";
		$db_where = "moblid IN ( $selectinfoid )";
		$this->db->query('UPDATE ' . $db_table . ' SET ' . $db_set . ' WHERE ' . $db_where);
		$this->writelog($this->lng['moblietypemain_moblie_add_log3'], $this->lng['log_extra_ok'] . ' id=' . $selectinfoid);
		exit('true');
	}

	function onmobliedel() {
		$db_table = db_prefix . 'moblie_list';
		$selectinfoid = $this->fun->accept('selectinfoid', 'P');
		if (empty($selectinfoid)) exit('false');
		$infoarray = explode(',', $selectinfoid);
		$count = count($infoarray) - 1;
		if ($count <= 0) exit('false');
		for ($i = 0; $i < $count; $i++) {
			$db_where = "moblid=$infoarray[$i]";
			$this->db->query('DELETE FROM ' . $db_table . ' WHERE ' . $db_where);
		}
		$this->writelog($this->lng['moblietypemain_moblie_add_log4'], $this->lng['log_extra_ok'] . ' id=' . $selectinfoid);
		exit('true');
	}

	function onmoblieinput() {
		parent::start_template();
		$tab = $this->fun->accept('tab', 'R');
		$tab = empty($tab) ? 'true' : $tab;
		$mobtid = intval($this->fun->accept('mobtid', 'R'));
		$type = $this->fun->accept('type', 'R');
		if (empty($mobtid)) exit('false');

		$db_table = db_prefix . 'moblie_type';
		$db_where = 'mobtid=' . $mobtid;
		$read = $this->db->fetch_first('SELECT * FROM ' . $db_table . ' WHERE ' . $db_where);
		if ($type == 'member') {

			$memberpuv = $this->get_member_purview_array();
			$this->ectemplates->assign('memberpuvlist', $memberpuv['list']);
		}

		$input_default = $this->CON;
		$this->ectemplates->assign('defaultinput', $input_default);
		$this->ectemplates->assign('lng', $lng);
		$this->ectemplates->assign('read', $read);
		$this->ectemplates->assign('tab', $tab);
		$outtemplates_file = $type == 'member' ? 'mobliemain/moblie_add' : 'mobliemain/moblie_batadd';
		$this->ectemplates->display($outtemplates_file);
	}

	function onbatupinfosave() {
		$mobtid = $this->fun->accept('mobtid', 'P');
		if (empty($mobtid)) exit('false');

		$fname = $_FILES['batfile']['tmp_name'];
		if (empty($fname)) {
			exit('false');
		}
		$time = time();
		$db_table = db_prefix . 'moblie_list';

		$handle = @fopen($fname, 'r');
		for ($i = 0; $data = $this->fun->fgetcsv_reg($handle); $i++) {
			if ($i > 0) {
				$mobile = $this->fun->daddslashes($data[0], 1);
				$name = $this->fun->daddslashes($data[1], 1);
				$name = !empty($name) ? $this->fun->codeing($name, 'gu') : '';

				$db_where = " WHERE mobtid=$mobtid AND moblienb='$mobile'";
				$countnum = $this->db_numrows($db_table, $db_where);
				if ($countnum <= 0 && $mobile && preg_match("/^1[0-9]{10}$/i", $mobile)) {
					$db_field = 'mobtid,moblienb,mobliename,isclass,addtime';
					$db_values = "$mobtid,'$mobile','$name',1,$time";
					$this->db->query('INSERT INTO ' . $db_table . ' (' . $db_field . ') VALUES (' . $db_values . ')');
				}
			}
		}
		$this->writelog($this->lng['moblietypemain_moblie_add_log2'], $this->lng['log_extra_ok']);
		exit('true');
	}

	function onmobliesave() {
		$mobtid = $this->fun->accept('mobtid', 'P');
		$mcid = $this->fun->accept('mcid', 'P');
		if (empty($mobtid) || empty($mcid)) exit('false');
		$time = time();

		$db_table = db_prefix . 'member';
		$db_table2 = db_prefix . 'moblie_list';







		$db_where = " WHERE isclass=1 AND mcid=$mcid";
		$sql = 'SELECT * FROM ' . $db_table . $db_where . ' ORDER BY userid DESC';
		$rs = $this->db->query($sql);
		while ($rsList = $this->db->fetch_assoc($rs)) {
			$mobile = $rsList['mobile'];
			if ($mobile && preg_match("/^1[0-9]{10}$/i", $mobile)) {
				$db_where = " WHERE mobtid=$mobtid AND moblienb='$rsList[mobile]'";
				$countnum = $this->db_numrows($db_table2, $db_where);
				if ($countnum <= 0) {
					$db_field = 'mobtid,moblienb,mobliename,isclass,addtime';
					$mobliename = empty($rsList['alias']) ? $rsList['username'] : $this->fun->daddslashes($rsList['alias'], 1);
					$db_values = "$mobtid,'$mobile','$mobliename',1,$time";
					$this->db->query('INSERT INTO ' . $db_table2 . ' (' . $db_field . ') VALUES (' . $db_values . ')');
				}
			}
		}
		$this->writelog($this->lng['moblietypemain_moblie_add_log'] . "(MCID=$mcid)", $this->lng['log_extra_ok']);
		exit('true');
	}

	function onmoblieout() {
		$mobtid = $this->fun->accept('mobtid', 'R');
		if (empty($mobtid)) exit('false');
		header("Content-type: application/vnd.ms-excel; charset=GB2312");
		header("Content-Disposition: attachment; filename=moblieout_$mobtid.xls");
		$data = "手机号\t 姓名\t\n";
		$db_table = db_prefix . 'moblie_list';
		$db_where = " WHERE mobtid=$mobtid AND isclass=1";
		$sql = 'SELECT * FROM ' . $db_table . $db_where;
		$rs = $this->db->query($sql);
		while ($rsList = $this->db->fetch_assoc($rs)) {
			$data .= "$rsList[moblienb]\t$rsList[mobliename]\t\n";
		}
		echo $this->fun->codeing($data, 'ug');
		exit;
	}

}

?>