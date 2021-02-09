<?php

/*
  PHP version 5
  Copyright (c) 2002-2013 ECISP.CN、EarcLink.COM
  警告：这不是一个免费的软件，请在许可范围内使用，请尊重知识产权，侵权必究，举报有奖！

  作者：黄祥云 E-mail:6326420@qq.com  QQ:6326420 TEL:18665655030
  ESPCMS官网介绍：http://www.ecisp.cn 企业建站：http://www.earclink.cn
 */

class mainpage extends connector {

	function mainpage() {
		$this->softbase(false);
		$this->mlink = $this->memberlink(array(), admin_LNG);
	}

	function in_index() {
		parent::start_pagetemplate();
		$lng = (admin_LNG == 'big5') ? $this->CON['is_lancode'] : admin_LNG;
		if ($this->CON['is_html']) {
			$lng_dir = $this->get_lng_dirpack(admin_LNG);
			if ($this->CON['is_alonelng']) {
				$index_file = admin_ROOT . $this->CON['file_htmldir'] . $this->CON['entrance_file'] . '.' . $this->CON['file_fileex'];
				$urlfile = admin_URL . $this->CON['file_htmldir'];
				$strfile = $this->CON['file_htmldir'] . $this->CON['entrance_file'] . '.' . $this->CON['file_fileex'];
			} else {
				$index_file = admin_ROOT . $this->CON['file_htmldir'] . $lng_dir . '/' . $this->CON['entrance_file'] . '.' . $this->CON['file_fileex'];
				$urlfile = admin_URL . $this->CON['file_htmldir'] . $lng_dir . '/';
				$strfile = $this->CON['file_htmldir'] . $lng_dir . '/' . $this->CON['entrance_file'] . '.' . $this->CON['file_fileex'];
			}
			if (is_file($index_file)) {
				header("HTTP/1.1 301 Moved Permanently");
				header('location: ' . $urlfile);
				exit;
			} else {
				header('Content-type: text/html; charset=utf-8');
				exit('错误：HTML静态网页不存在，请登陆后台进行静态页面生成!<br>Filename : ' . $strfile . '<br><a href="' . admin_URL . adminfile . '">点陆登陆管理平台!</a>');
			}
		}
		$this->pagetemplate->assign('id', 5);
		$this->pagetemplate->assign('mlink', $this->mlink);
		$this->pagetemplate->assign('path', 'index');
		$this->pagetemplate->display(admin_LNGDIR . 'index', 'index', false, null, admin_LNG);
	}

	function in_seccodelist() {
		$seccode = rand(100000, 999999);
		$this->fun->setcookie('ecisp_home_seccode', $this->fun->eccode($seccode . "\t" . time(), 'ENCODE'));
		@header("Expires: -1");
		@header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
		@header("Pragma: no-cache");
		include_once admin_ROOT . 'public/class_seccode.php';
		$code = new seccode();
		$code->code = $seccode;
		$code->type = 0;
		$code->width = 70;
		$code->height = 23;
		$code->background = 30;

		$code->adulterate = $this->CON['scode_adulterate'];

		$code->ttf = 0;
		$code->angle = 0;

		$code->color = 0;
		$code->size = 1;

		$code->shadow = $this->CON['scode_shadow'];
		$code->animator = 0;

		$code->bgcolor = $this->CON['scode_bgcolor'];

		$code->fontcolor = $this->CON['scode_fontcolor'];

		$code->datapath = admin_ROOT . 'datacache/';
		$code->includepath = '';
		$code->display();
	}

	function in_citylist() {
		$parentid = $this->fun->accept('parentid', 'R');
		$parentid = empty($parentid) ? 1 : intval($parentid);
		$verid = $this->fun->accept('verid', 'R');
		$verid = empty($verid) ? 0 : intval($verid);
		$db_table = db_prefix . 'city';
		$sql = "SELECT * FROM $db_table WHERE parentid=$parentid";
		$rs = $this->db->query($sql);
		while ($rsList = $this->db->fetch_array($rs)) {
			$citylist[] = $rsList;
		}

		foreach ($citylist as $key => $val) {
			if ($verid == $val['id']) {
				$list.='<option selected value="' . $val['id'] . '">' . $val['cityname'] . '</option>';
			} else {
				$list.='<option value="' . $val['id'] . '">' . $val['cityname'] . '</option>';
			}
		}
		if (admin_LNG == 'big5') {
			$list = $this->fun->codeing($list, 'gb');
		}
		exit($list);
	}

