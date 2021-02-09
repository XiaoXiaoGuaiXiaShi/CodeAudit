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

	function onsmstemplatelist() {
		parent::start_template();

		$MinPageid = $this->fun->accept('MinPageid', 'R');

		$page_id = $this->fun->accept('page_id', 'R');

		$countnum = $this->fun->accept('countnum', 'R');

		$MaxPerPage = $this->fun->accept('MaxPerPage', 'R');
		if (empty($MaxPerPage)) {
			$MaxPerPage = $this->CON['max_list'];
		}

		$lng = $this->sitelng;
		$lng = empty($lng) ? ($this->CON['is_alonelng'] && !empty($this->CON['home_lng'])) ? $this->CON['home_lng'] : $this->CON['default_lng']  : $lng;
		$db_where = ' WHERE styleclass=4 and lng=\'' . $lng . '\'';

		$typeclass = $this->fun->accept('typeclass', 'R');
		if (!empty($typeclass)) {
			$db_where.=" AND typeclass='$typeclass'";
		}
		$lockin = $this->fun->accept('lockin', 'R');
		if (!empty($lockin)) {
			if ($lockin == 2) $lockin = 0;
			$db_where.=' AND lockin=' . $lockin;
		}

		$limitkey = $this->fun->accept('limitkey', 'R');
		$limitkey = empty($limitkey) ? 'tmid' : $limitkey;

		$limitclass = $this->fun->accept('limitclass', 'R');
		$limitclass = empty($limitclass) ? 'DESC' : $limitclass;
		$db_table = db_prefix . 'templates';
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
		$this->ectemplates->assign('sql', $sql);
		$this->ectemplates->display('template/smstemplate_list');
	}

	function onsmstemplateadd() {
		parent::start_template();
		$tab = $this->fun->accept('tab', 'G');
		$tab = empty($tab) ? 'true' : $tab;
		$lng = $this->sitelng;
		$lng = empty($lng) ? ($this->CON['is_alonelng'] && !empty($this->CON['home_lng'])) ? $this->CON['home_lng'] : $this->CON['default_lng']  : $lng;
		$this->ectemplates->assign('lng', $lng);
		$this->ectemplates->assign('tab', $tab);
		$this->ectemplates->display('template/smstemplate_add');
	}

	function onsmstemplateedit() {
		parent::start_template();
		$db_table = db_prefix . 'templates';
		$tmid = intval($this->fun->accept('tmid', 'G'));
		$db_where = 'tmid=' . $tmid;
		$read = $this->db->fetch_first('SELECT * FROM ' . $db_table . ' WHERE ' . $db_where);
		$this->ectemplates->assign('tab', $tab);
		$this->ectemplates->assign('read', $read);
		$this->ectemplates->display('template/smstemplate_edit');
	}

	function onsave() {
		$inputclass = $this->fun->accept('inputclass', 'P');
		$lng = $this->fun->accept('lng', 'P');
		$templatename = $this->fun->accept('templatename', 'P');
		$templatecontent = $this->fun->accept('templatecontent', 'P');
		if (empty($templatecontent)) {
			exit('false');
		}
		$templatecode = $this->fun->accept('templatecode', 'P');
		$db_table = db_prefix . 'templates';
		$date = time();
		if ($inputclass == 'add') {
			$db_where = " WHERE lng='$lng' AND templatecode='$templatecode' AND styleclass=4";
			$countnum = $this->db_numrows($db_table, $db_where);
			if ($countnum > 0) exit('false');

			$db_field = 'lng,templatename,templatecode,title,templatecontent,pic,typeclass,styleclass,lockin,addtime';
			$db_values = "'$lng','$templatename','$templatecode','$title','$templatecontent','$pic','$typeclass',4,0,$date";
			$this->db->query('INSERT INTO ' . $db_table . ' (' . $db_field . ') VALUES (' . $db_values . ')');
			$this->writelog($this->lng['smstemplatemain_add_log'], $this->lng['log_extra_ok'] . ' templatename=' . $templatename);
			$this->dbcache->clearcache('templates_array_' . $lng . '_' . $styleclass, true);
			exit('true');
		} elseif ($inputclass == 'edit') {
			$tmid = $this->fun->accept('tmid', 'P');
			$db_where = 'tmid=' . $tmid;
			$db_set = "templatename='$templatename',templatecontent='$templatecontent'";
			$this->db->query('UPDATE ' . $db_table . ' SET ' . $db_set . ' WHERE ' . $db_where);
			$this->writelog($this->lng['smstemplatemain_edit_log'], $this->lng['log_extra_ok'] . ' templatename=' . $templatename);
			$this->dbcache->clearcache('templates_view_' . $tmid, true);
			$this->dbcache->clearcache('templates_array_' . $lng . '_' . $styleclass, true);
			exit('true');
		}
	}

	function onsmstemplatedel() {
		$db_table = db_prefix . 'templates';
		$tmid = $this->fun->accept('tmid', 'R');
		if (empty($tmid)) exit('false');
		$db_where = "tmid=$tmid and lockin=0";
		$styleclass = $this->fun->accept('styleclass', 'R');
		$lng = $this->fun->accept('lng', 'R');
		if (empty($styleclass) || empty($lng)) {
			exit('false');
		}
		$this->db->query('DELETE FROM ' . $db_table . ' WHERE ' . $db_where);
		$this->writelog($this->lng['mailtemplatemain_del_log'], $this->lng['log_extra_ok'] . ' id=' . $tmid);
		$this->dbcache->clearcache('templates_view_' . $tmid, true);
		$this->dbcache->clearcache('templates_array_' . $lng . '_' . $styleclass, true);
		exit('true');
	}

	function onsetting() {
		$db_table = db_prefix . 'templates';
		$selectinfoid = $this->fun->accept('selectinfoid', 'P');
		$selectinfoid = $selectinfoid . '0';
		if (empty($selectinfoid)) exit('false');
		$value = $this->fun->accept('value', 'P');
		$dbname = $this->fun->accept('dbname', 'P');
		$db_set = "$dbname=$value";
		$db_where = "tmid IN ( $selectinfoid )";
		$this->db->query('UPDATE ' . $db_table . ' SET ' . $db_set . ' WHERE ' . $db_where);
		$this->dbcache->clearcache('templates_view_' . $tmid, true);
		$this->dbcache->clearcache('templates_array', true);

		$this->writelog($this->lng['smstemplatemain_edit_log'], $this->lng['log_extra_ok'] . ' id=' . $selectinfoid);
		exit('true');
	}

	function oncheckcode() {
		$templatecode = $this->fun->accept('templatecode', 'R');
		$typeclass = $this->fun->accept('typeclass', 'R');
		$lng = $this->fun->accept('lng', 'R');
		$db_table = db_prefix . 'templates';
		$db_where = " WHERE lng='$lng' AND templatecode='$templatecode' AND styleclass=4";
		$countnum = $this->db_numrows($db_table, $db_where);
		if ($countnum > 0) {
			$exportAjax = 'false';
		} else {
			$exportAjax = 'true';
		}
		exit($exportAjax);
	}

}

?>