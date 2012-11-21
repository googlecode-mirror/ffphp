<?php
// +----------------------------------------------------------------------
// | 分布类
// +----------------------------------------------------------------------
// | [Framework For PHP] 超轻量级PHP框架 (简称:FFPHP)
// +----------------------------------------------------------------------
// | Licensed  (http://www.apache.org/licenses/LICENSE-2.0)
// | Copyright (c) 2012 http://www.ffphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 张通 (tongseo@gmail.com)
// | File ID: 18
// +----------------------------------------------------------------------

class Page{
	public $page;
	private $data;
	private $thisPage;
	private $num;
	private $pageNum;
	private $offset;
	private $page_num;

	function __construct($data,$num=5){
		if(isset($_GET['page'])){
			($_GET['page']==1) and header("Location: {$_SERVER['SCRIPT_NAME']}/{$_GET['m']}/{$_GET['a']}");
			
			 $this->thisPage=intval($_GET['page']);
		}else
			$this->thisPage=1;
		
		$this->data=$data;
		$this->num=$num;
		$this->pageNum=$this->getPageNum();
		if($this->thisPage!=1){
		if($this->thisPage<1 or $this->thisPage>$this->pageNum)
			debug::error('没有找到分页!',219001);
		}
		$this->offset=$this->getOffset();
		$this->pageHtml();
	}
	private function getPageNum(){
		return $this->page_num=ceil($this->data/$this->num);
	}
	private function getNextPage(){
		if($this->thisPage==$this->pageNum)
			return false;
		else
			return $this->thisPage+1;
	}
	private function getPrevPage(){
		if($this->thisPage==1)
			return false;
		else
			return $this->thisPage;
	}
	private function getOffset(){
		return ($this->thisPage-1)*$this->num;
	}
	private function getStartNum(){
		if($this->data==0)
			return 0;
		else
			return $this->offset;
	}
	private function getEndNum(){
		return min($this->offset+$this->num,$this->data);
	}
	private function getSartLimit(){
		if($this->thisPage==1){
			return 0;
		}else
			return ($this->thisPage-1)*$this->num;
	}
	function getPage(){
		$pageInfo=array(
			"row_data"=>$this->data,
			"limit_num"=>$this->num,
			"page_num"=>$this->page_num,
			"row_offset"=>$this->getOffset(),
			"next_page"=>$this->getNextPage(),
			"prev_page"=>$this->getPrevPage(),
			"page_start"=>$this->getStartNum(),
			"page_end"=>$this->getEndNum(),
			"current_page"=>$this->thisPage,
			'limit_start'=>$this->getSartLimit()
		);
		return $pageInfo;
	}
	function pageHtml(){
		if($this->page_num==1)
			$this->page='';
		elseif($this->page_num<=10){
			for($i=1;$i<=$this->page_num;$i++){
				$this->page.="<a href=\"{$_SERVER['SCRIPT_NAME']}/{$_GET['m']}/{$_GET['a']}/page/{$i}\">{$i}</a>";
			}
			if($this->thisPage>1)
				$this->page=$this->strhtml($this->thisPage-1,'上一页').$this->page;
			if($this->thisPage<$this->pageNum)
				$this->page.=$this->strhtml($this->thisPage+1,'下一页');
		}else{
			$pageArr=array();
			for($i=1;$i<6;$i++){
				if($this->thisPage-$i==0)
					break;
				array_unshift($pageArr,$this->thisPage-$i);				
			}
			for($y=0;$y<=(10-$i);$y++){
				if($this->thisPage+$y>$this->page_num)
					break;
				array_push($pageArr,$this->thisPage+$y);
			}
			if($i+$y<10){
				for(;$i<=10-$y;$i++){
				array_unshift($pageArr,$this->thisPage-$i);	
				}
			}
			foreach ($pageArr as $pageA){
				$this->page.=$this->strhtml($pageA);
			}
			if(array_shift($pageArr)>1)
				$this->page=$this->strhtml(1,'1..').$this->page;
			if(array_pop($pageArr)<$this->pageNum)
				$this->page.=$this->strhtml($this->page_num,$this->page_num.'...');
			if($this->thisPage>1)
				$this->page=$this->strhtml($this->thisPage-1,'上一页').$this->page;
			if($this->thisPage<$this->pageNum)
				$this->page.=$this->strhtml($this->thisPage+1,'下一页');
		}
	}
	function strhtml($id,$name=null){
		$str=$name?$name:$id;
		return "<a href=\"{$_SERVER['SCRIPT_NAME']}/{$_GET['m']}/{$_GET['a']}/page/{$id}\">{$str}</a>\n";
		
	}

}

?>
