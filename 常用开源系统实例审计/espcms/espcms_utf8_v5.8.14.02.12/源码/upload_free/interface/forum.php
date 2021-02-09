<?php

/*
  PHP version 5
  Copyright (c) 2002-2014 ECISP.CN、EarcLink.COM
  警告：这不是一个免费的软件，请在许可范围内使用，请尊重知识产权，侵权必究，举报有奖

  作者：黄祥云 E-mail:6326420@qq.com  QQ:6326420 TEL:18665655030
  ESPCMS官网介绍：http://www.ecisp.cn 企业建站：http://www.earclink.cn
 */

class mainpage extends connector {

	function mainpage() {
		$this->softbase(false);
		$this->mlink = $this->memberlink(array(), admin_LNG);
	}

	function in_list() {
		parent::start_pagetemplate();
		include_once admin_ROOT . 'public/class_pagebotton.php';
		$lng = (admin_LNG == 'big5') ? $this->CON['is_lancode'] : admin_LNG;
		$btid = intval($this->fun->accept('btid', 'G'));
		if (empty($btid)) {
			$this->callmessage($this->lng['db_err'], $_SERVER['HTTP_REFERER'], $this->lng['gobackurlbotton']);
		}

		$page = $this->fun->accept('page', 'G');
		$page = isset($page) ? intval($page) : 1;
		$typeread = $this->get_bbstype_view($btid);
		if (!$typeread['isclass']) {
			exit("Information parameter error!");
		} elseif ($typeread['purview'] > 0) {
			parent::member_purview($typeread['purview'], null, true);
		}
		$pagesylte = 1;

		$pagemax = intval($typeread['pagemax']);
		$typeread['addlink'] = $this->get_link('forumadd', $typeread, admin_LNG);
		$typeread['link'] = $this->get_link('forumlist', $typeread, admin_LNG);
		$this->pagetemplate->assign('type', $typeread);

		$userid = intval($this->member_cookieview('userid'));
		$userid = empty($userid) ? 0 : $userid;
		if ($userid) {
			$reMem = $this->get_member(0, $userid);
			$this->pagetemplate->assign('member', $reMem);
		}

		$db_table = db_prefix . 'bbs';
		$db_where = " WHERE isclass=1 AND upbid=0 AND btid=$btid AND lng='$lng'";
		$countnum = $this->db_numrows($db_table, $db_where);
		if ($countnum > 0) {

			$numpage = ceil($countnum / $pagemax);
		} else {
			$numpage = 1;
		}
		$sql = "SELECT * FROM $db_table $db_where LIMIT 0,$pagemax";
		$this->htmlpage = new PageBotton($sql, $pagemax, $page, $countnum, $numpage, $pagesylte, $this->CON['file_fileex'], 5, $this->lng['pagebotton'], $this->lng['gopageurl'], $this->CON['is_rewrite']);
		$sql = $this->htmlpage->PageSQL('istop,bid', 'down');
		$rs = $this->db->query($sql);
		while ($rsList = $this->db->fetch_assoc($rs)) {
			$rsList['link'] = $this->get_link('forumread', $rsList, admin_LNG);
			if ($typeread['ispage'] == 1) {
				$relist = $this->get_bbs_array($rsList['bid']);
				$rsList['list'] = $relist['list'];
			}
			$array[] = $rsList;
		}
		$this->pagetemplate->assign('tokenkey', $this->fun->token());
		$this->pagetemplate->assign('pagetext', $this->htmlpage->PageStat($this->lng['pagetext']));
		$this->pagetemplate->assign('pagebotton', $this->htmlpage->PageList());
		$this->pagetemplate->assign('pagenu', $this->htmlpage->Bottonstyle(false));
		$this->pagetemplate->assign('pagese', $this->htmlpage->pageSelect());
		$this->pagetemplate->assign('pagevt', $this->htmlpage->Prevbotton());
		$this->pagetemplate->assign('seccodelink', $this->get_link('seccode'));
		$this->pagetemplate->assign('mlink', $this->mlink);
		$this->pagetemplate->assign('array', $array);
		$this->pagetemplate->assign('current', $btid);
		$this->pagetemplate->assign('path', 'forum');

		$templatesDIR = $this->get_templatesdir('forum');
		if ($typeread['istemplates'] && !empty($typeread['templates'])) {
			$templatefilename = $lng . '/' . $templatesDIR . '/' . $typeread['templates'];
		} else {
			$templatefilename = $lng . '/' . $templatesDIR . '/message_center';
		}
		$out = $typeread['ispage'] == 1 ? 'forumbbs' : 'forumlist';
		$this->pagetemplate->assign('out', $out);
		unset($array, $typeread, $modelview, $LANPACK, $this->lng);
		$this->pagetemplate->display($templatefilename, 'forum_list', false, '', admin_LNG);
	}

