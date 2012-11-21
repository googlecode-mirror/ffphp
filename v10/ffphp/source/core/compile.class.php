<?php
// +----------------------------------------------------------------------
// | 模版编译类
// +----------------------------------------------------------------------
// | [Framework For PHP] 超轻量级PHP框架 (简称:FFPHP)
// +----------------------------------------------------------------------
// | Licensed  (http://www.apache.org/licenses/LICENSE-2.0)
// | Copyright (c) 2012 http://www.ffphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 张通 (tongseo@gmail.com)
// | File ID: 7
// +----------------------------------------------------------------------

class compile{
	static $assignVar;
	//模板编译类
	static function input($content,$assignVar){
		self::$assignVar=$assignVar;
		//处理包含
		$content=self::include_replace($content);
		//去除注释
		$arr1[]='/<\{\s*\/\*.*\*\/\s*\}>/';
		$arr2[]='';
		$content=preg_replace($arr1,$arr2,$content);
		//处理循环
		$content=self::variable_replace(self::foreach_replace($content));
		$content=self::tags_replace($content);
		//处理函数
	 	return self::function_replace($content);

	}

	//处理循环函数
	static function foreach_replace($inputCon){
		//获取循环主体		
		preg_match('/<\{\s*foreach[^\}]*\}>.*<\{\s*\/\s*foreach\s*\}\>/s',$inputCon,$foreachCon);
		if(isset($foreachCon[0])){
		preg_match_all('/<\{\s*foreach[^\}]*\}>|<\{\s*\/\s*foreach\s*\}\>/s',$foreachCon[0],$arr);
		//解析数等级并标记	
		$level=0;
		$levelArr1=array();
		$levelArr2=array();
		for($i=0;$i<count($arr[0]);$i++){
			if(preg_match('/<\{\s*foreach/',$arr[0][$i])){
				//对循环主体添加标记
				$level+=101;
				$levelArr[]=preg_replace('/<\{\s*foreach/is','<{foreach'.$level,$arr[0][$i]);
			}elseif(preg_match('/<\{\s*\/\s*foreach/is',$arr[0][$i])){
				//对循环结尾格式化
				$level-=100;
				$levelArr[]='<{/foreach}>';
			}
		}
		//处理循环主体标记处理
		$content1=str_replace($arr[0],$levelArr,$foreachCon[0]);		
		//切割一维循环
		$arr1=preg_split('/<\{foreach1/',$content1,-1,PREG_SPLIT_NO_EMPTY);
		//补全循环主体
		$arr2=preg_replace('/^(.*)<\{\/foreach\}>.*/s','<{foreach1\1<{/foreach}>',$arr1);
		$arr3=array();

		for($i=0;$i<count($arr2);$i++){
		$varArr1=$varArr2=array();
			$itemArr=array();
			//解析循环主体中的参数
			preg_match_all('/<\{foreach(\d\d\d).*from\s*=\s*\'?(\S+)\'?.*item\s*=\s*\'?(\w+)\'?.*\}>/',$arr2[$i],$itemArr1,PREG_SET_ORDER);
			foreach($itemArr1 as $v){
				$itemArr[$v[1]]['c']=$v[0];
				$itemArr[$v[1]]['from']=$v[2];
				$itemArr[$v[1]]['key']=null;
				$itemArr[$v[1]]['item']=$v[3];
				$itemArr[$v[1]]['r']="<?php foreach (".self::variable_replace($v[2])." as $$v[3]){ ?>";
			}
			//解析循环主体中的参数			
			preg_match_all('/<\{foreach(\d\d\d).*item\s*=\s*\'?(\w+)\'?.*from\s*=\s*\'?(\S+)\'?.*\}>/',$arr2[$i],$itemArr2,PREG_SET_ORDER);
			foreach($itemArr2 as $v){
				$itemArr[$v[1]]['c']=$v[0];
				$itemArr[$v[1]]['from']=$v[3];
				$itemArr[$v[1]]['key']=null;
				$itemArr[$v[1]]['item']=$v[2];
				$itemArr[$v[1]]['r']="<?php foreach (".self::variable_replace($v[2])." as $$v[2]){ ?>";
				
			}

			//补充解析循环主体中的参数
			preg_match_all('/<\{foreach(\d\d\d).*item\s*=\s*\'?(\w+)\'?.*key\s*=\s*\'?(\w+)\'?.*\}>/',$arr2[$i],$itemArr3,PREG_SET_ORDER);
			foreach($itemArr3 as $v){
				$itemArr[$v[1]]['c']=$v[0];
				$itemArr[$v[1]]['key']=$v[3];
				$itemArr[$v[1]]['item']=$v[2];
				$itemArr[$v[1]]['r']="<?php foreach (".self::variable_replace($itemArr[$v[1]]['from'])." as $$v[3] => $$v[2]){ ?>";
				
			}

			//补充解析循环主体中的参数
			preg_match_all('/<\{foreach(\d\d\d).*key\s*=\s*\'?(\w+)\'?.*item\s*=\s*\'?(\w+)\'?.*\}>/',$arr2[$i],$itemArr4,PREG_SET_ORDER);
			foreach($itemArr4 as $v){
				$itemArr[$v[1]]['c']=$v[0];
				$itemArr[$v[1]]['key']=$v[2];
				$itemArr[$v[1]]['item']=$v[3];
				$itemArr[$v[1]]['r']="<?php foreach (".self::variable_replace($itemArr[$v[1]]['from'])." as $$v[2] => $$v[3]){ ?>";
			}
			$arr3[$i]=$arr2[$i];

			$varArr1[]='/\$(\w+)\.(\w*)\.(\w*)\.(\w*)/';
			$varArr2[]='$\1[\'\2\'][\'\3\'][\'\4\']';

			$varArr1[]='/\$(\w+)\.(\w*)\.(\w*)/';
			$varArr2[]='$\1[\'\2\'][\'\3\']';

			$varArr1[]='/\$(\w+)\.(\w+)/';
			$varArr2[]='$\1[\'\2\']';
	
			foreach($itemArr as $key => $item){
			//添加标签替换数组
			$varArr1[]='/<\{\s*\$'.$item['item'].'(.*?)\}>/';
			$varArr2[]='<?php echo $'.$item['item'].'\1 ?>';

				if($item['key']){
				$varArr1[]='/<\{\s*\$'.$item['key'].'(.*?)\}>/';
				$varArr2[]='<?php echo $'.$item['key'].'\1 ?>';
				}
			$arr3[$i]=str_replace($item['c'],$item['r'],$arr3[$i]);
				$arr3[$i]=str_replace('<{/foreach}>','<?php } ?>',$arr3[$i]);
			}
			$arr3[$i]=preg_replace($varArr1,$varArr2,$arr3[$i]);
		}

		$foreachNewCon=str_replace($arr2,$arr3,$content1);
		return str_replace($foreachCon[0],$foreachNewCon,$inputCon);

		}else{
		return $inputCon;
		}
	}

