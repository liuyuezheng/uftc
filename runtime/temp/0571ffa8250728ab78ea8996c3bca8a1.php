<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:58:"D:\park\public/../application/admin\view\tixian\index.html";i:1535680428;}*/ ?>
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
    <!-- <link href="http://www.jq22.com/jquery/bootstrap-3.3.4.css" rel="stylesheet"> -->
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
	<?php if($shuju['data']['mg_name'] == 'admin'): ?>
	<form name="form" method='post' action="<?php echo url('admin/tixian/index'); ?>">
		
		<div style="margin-left:30px;">
			<?php if($shuju['data']['type'] == '1'): ?>
			<div data-toggle="distpicker" style="float:left;">	
		
					<div class="form-group">
				  		<select data-province="<?php echo $shuju['data']['province']; ?>" class="input-text" id='province1' name='province' style='width:150px;'></select>
				   </div>

				   <div class="form-group">
				  		<select data-city="<?php echo $shuju['data']['city']; ?>" class="input-text" id='city1' name='city' style='width:150px;'></select>
				  </div>

				  <div class="form-group">
				  		<select data-district="<?php echo $shuju['data']['area']; ?>" class="input-text" id='district1' name='area' style='width:150px;'></select>
				  </div>
		
			
		      </div>
		      <?php endif; ?>
		      <div class="form-group" style='margin-left:5px;'>
			    提现来源: <select name='type' style='width:150px;' class="input-text">
					<option value='1' <?php if($shuju['data']['type'] == '1'): ?>selected<?php endif; ?>>停车场</option>
					<option value='2' <?php if($shuju['data']['type'] == '2'): ?>selected<?php endif; ?>>用户</option>
				 </select>
			
				  
		</div>
		<div class="form-group" style='margin-left:5px;'>
				 时间: <input type="text" placeholder="" id="start_time" name="start_time" class="input-text" readonly="readonly" style="width:150px;" value="<?php echo $shuju['data']['start_time']==''?'':date('Y-m-d H:i:s',$shuju['data']['start_time']); ?>"/>
		<input type="text" placeholder="" id="end_time" name="end_time"
 class="input-text"  readonly="readonly" style="width:150px;" value="<?php echo $shuju['data']['end_time']==''?'':date('Y-m-d H:i:s',$shuju['data']['end_time']); ?>"/>
			<input type="submit" class="btn btn-success radius" value="搜索">
				  
		</div>		
		</div>
		
		
	</form>
	<?php else: ?>
	 <span class="l" style="margin-left:20px;margin-bottom: 20px;"> <a href="javascript:;" onclick="member_add('申请提现','<?php echo url('admin/tixian/add',['mg_id'=>$shuju['data']['mg_id']]); ?>','','510')" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe600;</i> 申请提现</a></span>
	<?php endif; ?>
	<div class="mt-20" style="margin-top:25px;">
	<table class="table table-border table-bordered table-hover table-bg table-sort">
		<thead>
			<tr class="text-c">
				<?php if($shuju['data']['type'] == '1'): ?>
				<th width="100">停车场名称</th>
				<?php else: ?>
				<th width="100">用户名称</th>
				<?php endif; ?>
				<th width="100">提现金额</th>
				<th width="100">账户余额</th>
				<th width="100">支付宝账号</th>
				<th width="100">真实姓名</th>
				<!-- <th width="100">提现状态</th> -->
				<th width="100">提现时间</th>
				<?php if($shuju['data']['mg_name'] == 'admin'): ?>
				<th width="100">提现状态</th>
				<?php endif; ?>
			</tr>
		</thead>
		<tbody>
			<?php if(empty($shuju['page']) || (($shuju['page'] instanceof \think\Collection || $shuju['page'] instanceof \think\Paginator ) && $shuju['page']->isEmpty())): ?>
				<tr class="text-c">
					<td colspan="100" >暂无数据</td>
			    </tr>
			<?php else: if(is_array($shuju['page']) || $shuju['page'] instanceof \think\Collection || $shuju['page'] instanceof \think\Paginator): $i = 0; $__LIST__ = $shuju['page'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?>
				<tr class="text-c">
				<?php if($shuju['data']['type'] == '1'): ?>
				<td><?php echo $val['name']; ?></td>
				<?php else: ?>
				<td><?php echo $val['username']; ?></td>
				<?php endif; ?>
				<td><?php echo $val['account']; ?></td>
				<td><?php echo $val['moneybag']; ?></td>
				<td><?php echo $val['alipay']; ?></td>
				<td><?php echo $val['truename']; ?></td>
				<!-- <td>
					<?php if($val['status'] == '1'): ?>审核通过<?php endif; if($val['status'] == '2'): ?>审核不通过<?php endif; if($val['status'] == '3'): ?>审核中<?php endif; ?>
				</td> -->
				<td><?php echo date('Y-m-d H:i:s',$val['duration']); ?></td>
				<?php if($shuju['data']['mg_name'] == 'admin'): ?>
				<!-- <td class="td-status"><a title="未付" href="javascript:;" onclick="member_edit('未付','<?php echo url('admin/tixian/verify',['id'=>$val['id']]); ?>','4','','510')" style="text-decoration:none"><span class="label label-success radius">未付</span></a></td> -->
				<td id="tixian_<?php echo $val['id']; ?>_<?php echo $val['ispay']; ?>" onclick="is_pay(this)">
					<?php if($val['ispay'] == '0'): ?>
					<img src="<?php echo config('admin_temp'); ?>/no.gif">
					<?php else: ?>
					<img src="<?php echo config('admin_temp'); ?>/yes.gif">
					<?php endif; ?>
				</td>
				<?php endif; ?>
			</tr>
			<?php endforeach; endif; else: echo "" ;endif; endif; if(!(empty($shuju['pagelist']) || (($shuju['pagelist'] instanceof \think\Collection || $shuju['pagelist'] instanceof \think\Paginator ) && $shuju['pagelist']->isEmpty()))): ?>
			<tr><td colspan="100"><?php echo $shuju['pagelist']; ?></td></tr>
			<?php endif; ?>
		</tbody>
	</table>
	</div>
</div>
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
<script type="text/javascript" src="<?php echo config('plugin'); ?>jedate/jquery.jedate.js"></script>
				<link type="text/css" rel="stylesheet" href="<?php echo config('plugin'); ?>jedate/skin/jedate.css">
				<script type="text/javascript">

                    //实现日期选择联动
                    var start ={
                        isinitVal:false,//在input框中显示初始时间信息
                        //festival:true,
                        ishmsVal:true,//是否允许手动修改时分秒
                        //minDate: $.nowDate({DD:0}),//设置当前事件为可选取的开始时间
                        maxDate: '2099-12-31 23:59:59',//设置结束时间
                        format:"YYYY-MM-DD hh:mm:ss",//时间显示的格式
                        zIndex:3000,//设置时间弹框在页面最上面显示
                        choosefun: function(elem,datas){
                            end.minDate = datas; //开始日选好后，重置结束日的最小日期
                            endDates();
                        },
                    }

                    function endDates() {
                        end.trigger = false;
                        $("#inpend").jeDate(end);
                    }

                    var end ={
                        isinitVal:false,
                        //festival:true,
                        ishmsVal:true,//是否允许手动修改时分秒
                        minDate: $.nowDate({DD:0}),
                        maxDate: '2099-12-31 23:59:59',
                        format:"YYYY-MM-DD hh:mm:ss",
                        zIndex:3000,
                        choosefun: function(elem,datas){
                            start.maxDate = datas; //将结束日的初始值设定为开始日的最大日期
                        }
                    }

                    //设置开始时间
                    $("#start_time").jeDate(start);

                    //设置结束时间
                    $("#end_time").jeDate(end);
				</script>

<script type="text/javascript">

//是否付款
function is_pay(obj){

    var id_ispromotion=$(obj).attr('id').split('_');
    var goods_id=id_ispromotion[1];

    var is_promotion=(id_ispromotion[2]==0)?1:0;
    //var is_promotion=(id_ispromotion[2]==0)?1:1;
    //var is_promotion=id_ispromotion[2]=1;
    //alert(is_promotion);
    $.ajax({
		url:"<?php echo url('admin/tixian/ispay'); ?>",
		data:{goods_id:goods_id,is_promotion:is_promotion},
		dataType:"json",
		type:"post",
		success:function(msg){
		    if(msg.status=="success"){
		        if(is_promotion==1){
		            $(obj).html('<img src="<?php echo config('admin_temp'); ?>/yes.gif" title="">');
		            layer.msg('付款成功',{icon:6,time:2000});

		            //$(obj).unbind('click');
				}
				//修改td的属性值
				$(obj).attr('id','tixian_'+goods_id+'_'+1);
			}
		}
	});

}

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
			url: '<?php echo url("admin/manager/shanchu"); ?>',
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