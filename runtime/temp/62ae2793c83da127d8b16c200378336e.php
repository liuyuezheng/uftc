<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:59:"D:\park\public/../application/admin\view\message\index.html";i:1545272479;}*/ ?>
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
<div class="page-container">
    <form name="form" method='post' action="">


        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3" style="margin-left: 10%;width: 9%">公告：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" id="content" style="width: 25%" value="" placeholder="请输入内容" name="content">
            </div>
        </div>

        <!--<div class="row cl remark" style="display:none;">-->
            <!--<label class="form-label col-xs-4 col-sm-3">备注：</label>-->
            <!--<div class="formControls col-xs-8 col-sm-9">-->
                <!--<input type="text" class="input-text" value="" placeholder="请输入内容" name="remark">-->
            <!--</div>-->
        <!--</div>-->

        <div class="row cl">
            <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
                <button type="submit" class="btn btn-success radius" style="margin-top: 3%;margin-left: -8%" id="admin-role-save" name="admin-role-save"><i class="icon-ok"></i> 即刻发布</button>
            </div>
        </div>

    </form>
    <!-- <span cass="l" style="margin-left:30px;"> <a href="javascript:;" onclick="member_add('添加个人车位','<?php echo url('admin/carposs/personadd'); ?>','','510')" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe600;</i> 添加个人车位</a></span>
    <span cass="l" style="margin-left:10px;"> <a href="javascript:;" onclick="member_add('添加物业车位','<?php echo url('admin/carposs/wuyeadd'); ?>','','510')" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe600;</i> 添加物业车位</a></span> -->

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
<script type="text/javascript">
    $('#admin-role-save').click(function () {
           var content=$('#content').val();
//        alert(content)
//        layer.confirm('确认要发布吗？',function(index){
            $.ajax({
                type: 'POST',
                url: '<?php echo url("admin/message/addMessage"); ?>',
                dataType: 'json',
                data:{content:content},
                success: function(data){
//                 alert(data);
                    if(data===true){
                        layer.msg('发布成功!',{icon: 1,time:1000});
                    }else{
                        layer.msg('发布失败!',{icon: 5,time:1000});
                    }
                }
            });
//        });

    });
 /*   $(function(){
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
                        html+='<option selected  value="'+data.data[i].mg_id+'">'+data.data[i].name+'</option>';
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
    })*/

    /*用户-添加*/
    function member_add(title,url,w,h){
        layer_show(title,url,w,h);
    }
    /*用户-查看*/
    function member_show(title,url,id,w,h){
        layer_show(title,url,w,h);
    }
    /*用户-停用*/
   /* function member_stop(obj,id){
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

    /!*用户-启用*!/
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
    }*/
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
                url: '<?php echo url("admin/carposs/shanchu"); ?>',
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