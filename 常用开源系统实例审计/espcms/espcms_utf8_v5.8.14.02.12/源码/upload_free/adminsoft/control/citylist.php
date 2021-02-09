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

	function oncitylist() {
		$parentid = $this->fun->accept('parentid', 'R');
		$parentid = empty($parentid) ? 1 : $parentid;
		$verid = $this->fun->accept('verid', 'R');
		$verid = empty($verid) ? 0 : $verid;
		$db_table = db_prefix . 'city';
		$sql = "select * from $db_table where parentid=$parentid";
		$rs = $this->db->query($sql);
		for ($i = 0; $rsList = $this->db->fetch_array($rs); $i++) {
			if ($verid == $rsList['id']) {
				$list.='<option selected value="' . $rsList['id'] . '">' . $rsList['cityname'] . '</option>';
			} else {
				$list.='<option value="' . $rsList['id'] . '">' . $rsList['cityname'] . '</option>';
			}
		}
		exit($list);
	}

}

?>