<?php

/*
  PHP version 5
  Copyright (c) 2002-2013 ECISP.CN、EarcLink.COM
  警告：这不是一个免费的软件，请在许可范围内使用，请尊重知识产权，侵权必究，举报有奖！

  作者：黄祥云 E-mail:6326420@qq.com  QQ:6326420 TEL:18665655030
  ESPCMS官网介绍：http://www.ecisp.cn 企业建站：http://www.earclink.cn
 */

class important extends connector {

	function important() {
		$this->softbase(true);
	}

	function onadverttypelist() {
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
		$db_where = ' WHERE lng=\'' . $lng . '\'';
		$isclass = intval($this->fun->accept('isclass', 'R'));
		if (!empty($isclass)) {
			if ($isclass == 2) $isclass = 0;
			$db_where.=' AND isclass=' . $isclass;
		}
		$iscode = intval($this->fun->accept('iscode', 'R'));
		if (!empty($iscode)) {
			if ($iscode == 2) $iscode = 0;
			$db_where.=' AND iscode=' . $iscode;
		}

		$limitkey = $this->fun->accept('limitkey', 'R');
		$limitkey = empty($limitkey) ? 'atid' : $limitkey;

		$limitclass = $this->fun->accept('limitclass', 'R');
		$limitclass = empty($limitclass) ? 'DESC' : $limitclass;
		$db_table = db_prefix . 'advert_type';
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
		$this->ectemplates->display('advert/advert_type_list');
	}

	function onadverttypeadd() {
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
		$this->ectemplates->display('advert/advert_type_add');
	}

	function onadverttypeedit() {
		parent::start_template();
		$tab = $this->fun->accept('tab', 'R');
		$tab = empty($tab) ? 'true' : $tab;

		$type = $this->fun->accept('type', 'R');
		$type = empty($type) ? 'edit' : $type;

		$atid = intval($this->fun->accept('atid', 'R'));
		if (empty($atid)) exit('false');
		$read = $this->get_advert_type_view($atid);
		$input_default = $this->CON;
		$this->ectemplates->assign('defaultinput', $input_default);
		$this->ectemplates->assign('lng', $read['lng']);
		$this->ectemplates->assign('read', $read);
		$this->ectemplates->assign('path', admin_URL);
		$this->ectemplates->assign('type', $type);
		$this->ectemplates->assign('tab', $tab);
		$this->ectemplates->display('advert/advert_type_edit');
	}

	function oninfosave() {
		$inputclass = $this->fun->accept('inputclass', 'P');
		$lng = $this->fun->accept('lng', 'P');
		$adtypename = $this->fun->accept('adtypename', 'P');
		$width = intval($this->fun->accept('width', 'P'));
		$width = empty($width) ? 0 : $width;
		$height = intval($this->fun->accept('height', 'P'));
		$height = empty($height) ? 0 : $height;
		$content = $this->fun->accept('content', 'P');

		$isclass = $this->esp_inputclassid;
		$isclass = empty($isclass) ? 0 : $isclass;

		$isxml = intval($this->fun->accept('isxml', 'P'));
		$isxml = empty($isxml) ? 0 : $isxml;
		$xmltemplatefile = $this->fun->accept('xmltemplatefile', 'P');
		$xmlfile = $this->fun->accept('xmlfile', 'P');
		$xmlpath = $this->fun->accept('xmlpath', 'P');

		$db_table = db_prefix . 'advert_type';
		if ($inputclass == 'add') {
			$db_field = 'lng,adtypename,content,width,height,isclass,iscode,isxml,xmltemplatefile,xmlfile,xmlpath';
			$db_values = "'$lng','$adtypename','$content',$width,$height,$isclass,0,$isxml,'$xmltemplatefile','$xmlfile','$xmlpath'";
			$this->db->query('INSERT INTO ' . $db_table . ' (' . $db_field . ') VALUES (' . $db_values . ')');
			$this->writelog($this->lng['adverttypemain_add_log'], $this->lng['log_extra_ok']);
			$this->dbcache->clearcache('advert_type_array_' . $lng, true);
			exit('true');
		} elseif ($inputclass == 'edit') {
			$atid = $this->fun->accept('atid', 'P');
			if (empty($atid)) exit('false');
			$db_where = 'atid=' . $atid;
			$db_set = "adtypename='$adtypename',content='$content',width='$width',height='$height',isxml=$isxml,xmltemplatefile='$xmltemplatefile',xmlfile='$xmlfile',xmlpath='$xmlpath'";
			$this->db->query('UPDATE ' . $db_table . ' SET ' . $db_set . ' WHERE ' . $db_where);
			$this->writelog($this->lng['adverttypemain_edit_log'], $this->lng['log_extra_ok'] . ' id=' . $atid);
			$this->dbcache->clearcache('advert_type_view_' . $atid, true);
			$this->dbcache->clearcache('advert_type_array_' . $lng, true);
			exit('true');
		}
	}

