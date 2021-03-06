<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:63:"D:\park\public/../application/admin\view\carposs\enterport.html";i:1535705752;}*/ ?>
<!--_meta 作为公共模版分离出去-->
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="renderer" content="webkit|ie-comp|ie-stand">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
<meta http-equiv="Cache-Control" content="no-siteapp" />
<link rel="Bookmark" href="/favicon.ico" >
<link rel="Shortcut Icon" href="/favicon.ico" />
<!--[if lt IE 9]>
<script type="text/javascript" src="<?php echo config('admin_lib'); ?>/html5shiv.js"></script>
<script type="text/javascript" src="<?php echo config('admin_lib'); ?>/respond.min.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="<?php echo config('admin_static'); ?>/h-ui/css/H-ui.min.css" />
<link rel="stylesheet" type="text/css" href="<?php echo config('admin_static'); ?>/h-ui.admin/css/H-ui.admin.css" />
<link rel="stylesheet" type="text/css" href="<?php echo config('admin_lib'); ?>/Hui-iconfont/1.0.8/iconfont.css" />
<link rel="stylesheet" type="text/css" href="<?php echo config('admin_static'); ?>/h-ui.admin/skin/default/skin.css" id="skin" />
<link rel="stylesheet" type="text/css" href="<?php echo config('admin_static'); ?>/h-ui.admin/css/style.css" />
<script type="text/javascript" src="<?php echo config('admin_lib'); ?>/jquery/1.9.1/jquery.min.js"></script> 
<!--[if IE 6]>
<script type="text/javascript" src="<?php echo config('admin_lib'); ?>/DD_belatedPNG_0.0.8a-min.js" ></script>
<script>DD_belatedPNG.fix('*');</script>
<![endif]-->
<!--/meta 作为公共模版分离出去-->

<title>新建网站权限 - 管理员管理 - H-ui.admin v3.1</title>
<meta name="keywords" content="H-ui.admin v3.1,H-ui网站后台模版,后台模版下载,后台管理系统模版,HTML后台模版下载">
<meta name="description" content="H-ui.admin v3.1，是一款由国人开发的轻量级扁平化网站后台模板，完全免费开源的网站后台管理系统模版，适合中小型CMS后台系统。">
</head>
<body>
<article class="page-container">
	<form action="" method="post" class="form form-horizontal" id="form-admin-permission-add" enctype="multipart/form-data">
		
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-3">excel文件：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="file"  value="" name="excel">
			</div>
		</div>

		<div class="row cl">
			<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
				<button type="submit" class="btn btn-success radius" id="admin-permission-save" name="admin-permission-save"><i class="icon-ok"></i> 确定</button>
			</div>
		</div>
	</form>
</article>

<style type="text/css">

</style>
<!--_footer 作为公共模版分离出去-->

<script type="text/javascript" src="<?php echo config('admin_lib'); ?>/layer/2.4/layer.js"></script>
<script type="text/javascript" src="<?php echo config('admin_static'); ?>/h-ui/js/H-ui.min.js"></script> 
<script type="text/javascript" src="<?php echo config('admin_static'); ?>/h-ui.admin/js/H-ui.admin.js"></script> <!--/_footer 作为公共模版分离出去-->

<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript" src="<?php echo config('admin_lib'); ?>/jquery.validation/1.14.0/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo config('admin_lib'); ?>/jquery.validation/1.14.0/validate-methods.js"></script>
<script type="text/javascript" src="<?php echo config('admin_lib'); ?>/jquery.validation/1.14.0/messages_zh.js"></script>
<script type="text/javascript">
$(function(){
	$(".permission-list dt input:checkbox").click(function(){
		$(this).closest("dl").find("dd input:checkbox").prop("checked",$(this).prop("checked"));
	});
	$(".permission-list2 dd input:checkbox").click(function(){
		var l =$(this).parent().parent().find("input:checked").length;
		var l2=$(this).parents(".permission-list").find(".permission-list2 dd").find("input:checked").length;
		if($(this).prop("checked")){
			$(this).closest("dl").find("dt input:checkbox").prop("checked",true);
			$(this).parents(".permission-list").find("dt").first().find("input:checkbox").prop("checked",true);
		}
		else{
			if(l==0){
				$(this).closest("dl").find("dt input:checkbox").prop("checked",false);
			}
			if(l2==0){
				$(this).parents(".permission-list").find("dt").first().find("input:checkbox").prop("checked",false);
			}
		}
	});


	//form表单通过Ajax无刷新方式提交数据给服务器端
	$('#form-admin-permission-dd').submit(function(evt){
	    //阻止浏览器默认的form表单提交
		//evt.preventDefault();
		//收集form表单的信息,下述serialize()收集的信息传递给服务器端的时候与传统form表单提交的效果一致
		var shuju = $(this).serialize();  //字符串  name=val&name=val&name=val...
		//走Ajax
		$.ajax({
			url:'<?php echo url("admin/carposs/enterport"); ?>',
			data:shuju,
			dataType:'json',
			type:'post',
			success:function(msg){
				alert(msg.status)
                if(msg.status=='success'){
                    layer.alert('添加成功', {icon: 6},function(){
                        //主页面刷新
                        parent.window.location.href=parent.window.location.href;
                        //当前窗口关闭
                        layer_close();//本身的H-ui.admin.js封装的
                    });
                }else{
                    layer.alert('添加失败，【'+msg.errorinfo+'】', {icon: 5});
                }
			}
		});
	});
});
</script>
<!--/请在上方写此页面业务相关的脚本-->
</body>
</html>