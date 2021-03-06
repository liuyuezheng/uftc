<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:60:"D:\park\public/../application/admin\view\carposs\verify.html";i:1543466580;}*/ ?>
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
<link rel="stylesheet" type="text/css" href="<?php echo config('admin_lib'); ?>/lightbox2/2.8.1/css/lightbox.css" />
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
    <form action="" method="post" class="form form-horizontal" id="form-admin-permission-add" 
    enctype="multipart/form-data">
        <input type="hidden" class="input-text" value="<?php echo $id; ?>"  name="id">
        <input type="hidden" class="input-text" value="<?php echo $uid; ?>"  name="uid">
        <input type="hidden" class="input-text" value="<?php echo $mg_id; ?>"  name="mg_id">
        <input type="hidden" class="input-text" value="<?php echo $number; ?>"  name="number">
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">图片：</label>
 
            <div class="formControls col-xs-8 col-sm-9">
                   
                <div class="picbox">
                    <?php if(is_array($logo) || $logo instanceof \think\Collection || $logo instanceof \think\Paginator): $i = 0; $__LIST__ = $logo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                    <a href="<?php echo $v; ?>" data-lightbox="gallery">
                    <img src="<?php echo $v; ?>" width="90"  height="90"></a>
                  <?php endforeach; endif; else: echo "" ;endif; ?>
                </div>
              
            </div>

        </div>

            <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">个人车位用户审核：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <select name="style" id="flag" class="input-text" style="width:150px;">
                    <option value="">-请选择-</option>
                    <option value="1">审核通过</option>
                    <option value="2">审核不通过</option>
                </select>
            </div>
        </div>

        <div class="row cl remark" style="display:none;">
            <label class="form-label col-xs-4 col-sm-3">备注：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="" placeholder="请输入内容" name="remark">
            </div>
        </div>

        <div class="row cl">
            <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
                <button type="submit" class="btn btn-success radius" id="admin-role-save" name="admin-role-save"><i class="icon-ok"></i> 确定</button>
            </div>
        </div>
    </form>
</article>

<!--_footer 作为公共模版分离出去-->

<script type="text/javascript" src="<?php echo config('admin_lib'); ?>/layer/2.4/layer.js"></script>
<script type="text/javascript" src="<?php echo config('admin_static'); ?>/h-ui/js/H-ui.min.js"></script> 
<script type="text/javascript" src="<?php echo config('admin_static'); ?>/h-ui.admin/js/H-ui.admin.js"></script> <!--/_footer 作为公共模版分离出去-->

<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript" src="<?php echo config('admin_lib'); ?>/jquery.validation/1.14.0/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo config('admin_lib'); ?>/jquery.validation/1.14.0/validate-methods.js"></script>
<script type="text/javascript" src="<?php echo config('admin_lib'); ?>/lightbox2/2.8.1/js/lightbox.min.js"></script>
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
    $('#form-admin-permission-add').submit(function(evt){
        //阻止浏览器默认的form表单提交
        evt.preventDefault();
        //收集form表单的信息,下述serialize()收集的信息传递给服务器端的时候与传统form表单提交的效果一致
        var shuju = $(this).serialize();  //字符串  name=val&name=val&name=val...
        //走Ajax
        $.ajax({
            url:'<?php echo \think\Request::instance()->url(); ?>',
            data:shuju,
            dataType:'json',
            type:'post',
            success:function(msg){
                if(msg.status=='success'){
                    layer.alert('设置成功', {icon: 6},function(){
                        //主页面刷新
                        parent.window.location.href=parent.window.location.href;
                        //当前窗口关闭
                        layer_close();//本身的H-ui.admin.js封装的
                    });
                }else{
                    layer.alert('设置失败，【'+msg.errorinfo+'】', {icon: 5});
                }
            }
        });
    });
});

$("#flag").change(function(){
    var val = $("#flag").val();
    if(val==2){
        $(".remark").css('display','block');
    }else{
        $(".remark").css('display','none');
    }
})

 
</script>
<!--/请在上方写此页面业务相关的脚本-->
</body>
</html>