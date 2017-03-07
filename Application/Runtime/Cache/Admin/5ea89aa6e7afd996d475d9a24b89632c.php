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
				<table class="table table-striped table-bordered" id="dataTable">
					<thead>
						<tr>
							<th>id</th>
							<th>账号</th>
							<th>昵称</th>
							<th class="hidden-xs">注册时间</th>
							<th class="hidden-xs">状态</th>
							<th>操作</th>
						</tr>
					</thead>
					<tbody>
						<?php if(is_array($list)): foreach($list as $key=>$v): ?><tr id="data-<?php echo ($v['id']); ?>">
							<td><?php echo ($v['id']); ?></td>
							<td><?php echo ($v['mobile']); ?></td>
							<td><?php echo ($v['realname']); ?></td>
							<td class="hidden-xs"><?php echo (date("Y-m-d H:i:s",$v['reg_date'])); ?></td>
							<td class="hidden-xs">
								<?php if($v['is_register'] == 1): ?><span class="label label-success">已审核</span>
								<?php else: ?>
								<span class="label label-danger">未审核</span><?php endif; ?>
							</td>
							<td>
								<a href="javascript:checkRegister(<?php echo ($v['id']); ?>,<?php echo ($v['is_register']); ?>);" class="btn btn-manager btn-info btn-xs" data-toggle="tooltip" data-placement="top" title="审核"><i class="fa fa-pencil"></i></a>
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
	function checkRegister(id,is_register){
		if (is_register == 1) {return};
		DMS.dialog("通过审核?",function(){
			DMS.ajaxPost("/manager.php?s=/Admin/checkRegister",{id:id},function(ret){
				if(ret.status==1){
	            	DMS.success("审核成功",0,function(){
						if(ret.url){
	            			window.location.href = ret.url;
	            		}else{
	            			window.location.href = window.location.href;
	            		}
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