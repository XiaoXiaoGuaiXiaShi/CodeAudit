<?php

/*
  PHP version 5
  Copyright (c) 2002-2013 ECISP.CN、EarcLink.COM
  警告：这不是一个免费的软件，请在许可范围内使用，请尊重知识产权，侵权必究，举报有奖！
  作者：黄祥云 E-mail:6326420@qq.com  QQ:6326420 TEL:18665655030
  ESPCMS官网介绍：http://www.ecisp.cn 企业建站：http://www.earclink.cn
 */
include_once('Barcode.php');

class BCGDrawing {
	const IMG_FORMAT_PNG = 1;
	const IMG_FORMAT_JPEG = 2;

	private $w, $h;  // int
	private $color;  // BCGColor
	private $filename; // char *
	private $im;  // {object}
	private $barcode; // BCGBarcode

	public function __construct($filename, BCGColor $color) {
		$this->im = null;
		$this->filename = $filename;
		$this->color = $color;
	}

	public function __destruct() {
		$this->destroy();
	}

	private function init() {
		if ($this->im === null) {
			$this->im = imagecreatetruecolor($this->w, $this->h)
				or die('Can\'t Initialize the GD Libraty');
			imagefilledrectangle($this->im, 0, 0, $this->w - 1, $this->h - 1, $this->color->allocate($this->im));
		}
	}

	public function get_im() {
		return $this->im;
	}

	public function set_im(&$im) {
		$this->im = $im;
	}

	public function setBarcode(BCGBarcode $barcode) {
		$this->barcode = $barcode;
	}

	public function draw() {
		$size = $this->barcode->getMaxSize();
		$this->w = $size[0];
		$this->h = $size[1];
		$this->init();
		$this->barcode->draw($this->im);
	}

	public function finish($image_style = self::IMG_FORMAT_PNG, $quality = 100) {
		if ($image_style === self::IMG_FORMAT_PNG) {
			if (empty($this->filename)) {
				imagepng($this->im);
			} else {
				imagepng($this->im, $this->filename);
			}
		} elseif ($image_style === self::IMG_FORMAT_JPEG) {
			imagejpeg($this->im, $this->filename, $quality);
		}
	}

	public function destroy() {
		@imagedestroy($this->im);
	}

}

;
?>