	function in_read() {
		parent::start_pagetemplate();
		include_once admin_ROOT . 'public/class_pagebotton.php';
		$lng = (admin_LNG == 'big5') ? $this->CON['is_lancode'] : admin_LNG;

		$page = $this->fun->accept('page', 'G');
		$page = isset($page) ? intval($page) : 1;
		$pagesylte = 1;

		$bid = intval($this->fun->accept('bid', 'G'));
		if (empty($bid)) trigger_error("Forum parameter error!", E_USER_ERROR);
		$rsList = $this->get_bbs($bid);
		if (!$rsList) exit("Information parameter error!");

		$typeread = $this->get_bbstype_view($rsList['btid']);
		if (!$typeread['isclass']) {
			exit("Information parameter error!");
		} elseif ($typeread['purview'] > 0) {
			parent::member_purview($typeread['purview'], null, true);
		}
		$pagemax = $typeread['listmax'];
		$typeread['link'] = $this->get_link('forumlist', $typeread, admin_LNG);

		$this->pagetemplate->assign('type', $typeread);

		$userid = intval($this->member_cookieview('userid'));
		$userid = empty($userid) ? 0 : $userid;
		if ($userid) {
			$reMem = $this->get_member(0, $userid);
			$this->pagetemplate->assign('member', $reMem);
		}

		$db_table = db_prefix . 'bbs';
		$db_where = "isclass=1 AND upbid=0 AND bid=$bid";
		$db_set = "click=click+1";
		$this->db->query('UPDATE ' . $db_table . ' SET ' . $db_set . ' WHERE ' . $db_where);

		$this->pagetemplate->assign('mlink', $this->mlink);
		$this->pagetemplate->assign('path', 'forum');

		$this->pagetemplate->assign('seccodelink', $this->get_link('seccode'));
		$this->pagetemplate->assign('read', $rsList);

		$db_table = db_prefix . 'bbs';
		$db_where = " WHERE isclass=1 AND upbid=$bid";
		$countnum = $this->db_numrows($db_table, $db_where);
		if ($countnum > 0) {

			$numpage = ceil($countnum / $pagemax);
		} else {
			$numpage = 1;
		}
		$sql = "SELECT * FROM $db_table $db_where LIMIT 0,$pagemax";
		$this->htmlpage = new PageBotton($sql, $pagemax, $page, $countnum, $numpage, $pagesylte, $this->CON['file_fileex'], 5, $this->lng['pagebotton'], $this->lng['gopageurl'], $this->CON['is_rewrite']);
		$sql = $this->htmlpage->PageSQL('bid', 'up');
		$rs = $this->db->query($sql);
		while ($rsList = $this->db->fetch_assoc($rs)) {
			$array[] = $rsList;
		}

		$templatesDIR = $this->get_templatesdir('forum');
		if ($typeread['istemplates'] && !empty($typeread['templates'])) {
			$templatefilename = $lng . '/' . $templatesDIR . '/' . $typeread['templates'];
		} else {
			$templatefilename = $lng . '/' . $templatesDIR . '/message_center';
		}
		$this->pagetemplate->assign('out', 'forumread');

		$this->pagetemplate->assign('pagetext', $this->htmlpage->PageStat($this->lng['pagetext']));
		$this->pagetemplate->assign('pagebotton', $this->htmlpage->PageList());
		$this->pagetemplate->assign('pagenu', $this->htmlpage->Bottonstyle(false));
		$this->pagetemplate->assign('pagese', $this->htmlpage->pageSelect());
		$this->pagetemplate->assign('pagevt', $this->htmlpage->Prevbotton());
		$this->pagetemplate->assign('tokenkey', $this->fun->token());
		$this->pagetemplate->assign('array', $array);
		unset($array, $typeread, $modelview, $LANPACK, $this->lng, $reMem);
		$this->pagetemplate->display($templatefilename, 'forum_read', false, '', admin_LNG);
	}