	function in_checkusername() {
		$username = $this->fun->accept('username', 'R');
		$email = $this->fun->accept('email', 'R');

		if (!preg_match("/^[^!@~`\'\"#\$\%\^&\*\(\)\+\-\{\}\[\]\|\\/\?\<\>\,\.\:\;]{2,16}$/i", $username) && !empty($username)) {
			exit('false');
		} elseif (!preg_match("/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/i", $email) && !empty($email)) {
			exit('false');
		}
		if ($this->CON['mem_isucenter']) {
			include_once admin_ROOT . 'public/uc_client/client.php';
			if (!empty($username)) {

				$data = uc_get_user($username);
				if ($data) {
					exit('false');
				}
			} elseif (!empty($email)) {
				$ucresult = uc_user_checkemail($email);
				if ($ucresult == -6 || $ucresult == -5) {
					exit('false');
				}
			}
		}
		$db_table = db_prefix . 'member';
		if (!empty($username)) {

			$lockusername = explode(',', $this->CON['mem_lock']);
			if (in_array($username, $lockusername)) {
				exit('false');
			}
			$db_where = " WHERE username='$username'";
		} elseif (!empty($email)) {
			$db_where = " WHERE email='$email'";
		}
		$countnum = $this->db_numrows($db_table, $db_where);
		if ($countnum > 0) {
			$exportAjax = 'false';
		} else {
			$exportAjax = 'true';
		}
		exit($exportAjax);
	}

	function in_typepuv() {
		if (!$this->CON['is_html']) {
			return true;
		}
		$tid = intval($this->fun->accept('tid', 'G'));
		$typeread = $this->get_type($tid);
		$userrank = $typeread['purview'];

		if (!$typeread['pageclass']) {
			return false;
		}

		if ($typeread['styleid'] == 3) {
			$typelink = $this->get_link('type', $typeread, admin_LNG);
			$text = "javascript:location.href='" . $typelink . "';";
			exit($text);
		}
		if (!$userrank) {
			return true;
		}

		$username = $this->member_cookieview('username');
		$username_id = $this->member_cookieview('userid');
		$ec_member_mcid = $this->member_cookieview('mcid');

		$mlink = $this->memberlink(array(), admin_LNG);
		if (empty($username) || empty($username_id)) {

			$this->fun->setcookie('ecisp_login_link', $_SERVER['HTTP_REFERER'], 3600);
			$linkURL = $this->get_link('memberlogin', array(), admin_LNG);
			if (admin_LNG == 'big5') {
				$str = $this->fun->codeing($this->lng['memberloginerr'], 'ub');
			} else {
				$str = $this->lng['memberloginerr'];
			}
			$text = "javascript:if (confirm('" . $str . "')){location.href='" . $linkURL . "'}else{location.href='" . $mlink['reg'] . "'};";
			exit($text);
		} else {
			if ($ec_member_mcid < $userrank && $userrank) {
				$linkURL = $this->get_link('memberlogin', array(), admin_LNG);
				if (admin_LNG == 'big5') {
					$str = $this->fun->codeing($this->lng['memberpuverr'], 'ub');
				} else {
					$str = $this->lng['memberpuverr'];
				}
				$text = "javascript:if (confirm('" . $str . "')){location.href='" . $linkURL . "'}else{location.href='" . $mlink['reg'] . "'};";
				exit($text);
			}
		}
	}

	function in_readpuv() {
		if (!$this->CON['is_html']) {
			return true;
		}
		$did = intval($this->fun->accept('did', 'G'));
		if (empty($did)) {
			return false;
		}
		$readinfo = $this->get_documentview($did);
		if (empty($readinfo['tid'])) return false;
		$typeread = $this->get_type($readinfo['tid']);

		if (!$readinfo['ishtml'] || !$typeread['pageclass']) {
			return false;
		}

		$userrank = $typeread['purview'];

		$userrank2 = $readinfo['purview'];
		if (!$readinfo['isclass']) {
			$text = "javascript:location.href='" . $this->CON['domain'] . "';";
			exit($text);
		}

		if ($readinfo['islink']) {
			$urladd = $readinfo['link'];
			$text = "javascript:location.href='" . $urladd . "';";
			exit($text);
		}

		$db_table = db_prefix . 'document';
		$db_where = "isclass=1 AND did=$did";
		$db_set = "click=click+1";
		$this->db->query('UPDATE ' . $db_table . ' SET ' . $db_set . ' WHERE ' . $db_where);
		if (!$userrank && !$userrank2) {

			return true;
		} elseif ($userrank < $userrank2) {

			$userrank = $userrank2;
		}

		$username = $this->member_cookieview('username');
		$username_id = $this->member_cookieview('userid');
		$ec_member_mcid = $this->member_cookieview('mcid');

		$mlink = $this->memberlink(array(), admin_LNG);
		if (empty($username) || empty($username_id)) {
			$this->fun->setcookie('ecisp_login_link', $_SERVER['HTTP_REFERER'], 3600);
			$linkURL = $this->get_link('memberlogin', array(), admin_LNG);
			if (admin_LNG == 'big5') {
				$str = $this->fun->codeing($this->lng['memberloginerr'], 'ub');
			} else {
				$str = $this->lng['memberloginerr'];
			}
			$text = "javascript:if (confirm('" . $str . "')){location.href='" . $linkURL . "'}else{location.href='" . $mlink['reg'] . "'};";
			exit($text);
		} else {
			if ($ec_member_mcid < $userrank && $userrank) {
				$linkURL = $this->get_link('memberlogin', array(), admin_LNG);
				if (admin_LNG == 'big5') {
					$str = $this->fun->codeing($this->lng['memberpuverr'], 'ub');
				} else {
					$str = $this->lng['memberpuverr'];
				}
				$text = "javascript:if (confirm('" . $str . "')){location.href='" . $linkURL . "'}else{location.href='" . $mlink['reg'] . "'};";
				exit($text);
			}
		}
	}

