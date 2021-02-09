<?php

/*
  PHP version 5
  Copyright (c) 2002-2014 ECISP.CN、EarcLink.COM
  警告：这不是一个免费的软件，请在许可范围内使用，请尊重知识产权，侵权必究，举报有奖

  作者：黄祥云 E-mail:6326420@qq.com  QQ:6326420 TEL:18665655030
  ESPCMS官网介绍：http://www.ecisp.cn 企业建站：http://www.earclink.cn
 */

class lib_tablabel extends connector {

	function lib_tablabel() {
		$this->softbase(true);
		parent::start_template();
		$this->ectemplates->caching = false;
		$this->ectemplates->libfile = true;
	}

	public function call_tablabel($para, $filename = 'tablabel') {

		$menutype = empty($para[0]) ? 'loglist' : $para[0];
		$archive = $this->fun->accept('archive', 'G');
		$action = $this->fun->accept('action', 'G');
		$this->ectemplates->assign('powerlist', $this->esp_powerlist);
		$this->ectemplates->assign('archive', $archive);
		$this->ectemplates->assign('action', $action);
		$this->ectemplates->assign('menutype', $menutype);
		$output = $this->ectemplates->fetch($filename);
		return $output;
	}


}

?>