	function onadverttypedel() {
		$db_table = db_prefix . 'advert_type';
		$db_table1 = db_prefix . 'advert';
		$selectinfoid = $this->fun->accept('selectinfoid', 'P');
		if (empty($selectinfoid)) exit('false');
		$infoarray = explode(',', $selectinfoid);
		$count = count($infoarray) - 1;
		if ($count <= 0) exit('false');
		for ($i = 0; $i < $count; $i++) {
			$db_where = "atid=$infoarray[$i]";
			$this->db->query('DELETE FROM ' . $db_table . ' WHERE ' . $db_where);
			$this->db->query('DELETE FROM ' . $db_table1 . ' WHERE ' . $db_where);
		}
		$this->writelog($this->lng['adverttypemain_del_log'], $this->lng['log_extra_ok'] . ' id=' . $selectinfoid);
		exit('true');
	}

	function onsetting() {
		$db_table = db_prefix . 'advert_type';
		$selectinfoid = $this->fun->accept('selectinfoid', 'P');
		$selectinfoid = $selectinfoid . '0';
		if (empty($selectinfoid)) exit('false');
		$value = $this->fun->accept('value', 'P');
		$dbname = $this->fun->accept('dbname', 'P');
		$db_set = "$dbname=$value";
		$db_where = "atid IN ( $selectinfoid )";
		$this->db->query('UPDATE ' . $db_table . ' SET ' . $db_set . ' WHERE ' . $db_where);
		$this->writelog($this->lng['adverttypemain_setting_log'], $this->lng['log_extra_ok'] . ' id=' . $selectinfoid);
		exit('true');
	}

	function onxmlcreat() {

		$lng = $this->fun->accept('lng', 'R');
		$lng_templates = $lng;
		$lng = ($lng == 'big5') ? $this->CON['is_lancode'] : $lng;
		if (empty($lng)) exit('false');
		include_once admin_ROOT . 'datacache/' . $lng . '_pack.php';
		$this->start_pagetemplate($lng_templates, $LANPACK);
		$atid = $this->fun->accept('atid', 'R');
		if (empty($atid)) exit('false');
		$read = $this->get_advert_type_view($atid);
		$this->pagetemplate->assign('read', $read);

		$templatefilename = 'xml/' . $read['xmltemplatefile'];

		$tpl_file = $this->pagetemplate->tpl_dir . $this->pagetemplate->templatesDIR . $templatefilename;
		$istpl_file = $this->pagetemplate->tpl_dir . $this->pagetemplate->templatesDIR . $templatefilename . '.xml';

		$dirpath = admin_ROOT . $read['xmlpath'];

		$filename = $dirpath . $read['xmlfile'] . '.xml';

		if ($this->fun->filemode($dirpath)) {

			if (!is_dir($dirpath)) {
				if (!@mkdir($dirpath, 0777, true)) {

					exit($read['xmlpath'] . $this->lng['createmain_creat_no_w']);
				}
			}
		} else {

			exit($read['xmlpath'] . $this->lng['createmain_creat_no_w']);
		}
		if (file_exists($istpl_file)) {
			$this->pagetemplate->display($templatefilename, $dirname . '_xml', true, $filename, $lng_templates, '.xml');
			$outkey = 'true';
		} else {
			$outkey = $read['xmlpath'] . $this->lng['createmain_creat_no_t'];
		}
		exit($outkey);
	}

}

?>