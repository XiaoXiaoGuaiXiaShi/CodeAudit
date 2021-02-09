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

	function onsmssendlist() {
		parent::start_template();

		$MinPageid = intval($this->fun->accept('MinPageid', 'R'));

		$page_id = intval($this->fun->accept('page_id', 'R'));

		$countnum = intval($this->fun->accept('countnum', 'R'));

		$MaxPerPage = intval($this->fun->accept('MaxPerPage', 'R'));
		if (empty($MaxPerPage)) {
			$MaxPerPage = $this->CON['max_list'];
		}

		$isclass = intval($this->fun->accept('isclass', 'R'));
		if (!empty($isclass)) {
			if ($isclass == 2) $isclass = 0;
			$db_where.=' AND isclass=' . $isclass;
		}

		$limitkey = $this->fun->accept('limitkey', 'R');
		$limitkey = empty($limitkey) ? 'smsid' : $limitkey;

		$limitclass = $this->fun->accept('limitclass', 'R');
		$limitclass = empty($limitclass) ? 'DESC' : $limitclass;

		$db_where = $this->db->wherestr($db_where);

		$db_table = db_prefix . 'smssendlist';
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
		$this->ectemplates->display('mobliemain/sms_send_list');
	}

	function onsmssendadd() {
		parent::start_template();
		$tab = $this->fun->accept('tab', 'R');
		$tab = empty($tab) ? 'true' : $tab;

		$input_default = $this->CON;

		$typearray = $this->get_moblie_type_array();
		$this->ectemplates->assign('typelist', $typearray['list']);

		$this->ectemplates->assign('defaultinput', $input_default);
		$this->ectemplates->assign('path', admin_URL);
		$this->ectemplates->assign('tab', $tab);
		$this->ectemplates->display('mobliemain/sms_send_add');
	}

	function onsmssendedit() {
		parent::start_template();
		$tab = $this->fun->accept('tab', 'R');
		$tab = empty($tab) ? 'true' : $tab;

		$type = $this->fun->accept('type', 'R');
		$type = empty($type) ? 'edit' : $type;
		$smsid = intval($this->fun->accept('smsid', 'R'));
		if (empty($smsid)) exit('false');

		$db_table = db_prefix . 'smssendlist';
		$db_where = 'smsid=' . $smsid;
		$read = $this->db->fetch_first('SELECT * FROM ' . $db_table . ' WHERE ' . $db_where);

		$typearray = $this->get_moblie_type_array($read['mobtid']);
		$this->ectemplates->assign('typelist', $typearray['list']);

		$this->ectemplates->assign('read', $read);
		$this->ectemplates->assign('path', admin_URL);
		$this->ectemplates->assign('type', $type);
		$this->ectemplates->assign('tab', $tab);
		if ($type == 'edit') {
			$this->ectemplates->display('mobliemain/sms_send_edit');
		} elseif ($type == 'send') {
			$moblielist_array = explode(',', $read['moblielist']);
			$sendhow = count($moblielist_array);
			$this->ectemplates->assign('sendhow', $sendhow);
			$this->ectemplates->display('mobliemain/sms_send_away');
		}
	}

	function oninfosave() {
		$inputclass = $this->fun->accept('inputclass', 'P');
		$moblielist = $this->fun->accept('moblielist', 'P');
		$content = trim(strip_tags($this->fun->accept('content', 'P')));
		if (empty($content)) exit('false');
		$issendtype = intval($this->fun->accept('issendtype', 'P'));
		$mobtid = $this->fun->accept('mobtid', 'P');

		if ($issendtype == 2) {
			$moblielist_array = explode(',', $moblielist);
			$moblielist = null;
			if (is_array($moblielist_array)) {
				$moblielist_array = array_unique($moblielist_array);
				foreach ($moblielist_array as $key => $value) {
					if (preg_match("/^1[0-9]{10}$/i", $value)) {
						$moblielist.=$value . ',';
					}
				}
				if (empty($moblielist)) exit('false');
				$moblielist = substr($moblielist, 0, strlen($moblielist) - 1);
			} else {
				exit('false');
			}
		}

		if ($issendtype == 1) {
			if ($mobtid <= 0) exit('false');
			$moblielist = null;
			$db_where = " WHERE isclass=1 AND mobtid=$mobtid";
			$db_table = db_prefix . 'moblie_list';
			$sql = 'SELECT moblid,moblienb FROM ' . $db_table . $db_where;
			$rs = $this->db->query($sql);
			while ($rsList = $this->db->fetch_assoc($rs)) {
				if (preg_match("/^1[0-9]{10}$/i", $rsList['moblienb'])) {
					$moblielist.=$rsList['moblienb'] . ',';
				}
			}
			if (empty($moblielist)) exit('false');
			$moblielist = substr($moblielist, 0, strlen($moblielist) - 1);
		}

		$time = time();
		$db_table = db_prefix . 'smssendlist';
		if ($inputclass == 'add') {
			$db_field = 'content,moblielist,issendtype,mobtid,sendhow,sendtime,isclass,addtime';
			$db_values = "'$content','$moblielist','$issendtype','$mobtid',0,0,1,$time";
			$this->db->query('INSERT INTO ' . $db_table . ' (' . $db_field . ') VALUES (' . $db_values . ')');
			$this->writelog($this->lng['moblietypemain_send_send_log'], $this->lng['log_extra_ok']);
			exit('true');
		} elseif ($inputclass == 'edit') {
			$smsid = $this->fun->accept('smsid', 'P');
			if (empty($smsid)) exit('false');
			$db_where = 'smsid=' . $smsid;
			$db_set = "moblielist='$moblielist',content='$content'";
			$this->db->query('UPDATE ' . $db_table . ' SET ' . $db_set . ' WHERE ' . $db_where);
			$this->writelog($this->lng['moblietypemain_send_edit_log'], $this->lng['log_extra_ok'] . ' id=' . $smsid);
			exit('true');
		}
	}

	function onsmssenddel() {
		$db_table = db_prefix . 'smssendlist';
		$selectinfoid = $this->fun->accept('selectinfoid', 'P');
		if (empty($selectinfoid)) exit('false');
		$infoarray = explode(',', $selectinfoid);
		$count = count($infoarray) - 1;
		if ($count <= 0) exit('false');
		for ($i = 0; $i < $count; $i++) {
			$db_where = "smsid=$infoarray[$i]";
			$this->db->query('DELETE FROM ' . $db_table . ' WHERE ' . $db_where);
		}
		$this->writelog($this->lng['moblietypemain_send_del_log'], $this->lng['log_extra_ok'] . ' id=' . $selectinfoid);
		exit('true');
	}

	function onsetting() {
		$db_table = db_prefix . 'smssendlist';
		$selectinfoid = $this->fun->accept('selectinfoid', 'P');
		$selectinfoid = $selectinfoid . '0';
		if (empty($selectinfoid)) exit('false');
		$value = $this->fun->accept('value', 'P');
		$dbname = $this->fun->accept('dbname', 'P');
		$db_set = "$dbname=$value";
		$db_where = "smsid IN ( $selectinfoid )";
		$this->db->query('UPDATE ' . $db_table . ' SET ' . $db_set . ' WHERE ' . $db_where);
		$this->writelog($this->lng['moblietypemain_send_edit_log'], $this->lng['log_extra_ok'] . ' id=' . $selectinfoid);
		exit('true');
	}

	function onsendsave() {
		$time = time();
		$inputclass = $this->fun->accept('inputclass', 'P');
		$smsid = $this->fun->accept('smsid', 'P');
		if (empty($smsid)) exit('false');
		$db_table = db_prefix . 'smssendlist';
		$db_where = 'isclass=1 AND smsid=' . $smsid;
		$read = $this->db->fetch_first('SELECT * FROM ' . $db_table . ' WHERE ' . $db_where);

		if ($this->CON['is_moblie']) {
			$this->sendsmsaway($read['content'], $read['moblielist'], 1);
		} else {
			exit('false');
		}
		$time = time();
		$db_set = "sendtime=$time";
		$this->db->query('UPDATE ' . $db_table . ' SET ' . $db_set . ' WHERE ' . $db_where);

		$this->writelog($this->lng['moblietypemain_send_sendlist_log'], $this->lng['log_extra_ok'] . ' id=' . $smsid);
		exit('true');
	}

}

?>