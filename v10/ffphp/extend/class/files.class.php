<?php
// +----------------------------------------------------------------------
// | 文件上传类
// +----------------------------------------------------------------------
// | [Framework For PHP] 超轻量级PHP框架 (简称:FFPHP)
// +----------------------------------------------------------------------
// | Licensed  (http://www.apache.org/licenses/LICENSE-2.0)
// | Copyright (c) 2012 http://www.ffphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 张通 (tongseo@gmail.com)
// | File ID: 17
// +----------------------------------------------------------------------
class Files{
	private $name;
	private $uploadPath='./upload/';
	private $maxSize='2000000';	//默认2M
	private $formName;
	private $allowSubFix=array('jpg','jpeg','png','gif');
	private $upPath;
	private $fileName;

	public $subFix;
       	
	function __construct($formName){
		$this->formName=$formName;
		$this->upPath=$this->uploadPath.date('Ym/d/');
	}
	function setPath($path){
		$this->upPath=$this->uploadPath.trim($path);
	}
	function setName($name){
		$this->fileName=trim($name);
	}


	public function setFix($arr){
		$this->allowSubFix=array_merge($this->allowSubFix,$arr);
	}
	public function setSize($v){
		$maxSize=$v;
	}
	public function run(){
		 $this->checkSize($_FILES[$this->formName]['size']);
		 $this->checkSubFix($_FILES[$this->formName]['name']);
		 $this->subFix=$this->getSubFix($_FILES[$this->formName]['name']);
		 $this->create();
		 if(move_uploaded_file($_FILES[$this->formName]['tmp_name'],$this->upPath.'/'.$this->fileName.'.'.$this->subFix))
			 return true;
		 else
			 return false;

	}

	private function checkSize($s){
		if($s>$this->maxSize)
			$this->error('文件大小超过指定大小！');
		
	}
	private function checkSubFix($n){
		if(!in_array($this->getSubFix($n),$this->allowSubFix))
			$this->error('文件后缀不合法！');
	
	}
	private function getSubFix($n){
		return strtolower(array_pop(explode('.',$n)));
	}
	private function create(){
		if(!file_exists($this->upPath)){
			mkdir($this->upPath,0755,true);
		}

	}

	private function error($str){
		debug::error($str,$str);
	}
}
