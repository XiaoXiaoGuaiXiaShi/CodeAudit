<?php

/*
  PHP version 5
  Copyright (c) 2002-2014 ECISP.CN、EarcLink.COM
  警告：这不是一个免费的软件，请在许可范围内使用，请尊重知识产权，侵权必究，举报有奖

  作者：黄祥云 E-mail:6326420@qq.com  QQ:6326420 TEL:18665655030
  ESPCMS官网介绍：http://www.ecisp.cn 企业建站：http://www.earclink.cn
 */

class lib_tabbotton extends connector {

	function lib_tabbotton() {
		$this->softbase(true);
		parent::start_template();
		$this->ectemplates->caching = false;
		$this->ectemplates->libfile = true;
	}




	public function call_tabbotton($para, $filename = 'tabbotton') {
		$this->ectemplates->assign('powerlist', $this->esp_powerlist);

		$loadfun = $para[0];
		if (!$loadfun) {
			return false;
		}
		$nowurl = $this->fun->request_url();

		$tabcount = empty($para[1]) ? 0 : $para[1];
		$tabexp = explode('-', $tabcount);

		if ($loadfun == 'articlelist') {

			$tabarray['mid'] = $tabexp[0];
			$tabarray['tid'] = $tabexp[1];
			$nowurl = preg_replace('/&tid=[0-9]+/i', '', $nowurl);
			$nowurl = preg_replace('/&mid=[0-9]+/i', '', $nowurl);

			$typelist = $this->get_typeselect(0, 0, $tabarray['tid'], $this->sitelng, 0, 1, true, false);
			if (is_array($typelist)) {
				foreach ($typelist as $key => $value) {
					if ($value['styleid'] == 4 && $value['gotoid'] > 0) {

						$gotodid = $this->get_type($value['gotoid'], 'linkid');
						if ($gotodid) {

							$typelist[$key]['linkid'] = $gotodid;
						}
					}
					$typelist[$key]['url'] = $nowurl . '&tid=' . $value['tid'] . '&mid=' . $value['mid'];
				}
			}
			$this->ectemplates->assign('typelist', $typelist);

			$modelarray = $this->get_model($tabarray['mid'], $this->sitelng, 0, 2, 0);
			if (is_array($modelarray['list'])) {
				foreach ($modelarray['list'] as $key => $value) {
					$modelarray['list'][$key]['url'] = $nowurl . '&mid=' . $value['mid'];
				}
			}
			$this->ectemplates->assign('modelarray', $modelarray['list']);
		} elseif ($loadfun == 'seolinklist' || $loadfun == 'seolinktypelist') {


			$tabarray['tid'] = $tabexp[1];
			$tabarray['mid'] = $tabexp[0];
			$nowurl = preg_replace('/&tid=[0-9]+/i', '', $nowurl);
			$nowurl = preg_replace('/&mid=[0-9]+/i', '', $nowurl);

			$typelist = $this->get_typeselect(0, 0, $tabarray['tid'], $this->sitelng, 0, 1, true, false);
			if (is_array($typelist)) {
				foreach ($typelist as $key => $value) {
					$typelist[$key]['url'] = $nowurl . '&tid=' . $value['tid'] . '&mid=' . $value['mid'];
				}
			}
			$this->ectemplates->assign('typelist', $typelist);

			$modelarray = $this->get_model($tabarray['mid'], $this->sitelng);
			if (is_array($modelarray['list'])) {
				foreach ($modelarray['list'] as $key => $value) {
					$modelarray['list'][$key]['url'] = $nowurl . '&mid=' . $value['mid'];
				}
			}
			$this->ectemplates->assign('modelarray', $modelarray['list']);
		} elseif ($loadfun == 'typelist') {
			$tabarray['mid'] = $tabexp[0];
			$nowurl = preg_replace('/&mid=[0-9]+/i', '', $nowurl);

			$modelarray = $this->get_model($tabarray['mid'], $this->sitelng);
			if (is_array($modelarray['list'])) {
				foreach ($modelarray['list'] as $key => $value) {
					$modelarray['list'][$key]['url'] = $nowurl . '&mid=' . $value['mid'];
				}
			}
			$this->ectemplates->assign('modelarray', $modelarray['list']);
		} elseif ($loadfun == 'subjectlist' || $loadfun == 'recomlist') {

			$tabarray['mid'] = $tabexp[0];
			$nowurl = preg_replace('/&mid=[0-9]+/i', '', $nowurl);

			$modelarray = $this->get_model($tabarray['mid'], $this->sitelng, 0, 2, 0);
			if (is_array($modelarray['list'])) {
				foreach ($modelarray['list'] as $key => $value) {
					$modelarray['list'][$key]['url'] = $nowurl . '&mid=' . $value['mid'];
				}
			}
			$this->ectemplates->assign('modelarray', $modelarray['list']);
		} elseif ($loadfun == 'memberlist') {

			$tabarray['mcid'] = $tabexp[2];
			$nowurl = preg_replace('/&mcid=[0-9]+/i', '', $nowurl);

			$memberclassarray = $this->get_member_purview_array($tabarray['mcid']);
			if (is_array($memberclassarray['list'])) {
				foreach ($memberclassarray['list'] as $key => $value) {
					$memberclassarray['list'][$key]['url'] = $nowurl . '&mcid=' . $value['mcid'];
				}
			}
			$this->ectemplates->assign('memberclass', $memberclassarray['list']);
		} elseif ($loadfun == 'advertlist') {

			$tabarray['atid'] = $tabexp[3];
			$nowurl = preg_replace('/&atid=[0-9]+/i', '', $nowurl);

			$advert_typearray = $this->get_advert_type_array($tabarray['atid'], $this->sitelng);
			if (is_array($advert_typearray['list'])) {
				foreach ($advert_typearray['list'] as $key => $value) {
					$advert_typearray['list'][$key]['url'] = $nowurl . '&atid=' . $value['atid'];
				}
			}
			$this->ectemplates->assign('adtype', $advert_typearray['list']);
		}
		$this->ectemplates->assign('menutype', $menutype);
		$this->ectemplates->assign('loadfun', $loadfun);
		$output = $this->ectemplates->fetch($filename);
		return $output;
	}

}

?>
