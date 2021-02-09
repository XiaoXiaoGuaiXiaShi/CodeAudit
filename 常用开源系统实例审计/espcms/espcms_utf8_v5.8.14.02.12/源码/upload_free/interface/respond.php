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

	function in_payok() {
		parent::start_pagetemplate();
		parent::member_purview(0, $this->mlink['login']);
		$lng = (admin_LNG == 'big5') ? $this->CON['is_lancode'] : admin_LNG;
		$linkURL = $_SERVER['HTTP_REFERER'];
		$paycode = $this->fun->accept('code', 'G');
		$ordersn = $this->fun->accept('ordersn', 'G');
		$oid = intval($this->fun->accept('oid', 'G'));
		$codesn = $this->fun->accept('codesn', 'G');
		$integral = intval($this->CON['order_integral']);
		$sncode = $this->fun->eccode($paycode . $ordersn . $oid, 'ENCODE', db_pscode, FALSE);

		if ($sncode != $codesn) {
			$linkURL = $this->mlink['center'];
			$this->callmessage($this->lng['order_pay_no'], $linkURL, $this->lng['member_center_botton']);
		}
		
		$userid = intval($this->ec_member_username_id);
		if (empty($userid) || empty($oid)) {
			$linkURL = $this->mlink['center'];
			$this->callmessage($this->lng['db_err'], $linkURL, $this->lng['member_center_botton']);
		}
		
		if (!empty($paycode)) {
			$db_table1 = db_prefix . 'order_pay';
			$db_table2 = db_prefix . 'order';
			$db_table3 = db_prefix . 'order_payreceipt';
			$db_where = "paycode='$paycode'";
			$rsList = $this->db->fetch_first('SELECT * FROM ' . $db_table1 . ' WHERE ' . $db_where);
			if ($rsList) {
				$config_list = unserialize($rsList['pluglist']);
				foreach ($config_list as $config) {
					$paymentvlue[$config['name']] = $config['value'];
				}

				include_once admin_ROOT . 'public/plug/payment/' . $paycode . '.php';

				$payment = new $paycode();

				$paymentType = $payment->respond($paymentvlue, $rsList);

				$db_where = "oid=$oid AND ordertype<>2";
				$rsRead = $this->db->fetch_first('SELECT * FROM ' . $db_table2 . ' WHERE ' . $db_where);
				if ($paymentType && $rsRead) {

					$ipadd = $this->fun->ip($_SERVER['REMOTE_ADDR']);
					$addtime = time();
					if (!empty($integral)) {
						$integral = intval($rsRead['orderamount'] / intval($integral));
					} else {
						$integral = 0;
					}
					$db_set = "ordertype=2,paysn='$paymentType',paytime='$addtime',integral=$integral";
					$this->db->query('UPDATE ' . $db_table2 . ' SET ' . $db_set . ' WHERE ' . $db_where);
					$db_field = 'oid,opid,paysn,ordersn,orderamount,bankaccount,bankname,username,content,userid,isclass,paytime,addtime';
					$db_values = "$oid,$rsRead[opid],'$rsRead[paysn]','$ordersn',$rsRead[orderamount],'$paymentvlue[paypal_account]','$paycode','onlineuser','',$rsRead[userid],1,$addtime,$addtime";
					$this->db->query('INSERT INTO ' . $db_table3 . ' (' . $db_field . ') VALUES (' . $db_values . ')');

					if ($rsRead['userid'] > 0) {
						$this->set_member_integral($rsRead['userid'], $integral);
					}

					if ($this->CON['is_email']) {

						$this->ordermailsend('orderpal', $oid, $rsRead['email']);

						$this->ordermailsend('orderpayadmin', $oid, $this->CON['admine_mail']);
					}

					if ($this->CON['is_moblie']) {
						$mobile = $this->CON['moblie_number'];
						$this->membersmssend($rsRead, $mobile, 'orderpay');
					}

					$this->pagetemplate->assign('order', $rsRead);
					$this->pagetemplate->assign('pay', $rsList);
					$this->pagetemplate->assign('paysn', $paymentType);

					$linkURL = $this->mlink['center'];
					$readlink = $this->get_link('orderread', $rsRead, admin_LNG);
					$this->callmessage($this->lng['order_pay_ok'], $linkURL, $this->lng['member_center_botton'], 1, $this->lng['order_read_botton'], 1, $readlink);
				} else {
					$linkURL = $this->mlink['center'];
					$this->callmessage($this->lng['order_pay_no'], $linkURL, $this->lng['member_center_botton']);
				}
			} else {
				$linkURL = $this->mlink['center'];
				$this->callmessage($this->lng['order_pay_no'], $linkURL, $this->lng['member_center_botton']);
			}
		} else {
			$linkURL = $this->mlink['center'];
			$this->callmessage($this->lng['order_pay_no'], $linkURL, $this->lng['member_center_botton']);
		}
	}

}

?>
