<?php
	function currentClassify($id){
    	$classifydb = M('Goods_classify');
    	$where['id'] = $id;
		$r =$classifydb->where($where)->find();
		$str = '';
		if($r['parent_id']) {
			$str = currentClassify($r['parent_id']);
		}
		return $str.L($r['classify_name']).' > ';
    }