	function in_save() {
		$linkURL = $_SERVER['HTTP_REFERER'];
		if (!$this->fun->is_token()) {
			$this->callmessage($this->lng['repeatinput'], $linkURL, $this->lng['gobackbotton']);
		}
		parent::start_pagetemplate();

		$lng = (admin_LNG == 'big5') ? $this->CON['is_lancode'] : admin_LNG;
		$inputclass = $this->fun->accept('inputclass', 'P');
		$btid = intval($this->fun->accept('btid', 'P'));
		if (empty($btid)) {
			$this->callmessage($this->lng['db_err'], $linkURL, $this->lng['gobackurlbotton']);
		}
		$db_table = db_prefix . "bbs";
		$title = trim($this->fun->accept('title', 'P', true, true));
		$title = $this->fun->substr($title, 80);

		$username = $this->fun->accept('username', 'P', true, true);
		$username = $this->fun->substr($username, 20);
		$email = trim($this->fun->accept('email', 'P', true, true));
		$content = $this->fun->accept('content', 'P');
		$content = empty($content) ? '' : $this->fun->Text2Html($content, false);

		$userid = intval($this->fun->accept('userid', 'P'));
		if (empty($title)) {
			$this->callmessage($this->lng['forum_title_err'], $linkURL, $this->lng['gobackbotton']);
		}
		if (empty($content) || empty($username)) {
			$this->callmessage($this->lng['forum_input_err'], $linkURL, $this->lng['gobackbotton']);
		}
		if (!preg_match("/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/i", $email)) {
			$this->callmessage($this->lng['email_err'], $linkURL, $this->lng['gobackbotton']);
		}
		$typeread = $this->get_bbstype_view($btid);
		if (!$typeread) {
			$this->callmessage($this->lng['db_err'], $_SERVER['HTTP_REFERER'], $this->lng['gobackurlbotton']);
		}
		$qq = $this->fun->accept('qq', 'P', true, true);
		$qq = $this->fun->substr($qq, 10);
		$msn = $this->fun->accept('msn', 'P', true, true);
		$address = $this->fun->accept('address', 'P', true, true);
		$address = $this->fun->substr($address, 150);
		$tel = $this->fun->accept('tel', 'P', true, true);
		$tel = $this->fun->substr($tel, 15);
		$mobile = $this->fun->accept('mobile', 'P', true, true);
		$mobile = $this->fun->substr($mobile, 15);

		if ($typeread['isseccode']) {
			$seccode = $this->fun->accept('seccode', 'P');
			include_once admin_ROOT . 'public/class_seccode.php';
			list($new_seccode, $expiration) = explode("\t", $this->fun->eccode($_COOKIE['ecisp_home_seccode'], 'DECODE'));
			$code = new seccode();
			$code->seccodeconvert($new_seccode);
			if ($new_seccode != strtoupper($seccode)) {
				$this->callmessage($this->lng['seescodeerr'], $linkURL, $this->lng['gobackbotton']);
			}
		}

		if (!$typeread['isclass']) {
			exit("Information parameter error!");
		} elseif ($typeread['purview'] > 0) {
			parent::member_purview($purview);
		}

		$isclass = $typeread['isaddclass'] ? 0 : 1;
		if ($typeread['purview'] > 0) {
			parent::member_purview($typeread['purview']);
		}
		if (!empty($this->ec_member_username_id)) {

			$rsMember = $this->get_member(null, $this->ec_member_username_id);

			$lockusername = explode(',', $this->CON['bbs_username']);

			if (in_array($this->ec_member_username, $lockusername)) {
				$this->callmessage($this->lng['forum_mem_username'], $_SERVER['HTTP_REFERER'], $this->lng['gobackbotton']);
			}
		}

		if (!empty($this->CON['bbs_filter'])) {
			if ($this->fun->screening_key($content, $this->CON['bbs_filter']) || $this->fun->screening_key($title, $this->CON['bbs_filter'])) {
				$this->callmessage($this->lng['forum_input_filter'], $linkURL, $this->lng['gobackbotton']);
			}
		}

		$ipadd = $this->fun->ip($_SERVER['REMOTE_ADDR']);
		$addtime = time();

		$usersessionid = md5($ipadd + $btid . 'input');
		$inputseesion = $this->fun->accept($usersessionid, 'C');

		if (empty($inputseesion) && $typeread['inputtime']) {

			$this->fun->setcookie($usersessionid, $addtime, $typeread['inputtime']);
		} elseif ($typeread['inputtime']) {

			$this->callmessage($this->lng['repeatinput'], $linkURL, $this->lng['gobackurlbotton']);
		}

		if ($inputclass == 'add') {
			$db_field = 'btid,upbid,adminid,userid,lng,title,content,username,email,qq,msn,address,tel,mobile,replynum,click,addtime,retime,isclass,istop,ipadd';
			$db_values = "$btid,0,0,$userid,'$lng','$title','$content','$username','$email','$qq','$msn','$address','$tel','$mobile',0,0,$addtime,0,$isclass,0,'$ipadd'";
			$this->db->query('INSERT INTO ' . $db_table . ' (' . $db_field . ') VALUES (' . $db_values . ')');
			$insert_id = $this->db->insert_id();

			$this->fun->setcookie('ecisp_home_seccode', null);
			if ($typeread['ispage'] == 2) {
				$linkURL = $this->get_link('forum', $typeread, admin_LNG);
			}
			if ($typeread['ismail'] && $this->CON['is_email']) {

				$mailcode = $typeread['mailcode'] ? $typeread['mailcode'] : 'forumnew';
				$putmail = $typeread['putmail'] ? $typeread['putmail'] : $this->CON['admine_mail'];
				$this->forumsendmail($mailcode, $insert_id, $putmail);
			}

			if ($typeread['issms'] && $this->CON['is_moblie']) {
				$messageArray = array('title' => $title);
				$mobile = $this->CON['moblie_number'];
				if ($typeread['smscode']) {
					$this->membersmssend($messageArray, $mobile, $typeread['smscode']);
				}
			}

			$this->callmessage($this->lng['forum_input_ok'], $linkURL, $this->lng['gobackurlbotton']);
		}

		if ($inputclass == 'reinput') {
			$upbid = intval($this->fun->accept('bid', 'P'));
			$reemail = $this->fun->accept('reemail', 'P');

			$db_where = "bid=$upbid AND isclass=1";
			$db_set = "replynum=replynum+1,retime=$addtime";
			$this->db->query('UPDATE ' . $db_table . ' SET ' . $db_set . ' WHERE ' . $db_where);

			$db_field = 'btid,upbid,adminid,userid,lng,title,content,username,email,qq,msn,address,tel,mobile,replynum,click,addtime,retime,isclass,istop,ipadd';
			$db_values = "$btid,$upbid,0,$userid,'$lng','$title','$content','$username','$email','$qq','$msn','$address','$tel','$mobile',0,0,$addtime,0,$isclass,0,'$ipadd'";
			$this->db->query('INSERT INTO ' . $db_table . ' (' . $db_field . ') VALUES (' . $db_values . ')');

			$this->fun->setcookie('ecisp_home_seccode', null);

			if ($typeread['ismail'] && $this->CON['is_email']) {

				if (preg_match("/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/i", $reemail)) {
					$this->forumsendmail('forumre', $upbid, $reemail);
				}
			}
			$this->callmessage($this->lng['forum_input_ok'], $linkURL, $this->lng['gobackurlbotton']);
		}
	}

}

?>