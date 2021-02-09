<?php

/*
  PHP version 5
  Copyright (c) 2002-2013 ECISP.CN、EarcLink.COM
  警告：这不是一个免费的软件，请在许可范围内使用，请尊重知识产权，侵权必究，举报有奖！
  作者：黄祥云 E-mail:6326420@qq.com  QQ:6326420 TEL:18665655030

  ESPCMS官网介绍：http://www.ecisp.cn 企业建站：http://www.earclink.cn
 */

class ectemplates_image {

	private $bgcolor = '#fff';
	private $dstW = 200;
	private $dstH = 200;
	private $picquality = 90;

	function imagecreat($dstW = 200, $dstH = 200, $bgcolor = '#fff', $picfile = '', $path = '', $iszoom = 1) {
		$RGBcolor = $this->color2rgb($bgcolor);

		$mime = $this->getMimeType($picfile);

		$imagetype = GetImageSize($picfile);

		$srcW = $imagetype[0];

		$srcH = $imagetype[1];

		$dstWH = $dstW / $dstH;

		$srcWH = $srcW / $srcH;

		if ($dstWH <= $srcWH) {

			$ftoW = $dstW;
			$ftoH = $ftoW * ($srcH / $srcW);
		} else {

			$ftoH = $dstH;
			$ftoW = $ftoH * ($srcW / $srcH);
		}

		switch ($imagetype[2]) {
			case 1:
				$im = @ImageCreateFromGIF($picfile);
				break;
			case 2:
				$im = @ImageCreateFromJPEG($picfile);
				break;
			case 3:
				$im = @ImageCreateFromPNG($picfile);
				break;
		}

		$nowpicname = basename($picfile);

		$newsfilepathname = $path;

		if ($iszoom == 1) {
			if ($srcW > $dstW || $srcH > $dstH) {

				if ($ftoH == $dstH) {
					$dstleft = ($dstW - $ftoW) / 2;
					$dsttop = 0;
				} else {
					$dstleft = 0;
					$dsttop = ($dstH - $ftoH) / 2;
				}

				if (function_exists('imagecreatetruecolor')) {

					$ni = ImageCreateTrueColor($dstW, $dstH);

					$bgcolor = ImageColorAllocate($ni, $RGBcolor['r'], $RGBcolor['g'], $RGBcolor['b']);

					imagefilledrectangle($ni, 0, 0, $dstW, $dstH, $bgcolor);

					ImageCopyResampled($ni, $im, $dstleft, $dsttop, 0, 0, $ftoW, $ftoH, $srcW, $srcH);
				} else {
					$ni = ImageCreate($dstW, $dstW);

					ImageCopyResized($ni, $im, 0, 0, 0, 0, $ftoW, $ftoH, $srcW, $srcH);
				}
			} else {

				$dstleft = ($dstW - $srcW) / 2;
				$dsttop = ($dstH - $srcH) / 2;

				if (function_exists('imagecreatetruecolor')) {

					$ni = ImageCreateTrueColor($dstW, $dstH);

					$bgcolor = ImageColorAllocate($ni, $RGBcolor['r'], $RGBcolor['g'], $RGBcolor['b']);

					imagefilledrectangle($ni, 0, 0, $dstW, $dstH, $bgcolor);

					ImageCopyResampled($ni, $im, $dstleft, $dsttop, 0, 0, $srcW, $srcH, $srcW, $srcH);
				} else {
					$ni = ImageCreate($dstW, $dstW);

					ImageCopyResized($ni, $im, 0, 0, 0, 0, $ftoW, $ftoH, $srcW, $srcH);
				}
			}
		} elseif ($iszoom == 2) {

			$ni = ImageCreateTrueColor($dstW, $dstH);
			if ($dstWH < $srcWH) {
				$cH = $dstH;
				$cW = round($dstH * ($srcW / $srcH), 0);
				$cwX = (($cW / 2) - ($dstW / 2)) * ($srcH / $dstH);
				$chY = 0;
			} else {
				$cW = $dstW;
				$cH = round($dstW * ($srcH / $srcW));
				$cwX = 0;
				$chY = (($cH / 2) - ($dstH / 2)) * ($srcW / $dstW);
			}
			if ($ni) {
				ImageCopyResampled($ni, $im, 0, 0, $cwX, $chY, $cW, $cH, $srcW, $srcH);
			} else {
				$ni = ImageCreate($dstW, $dstH);
				ImageCopyResized($ni, $im, 0, 0, $cwX, $chY, $cW, $cH, $srcW, $srcH);
			}
		} elseif ($iszoom == 3) {

			$ni = ImageCreateTrueColor($dstW, $dstH);
			if ($dstWH < $srcWH) {
				$cH = $dstH;
				$cW = round($dstH * ($srcW / $srcH), 0);
			} else {
				$cW = $dstW;
				$cH = round($dstW * ($srcH / $srcW));
			}
			if ($ni) {
				ImageCopyResampled($ni, $im, 0, 0, 0, 0, $cW, $cH, $srcW, $srcH);
			} else {
				$ni = ImageCreate($dstW, $dstH);
				ImageCopyResized($ni, $im, 0, 0, 0, 0, $cW, $cH, $srcW, $srcH);
			}
		} else {

			$ni = ImageCreateTrueColor($ftoW, $ftoH);
			if ($ni) {
				ImageCopyResampled($ni, $im, 0, 0, 0, 0, $ftoW, $ftoH, $srcW, $srcH);
			} else {
				$ni = ImageCreate($ftoW, $ftoH);
				ImageCopyResized($ni, $im, 0, 0, 0, 0, $ftoW, $ftoH, $srcW, $srcH);
			}
		}


		switch ($mime) {
			case 'gif':

				imagegif($ni, $newsfilepathname, $this->picquality);
				break;
			case 'jpg':

				imagejpeg($ni, $newsfilepathname, $this->picquality);
				break;
			case 'jpeg':

				imagejpeg($ni, $newsfilepathname, $this->picquality);
				break;
			case 'png':

				imagepng($ni, $newsfilepathname);
				break;
		}
		ImageDestroy($ni);

		$newpicname = basename($newsfilepathname);
		return $newpicname;
	}

	function color2rgb($hexColor) {
		$color = str_replace('#', '', $hexColor);
		if (strlen($color) > 3) {
			$rgb = array(
			    'r' => hexdec(substr($color, 0, 2)),
			    'g' => hexdec(substr($color, 2, 2)),
			    'b' => hexdec(substr($color, 4, 2))
			);
		} else {
			$color = str_replace('#', '', $hexColor);
			$r = substr($color, 0, 1) . substr($color, 0, 1);
			$g = substr($color, 1, 1) . substr($color, 1, 1);
			$b = substr($color, 2, 1) . substr($color, 2, 1);
			$rgb = array(
			    'r' => hexdec($r),
			    'g' => hexdec($g),
			    'b' => hexdec($b)
			);
		}
		return $rgb;
	}

	function getMimeType($file) {
		return is_dir($file) ? '文件夹' : $this->mime($file);
	}

	public function mime($file) {

		$file = realpath($file);

		$options = pathinfo($file, PATHINFO_EXTENSION);
		return $options;
	}

}

?>
