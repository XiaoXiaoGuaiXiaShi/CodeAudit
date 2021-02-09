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
		$this->softbase(true);
		parent::start_template();
		$this->ectemplates->caching = false;
		$this->ectemplates->libfile = true;
	}

	public function call_menu($para, $filename = 'menu') {

		$topmlid = empty($para[0]) ? 1 : $para[0];
		$archive = $this->fun->accept('archive', 'G');
		$action = $this->fun->accept('action', 'G');
		$loadfun = $this->fun->accept('loadfun', 'G');
		$menuid = $this->fun->accept('menuid', 'G');
		$db_table = db_prefix . 'menulink';
		$sql = "SELECT mlid,pid,topmlid,menuname,linkurl,isshow,loadfun FROM $db_table WHERE topmlid=0 ORDER BY pid,mlid ASC";
		$menu = $this->dbcache->checkcache('menu_' . $this->esp_username, false);
		if (!$menu) {
			$rs = $this->db->query($sql);

			while ($rsList = $this->db->fetch_assoc($rs)) {
				if (!in_array($rsList['loadfun'], $this->esp_powerlist)) {

					$topmlid = $rsList['mlid'];
					$sqlnext = "SELECT mlid,pid,topmlid,menuname,linkurl,isshow,loadfun FROM $db_table WHERE topmlid=$topmlid and isshow=1 ORDER BY pid,mlid ASC";
					$rsNext = $this->db->query($sqlnext);
					while ($rsNList = $this->db->fetch_assoc($rsNext)) {
						if (!in_array($rsNList['loadfun'], $this->esp_powerlist)) {
							$rsNList['linkurl'] = $rsNList['linkurl'] . '&menuname_title=' . urlencode($rsNList['menuname']);
							$rsList['menulink'][] = $rsNList;
						}
					}
					$menulinkarray[] = $rsList;
				}
			}
			$menu = $this->dbcache->cachesave('menu_' . $this->esp_username, $menulinkarray);
			$menu = $menu ? $menu : $menulinkarray;
			unset($menulinkarray);
		}

		$nowurl = $this->fun->request_url();

		$nowurl = preg_replace('/&sitesoftlng=[\w]+/i', '', $nowurl);
		$nowurl = preg_replace('/&tid=[0-9]+/i', '', $nowurl);
		$nowurl = preg_replace('/&mid=[0-9]+/i', '', $nowurl);
		$db_table = db_prefix . 'lng';
		$sql = "SELECT id,pid,lng,lngtitle,url,lockin,isopen,isuptype,sitename,keyword,description,copyright FROM $db_table WHERE isuptype=0 ORDER BY id ASC";
		$lanar = $this->dbcache->checkcache('lngarray', false);
		if (!$lanar) {
			$rs = $this->db->query($sql);

			while ($rsList = $this->db->fetch_assoc($rs)) {
				$rsList['url'] = $nowurl . '&sitesoftlng=' . $rsList['lng'];
				$lanarray[] = $rsList;
			}
			$lanar = $this->dbcache->cachesave('lngarray', $lanarray);
			$lanar = $lanar ? $lanar : $lanarray;
			unset($lanarray);
		}

		$this->ectemplates->assign('powerlist', $this->esp_powerlist);
		$this->ectemplates->assign('archive', $archive);
		$this->ectemplates->assign('action', $action);
		$this->ectemplates->assign('loadfun', $loadfun);
		$this->ectemplates->assign('menulinkarray', $menu);
		$this->ectemplates->assign('menuid', $menuid);
		$this->ectemplates->assign('lanarray', $lanar);
		$this->ectemplates->assign('lng', $this->sitelng);
		$output = $this->ectemplates->fetch($filename);
		return $output;
	}

}

?>
