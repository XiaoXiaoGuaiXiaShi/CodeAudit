<?php

/*
  PHP version 5
  Copyright (c) 2002-2013 ECISP.CN、EarcLink.COM
  警告：这不是一个免费的软件，请在许可范围内使用，请尊重知识产权，侵权必究，举报有奖！
  作者：黄祥云 E-mail:6326420@qq.com  QQ:6326420 TEL:18665655030
  ESPCMS官网介绍：http://www.ecisp.cn 企业建站：http://www.earclink.cn
 */

class BCGFont {

	private $path;
	private $text;
	private $size;
	private $box;

	public function __construct($fontPath, $size) {
		$this->path = $fontPath;
		$this->size = $size;
	}

	public function setText($text) {
		$this->text = $text;
		$im = imagecreate(1, 1);
		$this->box = imagettftext($im, $this->size, 0, 0, 0, imagecolorallocate($im, 0, 0, 0), $this->path, $this->text);
	}

	public function getWidth() {
		if ($this->box !== NULL) {




			return abs(max($this->box[2], $this->box[4]));
		} else {
			return 0;
		}
	}

	public function getHeight() {
		if ($this->box !== NULL) {
			return (float) abs(max($this->box[5], $this->box[7]) - min($this->box[1], $this->box[3]));
		} else {
			return 0.0;
		}
	}

	public function getUnderBaseline() {

		return (float) max($this->box[1], $this->box[3]);
	}

	public function draw(&$im, $color, $x, $y) {
		imagettftext($im, $this->size, 0, $x, $y, $color, $this->path, $this->text);
	}

}

?>