	function in_invite() {
		$mlvid = intval($this->fun->accept('mlvid', 'P'));
		$lng = $this->fun->accept('lng', 'R');
		if (!$mlvid) {
			$this->callmessage($this->lng['db_err'], $linkURL, $this->lng['gobackbotton']);
		}
		$email = $this->fun->accept('email', 'P');
		if (!preg_match("/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/i", $email) || empty($email)) {
			$this->callmessage($this->lng['email_err'], $linkURL, $this->lng['gobackbotton']);
		}

		$db_table = db_prefix . "mailinvite_list";
		$db_where = " WHERE email='$email' AND mlvid=$mlvid";
		$countnum = $this->db_numrows($db_table, $db_where);
		if ($countnum > 0) {
			$this->callmessage($this->lng['mem_regemail_no'], $linkURL, $this->lng['gobackbotton']);
		}
		$db_table = db_prefix . "mailinvite_list";
		$db_where = " WHERE mlvid=$mlvid";
		$countnum = $this->db_numrows($db_table, $db_where);
		if (!$countnum) {
			$this->callmessage($this->lng['db_err'], $_SERVER['HTTP_REFERER'], $this->lng['gobackurlbotton']);
		}

		$addtime = time();
		$ec_member_username_id = $this->member_cookieview('userid');
		if ($ec_member_username_id) {
			$rsMember = $this->get_member_attvalue($ec_member_username_id);
		}
		$userid = $ec_member_username_id ? $ec_member_username_id : 0;
		$name = $rsMember['alias'] ? addslashes($rsMember['alias']) : '';

		$sex = $rsMember['sex'] ? $rsMember['sex'] : 0;
		$tel = $rsMember['tel'] ? addslashes($rsMember['tel']) : '';
		$address = $rsMember['address'] ? addslashes($rsMember['address']) : '';

		$db_field = 'mlvid,userid,name,sex,email,tel,address,isclass,addtime';
		$db_values = "$mlvid,$userid,'$name',$sex,'$email','$tel','$address',1,$addtime";
		$this->db->query('INSERT INTO ' . $db_table . ' (' . $db_field . ') VALUES (' . $db_values . ')');

		$this->callmessage($this->lng['invite_ok'], $linkURL, $this->lng['gobackbotton']);
	}

	function in_zoom() {
		include_once admin_ROOT . 'public/ectemplates/ectemplates_image.php';
		parent::start_pagetemplate();
		$lng = (admin_LNG == 'big5') ? $this->CON['is_lancode'] : admin_LNG;
		$filename = $this->fun->accept('filename', 'G');
		if (empty($filename)) {
			return false;
		}
		$w = intval($this->fun->accept('w', 'G'));
		$h = intval($this->fun->accept('h', 'G'));
		$width = empty($w) ? 200 : $w;
		$height = empty($h) ? 200 : $h;
		$iszoom = intval($this->fun->accept('iszoom', 'G'));

		$bgcolor = $this->fun->accept('bgcolor', 'G');
		$bgcolor = empty($bgcolor) || !preg_match("/^#[a-zA-Z0-9]{3,6}$/i", $bgcolor) ? !preg_match("/^#[a-zA-Z0-9]{3,6}$/i", $this->CON['img_bgcolor']) ? '#ffffff' : $this->CON['img_bgcolor']  : $bgcolor;

		$picfile = admin_ROOT . $filename;

		$picfile = $this->picpath($picfile);
		if (!file_exists($picfile) && !is_file($picfile)) {
			return false;
		}

		$mime = $this->getMimeType($picfile);
		$imageinfo = getimagesize($picfile);
		if (!$imageinfo) {
			return false;
		}

		$newsfilepathname = $this->pagetemplate->cache_pic . $width . '_' . $height . '_' . md5($filename . $bgcolor) . '.' . $mime;
		if (!is_file($newsfilepathname)) {

			$imagescreat = new ectemplates_image();

			$imagescreat->imagecreat($width, $height, $bgcolor, $picfile, $newsfilepathname, $iszoom);
		}
		$imgdata = fread(fopen($newsfilepathname, 'rb'), filesize($newsfilepathname));
		header("content-type:$imageinfo[mime]");
		echo $imgdata;
	}

	function picpath($pic = null) {
		if (!empty($pic)) {
			if (stripos($pic, 'http')) {
				return $pic;
			} else {
				return str_replace('../', admin_ROOT . '/', $pic);
			}
		} else {
			return $pic;
		}
	}



	function getMimeType($file) {
		return is_dir($file) ? $file : $this->mime($file);
	}



	function mime($file) {

		$file = realpath($file);

		$options = pathinfo($file, PATHINFO_EXTENSION);
		return $options;
	}

}

?>