	//变量替换，支持到四维数
	static function variable_replace($max){
		
		$arr1[]='/\$(\w+)\.(\w+)\.(\w+)\.(\w+)/';
		$arr2[]='$\1[\'\2\'][\'\3\'][\'\4\']';
		$arr1[]='/\$(\w+)\.(\w+)\.(\w*)/';
		$arr2[]='$\1[\'\2\'][\'\3\']';
		$arr1[]='/\$(\w+)\.(\w+)/';
		$arr2[]='$\1[\'\2\']';

		foreach (self::$assignVar as $var){
			$arr1[]='/\$'.$var.'/';
			$arr2[]='$this->var[\''.$var.'\']';
		}
		if(is_array($max)){
			for($i=0;$i<count($max);$i++){
				$max[$i]=preg_replace($arr1,$arr2,$max[$i]);
			}
		}elseif(is_string($max)){
			$max=preg_replace($arr1,$arr2,$max);
		}
		return $max;
	}
	static function include_replace($inputCon){
		preg_match_all('/<\{\s*include\s+file=\"?\'?(.*?)\'?\"?\s*\}>/',$inputCon,$arr);
		for($i=0;$i<count($arr[1]);$i++){
				$arr[1][$i]=file_get_contents(_APP_.'template/'.C('TEMPLATE_STYLE').'/'.$arr[1][$i]);
		}
		return str_replace($arr[0],$arr[1],$inputCon);
	}
	static function tags_replace($inputCon){
		preg_match_all('/<\{(.*?)\}>/',$inputCon,$arr);
		$arr1[]='/\s*\/\s*if\s*/';
		$arr2[]='<?php } ?>';
		$arr1[]='/^\s*else\s*$/';
		$arr2[]='<?php }else{ ?>';
		$arr1[]='/\s*elseif(.*)/';
		$arr2[]='<?php }elseif(\1){ ?>';
		$arr1[]='/^\s*if(.*)/';
		$arr2[]='<?php if(\1){ ?>';
		$arr1[]='/^\s*(\$.*)/s';
		$arr2[]='<?php echo \1 ?>';	
		$arr[1]=preg_replace($arr1,$arr2,$arr[1]);
		return str_replace($arr[0],$arr[1],$inputCon);	
	}

	static function function_replace($inputCon){
		$arr1[]='/<\?php echo \$([\w-\>\[\]\'\"]+)\s*\|\s*(\w+)\s*\?>/';
		$arr2[]='<?php echo \2($\1)?>';
		$arr1[]='/<\?php echo \$([\w-\>\[\]\'\"]+)\s*\|\s*date_format\s*:\s*[\'\"](.*?)[\'\"]\s*\?>/';
		$arr2[]='<?php echo date(\'\2\',$\1)?>';
		return preg_replace($arr1,$arr2,$inputCon);
	}
}
