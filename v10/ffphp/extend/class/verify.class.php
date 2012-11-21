<?php
//文件编号19
		/**
		 * 验证码类
		 * __construct($width=60,$height=20,$codeNum=4,$codeMode=1);
		 * @width	输出图片的宽度
		 * @height	输出图片的高度
		 * @codeNum	验证码长度
		 * @codeMode	验证码模式 0,数字  1,大写字母 2,小写字母
		 *
		 * run();	启动类，输出验证码
		 * code();	获得，验证码中的字符串
		 */
class verify{
	private $width;
	private $height;
	private $codeNum;
	private $codeMode;
	private $checkCode;
	private $image;

	function __construct($width=60,$height=20,$codeNum=4,$codeMode=1){
		$this->width=$width;
		$this->height=$height;
		$this->codeNum=$codeNum;
		$this->codeMode=$codeMode;
		$this->checkCode=$this->createCheckCode();
	}
	
	function run(){
		$this->getCreateImage();
		$this->outputText();
		$this->setDisturbColor();
		$this->outputImage();
	}
	function code(){
		return $this->checkCode;
	}
	private function getCreateImage(){
		$this->image=imageCreate($this->width,$this->height);
		$back=imageColorAllocate($this->image,255,255,255);
		$border=imagecolorallocate($this->image,0,0,0);
		imageRectangle($this->image,0,0,$this->width-1,$this->height-1,$border);
	}
	private function createCheckCode(){
		$ascii_number='';
		for($i=0;$i<$this->codeNum;$i++){
			switch($this->codeMode){
			case 0:$rand_number=mt_rand(48,57);break;
			case 1:$rand_number=mt_rand(65,90);break;
			case 2:$rand_number=mt_rand(97,122);break;
			}
			$ascii=sprintf('%c',$rand_number);
			$ascii_number=$ascii_number.$ascii;
		}
		return $ascii_number;

	
	}

	private function setDisturbColor(){
		for($i=0;$i<=100;$i++){
			$color=imagecolorallocate($this->image,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
			imagesetpixel($this->image,mt_rand(1,$this->width-2),mt_rand(1,$this->height-2),$color);
		}
	}

	private function outputText(){
		for($i=0;$i<=$this->codeNum;$i++){
			$bg_color=imagecolorallocate($this->image,mt_rand(0,255),mt_rand(0,128),mt_rand(0,255));
			$x=floor($this->width/$this->codeNum)*$i+3;
			$y=mt_rand(0,$this->height-15);
			$str=substr($this->checkCode,$i,1);
			imagechar($this->image,5,$x,$y,$str,$bg_color);
		}
	}

	private function outputImage(){
		if(imagetypes()&IMG_GIF){
		header('content-type:image/gif');
		imagegif($this->image);
		}elseif(imagetypes()&IMG_JPG){
 		header('content-type:image/jpeg');
		imagejpeg($this->image,'',0.5);
		}elseif(imagetypes()&IMG_PNG){
		header('content-type:image/png');
		imagepng($this->image);
		}
 	}
	function __destruct(){
		imagedestroy($this->image);
	}
}

?>
