<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>章鱼云销</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	    <link href="<?php echo C('ADMIN_CSS_PATH');?>/bootstrap.min.css" rel="stylesheet">
		<link href="<?php echo C('ADMIN_CSS_PATH');?>/font-awesome.min.css" rel="stylesheet">
		<link href="<?php echo C('ADMIN_CSS_PATH');?>/common.css" rel="stylesheet">
		<script src="<?php echo C('ADMIN_JS_PATH');?>/jquery-1.11.1.min.js"></script>
	    <script src="<?php echo C('ADMIN_JS_PATH');?>/bootstrap.min.js" type="text/javascript"></script>
	    <script src="<?php echo C('ADMIN_JS_PATH');?>/common.js"></script>
	    <script src="<?php echo C('ADMIN_JS_PATH');?>/jquery.slimscroll.min.js"></script>
	    <script src="<?php echo C('ADMIN_JS_PATH');?>/admin_template.js"></script>
	    <script src="<?php echo C('ADMIN_JS_PATH');?>/admin.js"></script>
	    <script src="<?php echo C('ADMIN_JS_PATH');?>/cookie.js"></script>
    </head>
    <body class="overflow-hidden">
<div class="padding-md">
	<div class="smart-widget widget-dark-blue">
		<div class="smart-widget-inner">
			<div class="smart-widget-body">
				<div class="row">
					<div class="col-md-6">
						<a href="/manager.php?s=/Goods/goodsAdd" class="btn btn-sm btn-info">
		                    <i class="fa fa-plus"></i>增加商品
		                </a>
		                <a href="javascript:manager('lock')" class="btn btn-sm btn-success">禁止</a>
		                <a href="javascript:manager('unlock')" class="btn btn-sm btn-success">解除禁止</a>
		                <a href="javascript:manager('delete')" class="btn btn-sm btn-danger">删除</a>
					</div>
					<div class="col-md-6">
						<div class="input-group pull-right col-md-6" id="search-form">
							<input type="text" name="keyword" class="form-control input-sm" placeholder="用户昵称" value="<?php echo ($searchInfo['keyword']); ?>">
							<span class="input-group-btn">
								<a href="javascript:;" url="/manager.php?s=/Goods/index" id="search" class="btn btn-sm btn-success">搜索</a>
							</span>
						</div>
					</div>
				</div>
				<table class="table table-striped table-bordered m-top-sm" id="dataTable">
					<thead>
						<tr>
							<th>
								<!-- <input type="checkbox" class="check-all" /> -->
								<div class="custom-checkbox">
									<input type="checkbox" id="checkall" class="check-all" >
									<label for="checkall"></label>
								</div>
							</th>
							<th>商品ID</th>
							<th>所属商户</th>
							<th>商品名称</th>
							<th>商品编号</th>
							<th>商品图片</th>
							<th class="hidden-xs">商品上架时间</th>
							<th>是否上架</th>
							<th>是否可以下单</th>
							<th class="hidden-xs">商品更新时间</th>
							<th>操作</th>
						</tr>
					</thead>
					<tbody>
						<?php if(is_array($list)): foreach($list as $key=>$v): ?><tr id="data-<?php echo ($v['goods_id']); ?>">
							<td>
								<div class="custom-checkbox">
									<input type="checkbox" value="<?php echo ($v['goods_id']); ?>" name="goodsids[]" id="goods_id-<?php echo ($v['goods_id']); ?>" class="ids" >
									<label for="goods_id-<?php echo ($v['goods_id']); ?>"></label>
								</div>
							</td>
							<td><?php echo ($v['goods_id']); ?></td>
							<td><?php echo (get_admin_name($v['uid'])); ?></td>
							<td><?php echo ($v['goods_name']); ?></td>
							<td><?php echo ($v['goods_sn']); ?></td>
							<td>
								<?php if($v['goods_original_img']): ?><img src="<?php echo ($v['goods_original_img']); ?>" class="avatar" />&nbsp;<?php endif; ?>
							</td>
							<td class="hidden-xs"><?php echo (date("Y-m-d H:i:s",$v['goods_ontime'])); ?></td>
							<td>
								<?php if($v['is_on_sale'] == 1): ?><span class="label label-success">上架</span>
								<?php else: ?>
								<span class="label label-danger">下架</span><?php endif; ?>
							</td>
							<td>
								<?php if($v['is_commit'] == 1): ?><span class="label label-success">是</span>
								<?php else: ?>
								<span class="label label-danger">否</span><?php endif; ?>
							</td>
							<td class="hidden-xs"><?php echo (date("Y-m-d H:i:s",$v['goods_update_time'])); ?></td>
							<td>
								<a href="javascript:showInfo(<?php echo ($v['goods_id']); ?>)" class="btn btn-manager btn-warning btn-xs" data-toggle="tooltip" data-placement="top" title="详情" data-original-title="详情"><i class="fa fa-info"></i></a>
								<a href="/manager.php?s=/Goods/goodsEdit/goods_id/<?php echo ($v['goods_id']); ?>" class="btn btn-manager btn-info btn-xs" data-toggle="tooltip" data-placement="top" title="编辑"><i class="fa fa-pencil"></i></a>
								<a href="javascript:goodsDelete(<?php echo ($v['goods_id']); ?>);" class="btn btn-manager btn-danger btn-xs" data-toggle="tooltip" data-placement="top" title="删除"><i class="fa fa-trash-o"></i></a>
							</td>
						</tr><?php endforeach; endif; ?>
					</tbody>
				</table>
				<div class="content text-right">
					<ul class="pagination">
						<?php echo ($page); ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	//搜索功能
	$("#search").click(function(){
		var url = $(this).attr('url');
        var query  = $('#search-form').find('.input-sm').serialize();
        // if(!$('input[name="keyword"]').val()){
        // 	DMS.alert("请输入关键词关键词");
        // 	return;
        // }
        if( url.indexOf('?')>0 ){
            url += '&' + query;
        }else{
            url += '?' + query;
        }
		window.location.href = url;
	});
	$(".check-all").click(function(){
		$(".ids").prop("checked", this.checked);
	});
	$(".ids").click(function(){
		var option = $(".ids");
		option.each(function(i){
			if(!this.checked){
				$(".check-all").prop("checked", false);
				return false;
			}else{
				$(".check-all").prop("checked", true);
			}
		});
	});
	function manager(type){
		var goodsids='';
		$("input[name='goodsids[]']:checked").each(function(i, n){
			goodsids += $(n).val() + ',';
		});
		goodsids = goodsids.substring(0,goodsids.length-1);

		if(goodsids=='') {
			DMS.alert("请先选择用户")
			return false;
		}else{
			switch (type) {
				case 'lock':
					var url = "/manager.php?s=/Goods/memberLock";
					break;
				case 'unlock':
					var url = "/manager.php?s=/Goods/memberUnLock";
					break;
				case 'delete':
					var url = "/manager.php?s=/Goods/membersDelete";
					break;
			}
			if(url){
				DMS.dialog("确定要执行当前操作吗?",function(){
					DMS.ajaxPost(url,{goodsids:goodsids},function(ret){
						if(ret.status==1){
							DMS.success(ret.info,0,function(){
		                		window.location.href = window.location.href;
							});
		                }else{
		                	DMS.error(ret.info,0);
		                }
					})
				});
			}
		}
	}
	function goodsAdd(){
		DMS.ajaxShow("添加商品","/manager.php?s=/Goods/goodsAdd");
	}
	function goodsEdit(goods_id){
		DMS.ajaxShow("编辑商品","/manager.php?s=/Goods/goodsEdit/goods_id/"+goods_id);
	}
	function reSearch(){
		window.location.href = "/manager.php?s=/Goods/index";
	}
	function showInfo(goods_id){
		DMS.loadUrl("用户详情","/manager.php?s=/Goods/goodsInfo/goods_id/"+goods_id);
	}
	function goodsDelete(goods_id){
		DMS.dialog("确定要删除吗?",function(){
			DMS.ajaxPost("/manager.php?s=/Goods/goodsDelete",{goods_id:goods_id},function(ret){
				if(ret.status==1){
                	DMS.success(ret.info,0,function(){
                		$("#data-"+goods_id).remove();
					});
                }else{
                	DMS.error(ret.info,0);
                }
			})
		});
	}
</script>
    </body>
</html>