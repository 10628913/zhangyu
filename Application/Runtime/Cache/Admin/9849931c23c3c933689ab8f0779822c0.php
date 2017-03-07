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
						<a href="javascript:clientAdd();" class="btn btn-sm btn-info">增加客户
			            </a>
		                <a href="javascript:manager('delete');" class="btn btn-sm btn-danger">删除</a>
					</div>
					<div class="col-md-6">
						<div class="input-group pull-right col-md-6" id="search-form">
							<input type="text" name="keyword" class="form-control input-sm" placeholder="用户昵称" value="<?php echo ($searchInfo['keyword']); ?>">
							<span class="input-group-btn">
								<a href="javascript:;" url="/manager.php?s=/Client/index" id="search" class="btn btn-sm btn-success">搜索</a>
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
							<th>ID</th>
							<th>昵称</th>
							<th>手机号</th>
							<th>所属上级</th>
							<th>所属上级类型</th>
							<th class="hidden-xs">注册时间</th>
							<th>操作</th>
						</tr>
					</thead>
					<tbody>
						<?php if(is_array($list)): foreach($list as $key=>$v): ?><tr id="data-<?php echo ($v['client_id']); ?>">
							<td>
								<div class="custom-checkbox">
									<input type="checkbox" value="<?php echo ($v['client_id']); ?>" name="clientids[]" id="client_id-<?php echo ($v['client_id']); ?>" class="ids" >
									<label for="client_id-<?php echo ($v['client_id']); ?>"></label>
								</div>
							</td>
							<td><?php echo ($v['client_id']); ?></td>
							<td>
								<?php if($v['client_avatar']): ?><img src="<?php echo ($v['client_avatar']); ?>" class="avatar" />&nbsp;<?php endif; echo ($v['client_name']); ?>
							</td>
							<td><?php echo ($v['client_mobile']); ?></td>
							<td>
								<?php if($v['parent_type'] == 0): echo (getClientAdminName($v['parent_id'])); ?>
									<?php else: echo (getClientMemberName($v['parent_id'])); endif; ?>
							</td>
							<td>
								<?php if($v['parent_type'] == 0): ?>一手
									<?php else: ?>代理<?php endif; ?>
							</td>
							<td class="hidden-xs"><?php echo (date("Y-m-d H:i:s",$v['client_addtime'])); ?></td>
							<td>
								<a href="javascript:showInfo(<?php echo ($v['client_id']); ?>)" class="btn btn-manager btn-warning btn-xs" data-toggle="tooltip" data-placement="top" title="详情" data-original-title="详情"><i class="fa fa-info"></i></a>
								<a href="javascript:clientEdit(<?php echo ($v['client_id']); ?>)" class="btn btn-manager btn-info btn-xs" data-toggle="tooltip" data-placement="top" title="编辑"><i class="fa fa-pencil"></i></a>
								<a href="javascript:clientDelete(<?php echo ($v['client_id']); ?>);" class="btn btn-manager btn-danger btn-xs" data-toggle="tooltip" data-placement="top" title="删除"><i class="fa fa-trash-o"></i></a>
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
	$("#search").click(function(){
		var url = $(this).attr('url');
        var query  = $('#search-form').find('.input-sm').serialize();
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
		var clientids='';
		$("input[name='clientids[]']:checked").each(function(i, n){
			clientids += $(n).val() + ',';
		});
		clientids = clientids.substring(0,clientids.length-1);

		if(clientids=='') {
			DMS.alert("请先选择用户")
			return false;
		}else{
			var url = "/manager.php?s=/Client/clientsDelete";
			// switch (type) {
			// 	case 'lock':
			// 		var url = "/manager.php?s=/Client/clientLock";
			// 		break;
			// 	case 'unlock':
			// 		var url = "/manager.php?s=/Client/clientUnLock";
			// 		break;
			// 	case 'delete':
			// 		var url = "/manager.php?s=/Client/clientDelete";
			// 		break;
			// }
			if(url){
				DMS.dialog("确定要执行当前操作吗?",function(){
					DMS.ajaxPost(url,{clientids:clientids},function(ret){
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
	};
	function clientAdd(){
		DMS.ajaxShow("新增客户","/manager.php?s=/Client/clientAdd");
	}
	function clientEdit(client_id){
		DMS.ajaxShow("编辑用户","/manager.php?s=/Client/clientEdit/client_id/"+client_id);
	}
	// function clientAdd(){
	// 	DMS.ajaxShow("编辑用户","/manager.php?s=/Client/clientAdd");
	// }
	// function clientEdit(client_id){
	// 	DMS.ajaxShow("编辑用户","/manager.php?s=/Client/clientEdit/client_id/"+client_id);
	// }
	function showInfo(client_id){
		DMS.loadUrl("用户详情","/manager.php?s=/Client/clientInfo/client_id/"+client_id);
	}
	function clientDelete(client_id){
		DMS.dialog("确定要删除吗?",function(){
			DMS.ajaxPost("/manager.php?s=/Client/clientDelete",{client_id:client_id},function(ret){
				if(ret.status==1){
                	DMS.success(ret.info,0,function(){
                		$("#data-"+client_id).remove();
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