<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:63:"D:\park\public/../application/admin\view\user\personsindex.html";i:1543231701;}*/ ?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <!--[if lt IE 9]>
    <script type="text/javascript" src="<?php echo config('admin_lib'); ?>/html5shiv.js"></script>
    <script type="text/javascript" src="<?php echo config('admin_lib'); ?>/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" type="text/css" href="<?php echo config('admin_static'); ?>/h-ui/css/H-ui.min.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo config('admin_lib'); ?>/lightbox2/2.8.1/css/lightbox.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo config('admin_static'); ?>/h-ui.admin/css/H-ui.admin.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo config('admin_lib'); ?>/Hui-iconfont/1.0.8/iconfont.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo config('admin_static'); ?>/h-ui.admin/skin/default/skin.css" id="skin" />
    <link rel="stylesheet" type="text/css" href="<?php echo config('admin_static'); ?>/h-ui.admin/css/style.css" />
    <!--[if IE 6]>
    <script type="text/javascript" src="<?php echo config('admin_lib'); ?>/DD_belatedPNG_0.0.8a-min.js" ></script>
    <script>DD_belatedPNG.fix('*');</script>
    <![endif]-->

    <!--_footer 作为公共模版分离出去-->
    <script type="text/javascript" src="<?php echo config('admin_lib'); ?>/jquery/1.9.1/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo config('admin_lib'); ?>/layer/2.4/layer.js"></script>
    <script type="text/javascript" src="<?php echo config('admin_static'); ?>/h-ui/js/H-ui.min.js"></script>
    <script type="text/javascript" src="<?php echo config('admin_static'); ?>/h-ui.admin/js/H-ui.admin.js"></script>
    <!--/_footer 作为公共模版分离出去-->



    <!--请在下方写此页面业务相关的脚本-->
    <script type="text/javascript" src="<?php echo config('admin_lib'); ?>/My97DatePicker/4.8/WdatePicker.js"></script>
    <script type="text/javascript" src="<?php echo config('admin_lib'); ?>/datatables/1.10.0/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="<?php echo config('admin_lib'); ?>/laypage/1.2/laypage.js"></script>
<title>管理员管理</title>
</head>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 管理员中心 <span class="c-gray en">&gt;</span> 管理员管理 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>

