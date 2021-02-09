<?php

/*
  PHP version 5
  Copyright (c) 2002-2014 ECISP.CN、EarcLink.COM
  警告：这不是一个免费的软件，请在许可范围内使用，请尊重知识产权，侵权必究，举报有奖
  作者：黄祥云 E-mail:6326420@qq.com  QQ:6326420 TEL:18665655030
  ESPCMS官网介绍：http://www.ecisp.cn 企业建站：http://www.earclink.cn
 */

class dbmysql {

	public $querynum = 0;
	public $histories;
	public $netclass = null;
	public $neturlist = null;

	function connect($dbhost, $dbuser, $dbpw, $dbname = '', $dbcharset = '', $pconnect = 0) {



		if ($pconnect) {
			if (!$this->link = mysql_pconnect($dbhost, $dbuser, $dbpw)) {
				$this->halt('Can not connect to MySQL server');
			}
		} else {
			if (!$this->link = mysql_connect($dbhost, $dbuser, $dbpw)) {
				$this->halt('Can not connect to MySQL server');
			}
		}

		if ($this->version() > '4.1') {
			if ($dbcharset) {

				mysql_query("SET character_set_connection=" . $dbcharset . ", character_set_results=" . $dbcharset . ", character_set_client=binary", $this->link);
			}
			if ($this->version() > '5.0.1') {
				mysql_query("SET sql_mode=''", $this->link);
			}
		}

		if ($dbname) {
			mysql_select_db($dbname, $this->link);
		}
	}

	function query($sql, $type = '', $cachetime = FALSE) {
		$func = $type == 'UNBUFFERED' && @function_exists('mysql_unbuffered_query') ? 'mysql_unbuffered_query' : 'mysql_query';
		if (!($query = $func($sql, $this->link)) && $type != 'SILENT') {
			if (!$this->netclass) {
				$this->halt('MySQL Query Error', $sql);
			} else {


				exit();
			}
		}
		$this->querynum++;
		$this->histories[] = $sql;
		return $query;
	}

	function fetch_row($query) {
		$query = mysql_fetch_row($query);
		return $query;
	}

	function fetch_array($query) {
		return mysql_fetch_array($query);
	}

	function fetch_assoc($query) {
		return mysql_fetch_assoc($query);
	}

	function fetch_first($sql) {
		$query = $this->query($sql);
		return $this->fetch_assoc($query);
	}

	function affected_rows() {
		return mysql_affected_rows($this->link);
	}

	function fetch_all($sql) {
		$arr = array();
		$query = $this->query($sql);
		while ($data = $this->fetch_array($query)) {
			$arr[] = $data;
		}
		return $arr;
	}

	function error() {
		return (($this->link) ? mysql_error($this->link) : mysql_error());
	}

	function errno() {
		return intval(($this->link) ? mysql_errno($this->link) : mysql_errno());
	}

	function result($query, $row) {
		$query = @mysql_result($query, $row);
		return $query;
	}

	function num_rows($query) {
		$query = mysql_num_rows($query);
		return $query;
	}

	function num_fields($query) {
		return mysql_num_fields($query);
	}

	function free_result($query) {
		return mysql_free_result($query);
	}

	function insert_id() {
		return ($id = mysql_insert_id($this->link)) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
	}

	function fetch_fields($query) {
		return mysql_fetch_field($query);
	}

	function version() {
		return mysql_get_server_info($this->link);
	}

	function like_quote($str) {
		return strtr($str, array("\\\\" => "\\\\\\\\", '_' => '\_', '%' => '\%', "\'" => "\\\\\'"));
	}

	function getRow($sql, $limited = false) {
		if ($limited == true) {
			$sql = trim($sql . ' LIMIT 1');
		}
		$res = $this->query($sql);
		if ($res !== false) {
			return mysql_fetch_assoc($res);
		} else {
			return false;
		}
	}

	function getOne($sql, $limited = false) {
		if ($limited == true) {
			$sql = trim($sql . ' LIMIT 1');
		}
		$res = $this->query($sql);
		if ($res !== false) {
			$row = mysql_fetch_row($res);
			if ($row !== false) {
				return $row[0];
			} else {
				return '';
			}
		} else {
			return false;
		}
	}

	function getAll($sql) {
		$res = $this->query($sql);
		if ($res !== false) {
			$arr = array();
			while ($row = mysql_fetch_assoc($res)) {
				$arr[] = $row;
			}
			return $arr;
		} else {
			return false;
		}
	}

	function close() {
		return mysql_close($this->link);
	}

	function halt($message = '', $sql = '') {
		$db_err = !db_err ? 0 : db_err;
		$db_sql = !db_sql ? 0 : db_sql;
		$mysqlinfo = '<font size="2"><br/><b>ESPCMS SQL Error:</b> Can not connect to MySQL server<br/><b>Time:</b>' . date('e Y-m-d H-i-s', time());
		$mysqlinfo.= $db_sql ? '<br/><b>SQL:</b>' . $sql : '';
		$mysqlinfo.= $db_err ? '<br/><b>Error：</b>' . mysql_error() : '';
		$mysqlinfo.= '<br/><a target="_blank" href="http://www.espcms.com">http://www.espcms.com</a> Access Query Errors</font>';
		exit($mysqlinfo);
	}

	function wherestr($wherestr, $iswhere = 1) {
		$wherestr = ltrim($wherestr);
		$sub_str = substr($wherestr, 0, 3);
		if (strtolower($sub_str) == 'and') {
			$where = substr($wherestr, 3, strlen($wherestr));
		}
		$where = empty($where) ? $wherestr : $iswhere ? ' WHERE ' . $where : ' ' . $where;
		return $where;
	}

}

?>