<div class="page-container">
	<form name="form" method='post' action="<?php echo url('admin/user/personsindex'); ?>">
		<?php if($shuju['data']['mg_name'] == 'admin'): ?>
		<div style="margin-left:30px;margin-bottom:18px;">
			<div data-toggle="distpicker">
				<div data-toggle="distpicker">
					<div class="form-group">
				  		<select data-province="<?php echo $shuju['data']['province']; ?>" class="input-text" id='province1' name='province' style='width:150px;'></select>
				   </div>
				   <div class="form-group">
				  		<select data-city="<?php echo $shuju['data']['city']; ?>" class="input-text" id='city1' name='city' style='width:150px;'></select>
				  </div>
				  <div class="form-group">
				  		<select data-district="<?php echo $shuju['data']['area']; ?>" class="input-text" id='district1' name='area' style='width:150px;'></select>
				  </div>
				  <div class="form-group">
				  		<select class="form-control" class="input-text" style='width:150px;height:31px;' id="manager" name="manager">
				  			
				  		</select>
				  </div>
				  <div class="form-group">
			用户名称: <input type="text" class="input-text" style="width:150px;" placeholder="请输入内容" value="<?php echo $shuju['data']['username']; ?>" name="username" >
			联系方式: <input type="text" class="input-text" style="width:150px;margin-right:10px;" placeholder="请输入内容" value="<?php echo $shuju['data']['phone']; ?>" name="phone" >
			<input type="submit" class="btn btn-success radius" value="搜索">
		</div>
				</div>
		      </div>
		</div>
		<?php else: ?>
		<div class="form-group" style="margin-left:20px;">
			用户名称: <input type="text" class="input-text" style="width:150px;" placeholder="请输入内容" value="<?php echo $shuju['data']['username']; ?>" name="username" >
			联系方式: <input type="text" class="input-text" style="width:150px;margin-right:10px;" placeholder="请输入内容" value="<?php echo $shuju['data']['phone']; ?>" name="phone" >
			<input type="submit" class="btn btn-success radius" value="搜索">
		</div>
		<?php endif; ?>
	</form>
	 <!-- <span cass="l" style="margin-lelft:10px;"> <a href="javascript:;" onclick="member_add('添加个人车位用户','<?php echo url('admin/user/personadd'); ?>','','510')" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe600;</i> 添加个人车位用户</a></span> -->
	<div class="mt-20" style="margin-top:20px;">
	<table class="table table-border table-bordered table-hover table-bg table-sort">
		<thead>
			<tr class="text-c">
				<th width="100">用户名</th>
				<th width="100">车牌号</th>
				<th width="100">联系方式</th>
				<!--<th width="100">图片</th>-->
				<?php if($shuju['data']['mg_name'] == 'admin'): ?>
				<th width="100">停车场</th>
				<?php endif; ?>
				<th width="100">车位号</th>
				<!--<th width="100">操作</th>-->
			</tr>
		</thead>
		<tbody>
			<?php if(empty($shuju['page']) || (($shuju['page'] instanceof \think\Collection || $shuju['page'] instanceof \think\Paginator ) && $shuju['page']->isEmpty())): ?>
				<tr class="text-c">
					<td colspan="100" >暂无数据</td>
			    </tr>
			<?php else: if(is_array($shuju['page']) || $shuju['page'] instanceof \think\Collection || $shuju['page'] instanceof \think\Paginator): $i = 0; $__LIST__ = $shuju['page'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?>
				<tr class="text-c">
				<td><?php echo $val['username']; ?></td>
				<td><?php echo $val['plate']; ?></td>
				<td><?php echo $val['phone']; ?></td>
				<!--<td><div class="picbox"><a href="<?php echo substr($val['logo'],1); ?>" data-lightbox="gallery"><img src="<?php echo substr($val['logo'],1); ?>" width="50" height="40"></a></div></td>-->
					<?php if($shuju['data']['mg_name'] == 'admin'): ?>
					<!--<th width="100">停车场</th>-->
					<td><?php echo $val['mg_name']; ?></td>
					<?php endif; ?>
					<!--<th width="100">车位号</th>-->
					<td><?php echo $val['number']; ?></td>
				<!--<td class="td-manage">-->
					<!--<a title="编辑" href="javascript:;" onclick="member_edit('编辑','<?php echo url('admin/user/personsedit',['uid'=>$val['uid']]); ?>','4','','510')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>-->
				<!--</td>-->
			</tr>
			<?php endforeach; endif; else: echo "" ;endif; endif; if(!(empty($shuju['pagelist']) || (($shuju['pagelist'] instanceof \think\Collection || $shuju['pagelist'] instanceof \think\Paginator ) && $shuju['pagelist']->isEmpty()))): ?>
			<tr><td colspan="100"><?php echo $shuju['pagelist']; ?></td></tr>
			<?php endif; ?>
		</tbody>
	</table>
	</div>
</div>
<input type="hidden" value="<?php echo $shuju['data']['manager']; ?>" id="xx"> 
<style type="text/css">
	/**分页页码列表样式**/
	.pagination li{list-style:none;float:left;margin-left:10px;
		padding:0 10px;
		background-color:#5a98de;
		border:1px solid #ccc;
		height:26px;
		line-height:26px;
		cursor:pointer;
	}
	.pagination li a{color:white;}
	.pagination li.active{background-color:white;color:gray;}
	.pagination li.disabled{background-color:white;color:gray;}
</style>
<!--_footer 作为公共模版分离出去-->
<script type="text/javascript" src="<?php echo config('admin_lib'); ?>/jquery/1.9.1/jquery.min.js"></script> 
<script type="text/javascript" src="<?php echo config('admin_lib'); ?>/layer/2.4/layer.js"></script>
<script type="text/javascript" src="<?php echo config('admin_static'); ?>/h-ui/js/H-ui.min.js"></script> 
<script type="text/javascript" src="<?php echo config('admin_static'); ?>/h-ui.admin/js/H-ui.admin.js"></script> <!--/_footer 作为公共模版分离出去-->

<script src="<?php echo config('plugin'); ?>jQueryDistpicker/js/distpicker.data.js"></script>
<script src="<?php echo config('plugin'); ?>jQueryDistpicker/js/distpicker.js"></script>
<script src="<?php echo config('plugin'); ?>jQueryDistpicker/js/main.js"></script>
<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript" src="<?php echo config('admin_lib'); ?>/My97DatePicker/4.8/WdatePicker.js"></script> 
<script type="text/javascript" src="<?php echo config('admin_lib'); ?>/datatables/1.10.0/jquery.dataTables.min.js"></script> 
<script type="text/javascript" src="<?php echo config('admin_lib'); ?>/lightbox2/2.8.1/js/lightbox.min.js"></script> 
<script type="text/javascript" src="<?php echo config('admin_lib'); ?>/laypage/1.2/laypage.js"></script>
<script type="text/javascript">
$(function(){
	$(function(){
		var province=$('#province1').val();
		var city=$('#city1').val();
		var district=$('#district1').val();
		var manager=$('#manager').val();
		var html='';
		$.ajax({
			type: 'POST',
			url: '<?php echo url("admin/user/getscc"); ?>',
			dataType: 'json',
			data:{province:province,city:city,district:district,manager:manager},
			success: function(data){
				html+='<option  value="">'+'--请选择--'+'</option>';
				var xx = $('#xx').val();
				for(i=0;i<data.data.length;i++){
					if(xx==data.data[i].mg_id){
						html+='<option selected value="'+data.data[i].mg_id+'">'+data.data[i].name+'</option>';
					}else{
						html+='<option  value="'+data.data[i].mg_id+'">'+data.data[i].name+'</option>';
					}
				}
				$('#manager').html(html);
			}
		});
	})

	$('#district1').change(function(){
		var province=$('#province1').val();
		var city=$('#city1').val();
		var district=$('#district1').val();
		var manager=$('#manager').val();
		var html='';
		$.ajax({
			type: 'POST',
			url: '<?php echo url("admin/user/getscc"); ?>',
			dataType: 'json',
			data:{province:province,city:city,district:district,manager:manager},
			success: function(data){
				html+='<option  value="">'+'--请选择--'+'</option>';
				var xx = $('#xx').val();
				for(i=0;i<data.data.length;i++){
					if(xx==data.data[i].mg_id){
						html+='<option selected value="'+data.data[i].mg_id+'">'+data.data[i].name+'</option>';
					}else{
						html+='<option value="'+data.data[i].mg_id+'">'+data.data[i].name+'</option>';
					}
					
				}
				$('#manager').html(html);
			}
		});
	})
	
});
/*用户-添加*/
function member_add(title,url,w,h){
	layer_show(title,url,w,h);
}
/*用户-查看*/
function member_show(title,url,id,w,h){
	layer_show(title,url,w,h);
}
/*用户-停用*/
function member_stop(obj,id){
	layer.confirm('确认要停用吗？',function(index){
		$.ajax({
			type: 'POST',
			url: '',
			dataType: 'json',
			success: function(data){
				$(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="member_start(this,id)" href="javascript:;" title="启用"><i class="Hui-iconfont">&#xe6e1;</i></a>');
				$(obj).parents("tr").find(".td-status").html('<span class="label label-defaunt radius">已停用</span>');
				$(obj).remove();
				layer.msg('已停用!',{icon: 5,time:1000});
			},
			error:function(data) {
				console.log(data.msg);
			},
		});		
	});
}

/*用户-启用*/
function member_start(obj,id){
	layer.confirm('确认要启用吗？',function(index){
		$.ajax({
			type: 'POST',
			url: '',
			dataType: 'json',
			success: function(data){
				$(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="member_stop(this,id)" href="javascript:;" title="停用"><i class="Hui-iconfont">&#xe631;</i></a>');
				$(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已启用</span>');
				$(obj).remove();
				layer.msg('已启用!',{icon: 6,time:1000});
			},
			error:function(data) {
				console.log(data.msg);
			},
		});
	});
}
/*用户-编辑*/
function member_edit(title,url,id,w,h){
	layer_show(title,url,w,h);
}
/*密码-修改*/
function change_password(title,url,id,w,h){
	layer_show(title,url,w,h);	
}
/*用户-删除*/
function member_del(obj,id){
	layer.confirm('确认要删除吗？',function(index){
		$.ajax({
			type: 'POST',
			url: '<?php echo url("admin/user/shanchu"); ?>',
			dataType: 'json',
			data:{id:id},
			success: function(data){
				$(obj).parents("tr").remove();
				layer.msg('已删除!',{icon:1,time:1000});
			},
			error:function(data) {
				console.log(data.msg);
			},
		});		
	});
}
</script> 
</body>
</html>