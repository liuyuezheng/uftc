<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:61:"D:\park\public/../application/admin\view\carposs\wuyeadd.html";i:1543820402;}*/ ?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="stylesheet" type="text/css" href="/admin/js/css/layui.css" />
    <script type="text/javascript" src="/admin/js/layui.all.js"></script>
    <!--[if lt IE 9]>
    <script type="text/javascript" src="<?php echo config('admin_lib'); ?>/html5shiv.js"></script>
    <script type="text/javascript" src="<?php echo config('admin_lib'); ?>/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" type="text/css" href="<?php echo config('admin_static'); ?>/h-ui/css/H-ui.min.css" />
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
	<title>新建网站管理员 - 管理员管理 - H-ui.admin v3.1</title>
	<meta name="keywords" content="H-ui.admin v3.1,H-ui网站后台模版,后台模版下载,后台管理系统模版,HTML后台模版下载">
	<meta name="description" content="H-ui.admin v3.1，是一款由国人开发的轻量级扁平化网站后台模板，完全免费开源的网站后台管理系统模版，适合中小型CMS后台系统。">
</head>
<body>
<style> .layui-elem-field legend {
    margin-left: -9px;
    padding: 0 10px;
    font-size: 20px;
    font-weight: 300;
}
.layui-upload-img { width: 90px; height: 90px; margin: 0; }
.pic-more { width:100%; left; margin: 10px 0px 0px 0px;}
.pic-more li { width:90px; float: left; margin-right: 5px;}
.pic-more li .layui-input { display: initial; }
.pic-more li a { position: absolute; top: 0; display: block; }
.pic-more li a i { font-size: 24px; background-color: #008800; }
#slide-pc-priview .item_img img{ width: 90px; height: 90px;}
#slide-pc-priview li{position: relative;}
#slide-pc-priview li .operate{ color: #000; display: none;}
#slide-pc-priview li .toleft{ position: absolute;top: 40px; left: 1px; cursor:pointer;}
#slide-pc-priview li .toright{ position: absolute;top: 40px; right: 1px;cursor:pointer;}
#slide-pc-priview li .close{position: absolute;top: 5px; right: 5px;cursor:pointer;}
#slide-pc-priview li:hover .operate{ display: block;}</style>
<article class="page-container">
	<form action="" method="post" class="form form-horizontal" id="form-admin-role-add">
        
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-3"><span class="c-red"></span>车位号：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" class="input-text" value=""  name="number">
			</div>
		</div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red"></span>车位传感器编号：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value=""  name="sensor">
            </div>
        </div>
        <?php if($data['mg_name'] == 'admin'): ?>
       <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red"></span>所属车场：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <div data-toggle="distpicker">
                    <div class="form-group">
                        <select data-province="" class="input-text" id='province1' name='province' style='width:150px;'></select>
                   </div>
                   <div class="form-group">
                        <select data-city="" class="input-text" id='city1' name='city' style='width:150px;'></select>
                  </div>
                  <div class="form-group">
                        <select data-district="" class="input-text" id='district1' name='area' style='width:150px;'></select>
                  </div>
            </div>
        </div>
    </div>

    <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red"></span></label>
            <div class="formControls col-xs-8 col-sm-9">
               <select class="form-control" class="input-text" style='width:150px;height:31px;' id="manager" name="mg_id">
                            
                        </select>
            </div>
        </div>
    <?php endif; ?>

        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red"></span>车位类型：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="radio" value="2" name="types">出租</input>
                <input type="radio" value='4' style="margin-left:10px;" name='types'>临时</input>
                
            </div>
        </div>
        <div class="row cl del3">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red"></span>车位状态：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="radio" value="1" name="type">维护中</input>
                <input type="radio" value='2' style="margin-left:10px;" name='type'>租赁中</input>
                <input type="radio" value='3' style="margin-left:10px;" name='type'>停车中</input>
                <input type="radio" value='4' style="margin-left:10px;" name='type'>空置中</input>
            </div>
        </div>
        <div class="row cl del2">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red"></span>车位状态：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="radio" value="1" name="type">维护中</input>
                <!--<input type="radio" value='2' style="margin-left:10px;" name='type'  checked="checked">发布出租</input>-->
                <input type="radio" value='3' style="margin-left:10px;" name='type'>停车中</input>
                <input type="radio" value='4' style="margin-left:10px;" name='type'>空置中</input>
            </div>
        </div>
        <div class="row cl del1">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red"></span>车位状态：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <!--<input type="radio" value="1" name="type">维护中</input>-->
                <input type="radio" value='2' style="margin-left:10px;" name='type'  checked="checked">租赁中</input>
                <!--<input type="radio" value='3' style="margin-left:10px;" name='type'>停车中</input>-->
                <!--<input type="radio" value='4' style="margin-left:10px;" name='type'>空置中</input>-->
            </div>
        </div>
        <script>
            $('.del3').css('display','block');
            $('.del').css('display','none');
            $('.del2').css('display','none');
            $('.del1').css('display','none');
            $("input[name='types']").change(function(){
                var types=$("input[name='types']:checked").val();
                if(types==2){
                    $('.del').css('display','block');
                    $('.del1').css('display','block');
                    $('.del2').css('display','none');
                    $('.del3').css('display','none');
                }else{
                    $('.del').css('display','none');
                    $('.del2').css('display','block');
                    $('.del1').css('display','none');
                    $('.del3').css('display','none');
                }
            })
        </script>
        <div class="row cl del">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red"></span>出租时间：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" placeholder="" id="start_time" name="start_time" class="input-text" readonly="readonly" style="width:150px;"/>
                <input type="text" placeholder="" id="end_time" name="end_time"
     class="input-text"  readonly="readonly" style="width:150px;"/>
            </div>
        </div>

         <div class="row cl del">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red"></span>收费标准：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value=""  name="charge">
            </div>
        </div>
        <script src="<?php echo config('plugin'); ?>uploadify/jquery.uploadify.min.js" type="text/javascript"></script>
                <link rel="stylesheet" type="text/css" href="<?php echo config('plugin'); ?>uploadify/uploadify.css">
                <div class="row cl">
                    <label class="form-label col-xs-4 col-sm-3">车位图：</label>
                    <div class="layui-form-item" style="margin-left: 7rem">
                        <!--<label class="layui-form-label">缩略图</label>-->
                        <div class="layui-input-inline">
                            <div class="layui-upload">
                                <button type="button" class="layui-btn" id="upload">单图片上传</button>
                                <div class="layui-upload-list" style="padding-left:70px;">
                                    <img class="layui-upload-img" src="" id="showImg" width="200">
                                    <input type="hidden" name="pic" id="path">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--<div class="formControls col-xs-8 col-sm-9">-->
                        <!--<input type="file" id="logos" name="logos" />-->
                        <!---->
                        <!--<input type="hidden" id="logo" name="logo" value=""/>-->
                    <!--</div>-->
                    <!--<label class="form-label col-xs-4 col-sm-3"></label>-->
                    <!--<div class="formControls col-xs-8 col-sm-9">-->
                        <!---->
                        <!--<img src="" alt="" width="200" height="100" id="logo_show" />-->
                    <!--</div>-->
                </div>
        <script type="text/javascript">
            layui.use(['form', 'upload'], function(){
                var form = layui.form, $ = layui.jquery, upload = layui.upload;
                upload.render({
                    elem: '#upload'
                    ,url: '<?php echo url("admin/manager/upload"); ?>'
                    ,multiple: false
                    ,size: 1024
                    ,before: function(obj){
                        //预读本地文件示例，不支持ie8
                        obj.preview(function(index, file, result){
                            $('#showImg').attr('src', result); //图片链接（base64）
                        });
                    }
                    ,done: function(res){
                        console.log(res);
                        //如果上传失败
                        if(res.code == 0){
                            return layer.msg('上传失败');
                        }else{
                            $('#path').val(res.name);
                        }
                        //上传成功
//                        $('#path').val(res.data.src); // 将上传后的图片路径赋值给隐藏域
                    }
                });
            });
        </script>
          <!--      <script type="text/javascript">
                    <?php $timestamp = time();?>
                    $(function() {
                        $('#logos').uploadify({
                            'buttonText':'选择图片',
                            //不允许"一次性"上传多张图片
                            'multi':false,
                            'formData'     : {
                                'timestamp' : '<?php echo $timestamp;?>',
                                'token'     : '<?php echo md5('unique_salt' . $timestamp);?>'
                            },
                            'swf'      : '/plugin/uploadify/uploadify.swf',
                            'uploader' : '<?php echo url("admin/manager/logo_up"); ?>',

                            'onUploadSuccess' : function(file, data, response) {
                                //file.name: 被上传附件的名字
                                //data:     是服务器端返回的json字符串信息
                                //response: true

                                var obj = JSON.parse(data); //把json字符串变为object对象形式
                                //① 把上传好的图片显示出来
                                //substring(1),是把图片路径名的第一个"点"给去除

                                $('#logo_show').attr('src',obj.logopathname.substring(1));
                                //② 把附件路径名设置给id=goods_logo的隐藏域中
                                $('#logo').val(obj.logopathname);
                            }
                        });
                    });
                    /*
                        formData      上传表单维护的必要数据(timestamp和token)
                        uploadify.swf 类似播放器
                        uploadify.php 上传附件服务器端处理的php脚本
                        onUploadSuccess 附件成功上传的回调函数
                     */
                </script>-->


            </div>
		<div class="row cl">
			<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
				<button type="submit" class="btn btn-success radius" id="admin-role-save" name="admin-role-save"><i class="icon-ok"></i> 确定</button>
			</div>
		</div>
	</form>
</article>

<script type="text/javascript" src="<?php echo config('plugin'); ?>jedate/jquery.jedate.js"></script>
                <link type="text/css" rel="stylesheet" href="<?php echo config('plugin'); ?>jedate/skin/jedate.css">
                <script type="text/javascript">

                    //实现日期选择联动
                    var start ={
                        isinitVal:false,//在input框中显示初始时间信息
                        //festival:true,
                        ishmsVal:true,//是否允许手动修改时分秒
                        //minDate: $.nowDate({DD:0}),//设置当前事件为可选取的开始时间
                        maxDate: '2099-12-31',//设置结束时间
                        format:"YYYY-MM-DD",//时间显示的格式
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
                        format:"YYYY-MM-DD",
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
<script src="<?php echo config('plugin'); ?>jQueryDistpicker/js/distpicker.data.js"></script>
<script src="<?php echo config('plugin'); ?>jQueryDistpicker/js/distpicker.js"></script>
<script src="<?php echo config('plugin'); ?>jQueryDistpicker/js/main.js"></script>
<script type="text/javascript">
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
                //var xx = $('#xx').val();
                for(i=0;i<data.data.length;i++){
                   /* if(xx==data.data[i].mg_id){
                        html+='<option selected value="'+data.data[i].mg_id+'">'+data.data[i].name+'</option>';
                    }else{*/
                        html+='<option value="'+data.data[i].mg_id+'">'+data.data[i].name+'</option>';
                   /* }
                    */
                }
                console.log(html);
                $('#manager').html(html);
            }
        });
        });
</script>

<!-- <script>
    $('#start_time').change(function(){
        alert(1);
    })
</script> -->


<script type="text/javascript">

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
                //var xx = $('#xx').val();
                for(i=0;i<data.data.length;i++){
                   /* if(xx==data.data[i].mg_id){
                        html+='<option selected value="'+data.data[i].mg_id+'">'+data.data[i].name+'</option>';
                    }else{*/
                        html+='<option value="'+data.data[i].mg_id+'">'+data.data[i].name+'</option>';
                   /* }
                    */
                }
                console.log(html);
                $('#manager').html(html);
            }
        });
    })

       $('#city1').change(function(){
        var province=$('#province1').val();
        var city=$('#city1').val();
        var district=$('#district1').val();
        var manager=$('#manager').val();
        var html='';
        $.ajax({
            type: 'POST',
            url: '<?php echo url("admin/sensor/getsc"); ?>',
            dataType: 'json',
            data:{province:province,city:city,district:district,manager:manager},
            success: function(data){
               
                for(i=0;i<data.data.length;i++){
                        html+='<option value="'+data.data[i].mg_id+'">'+data.data[i].name+'</option>';
                }
                //console.log(html);
                $('#manager').html(html);
                var htm='';
                var mm=$('#manager').val();
                $.ajax({
                    type: 'POST',
                    url: '<?php echo url("admin/sensor/getcarpos"); ?>',
                    dataType: 'json',
                    data:{manager:mm},
                    success: function(data){
                       
                        for(i=0;i<data.data.length;i++){
                                htm+='<option value="'+data.data[i].id+'">'+data.data[i].number+'</option>';
                        }
                        //console.log(html);
                        $('#carpos').html(htm);
                    }
                });
            }
        });
    })

       $('#province1').change(function(){
        var province=$('#province1').val();
        var city=$('#city1').val();
        var district=$('#district1').val();
        var manager=$('#manager').val();
        var html='';
        $.ajax({
            type: 'POST',
            url: '<?php echo url("admin/sensor/gets"); ?>',
            dataType: 'json',
            data:{province:province,city:city,district:district,manager:manager},
            success: function(data){
               
                for(i=0;i<data.data.length;i++){
                        html+='<option value="'+data.data[i].mg_id+'">'+data.data[i].name+'</option>';
                }
                //console.log(html);
                $('#manager').html(html);
                var htm='';
                var mm=$('#manager').val();
                $.ajax({
                    type: 'POST',
                    url: '<?php echo url("admin/sensor/getcarpos"); ?>',
                    dataType: 'json',
                    data:{manager:mm},
                    success: function(data){
                       
                        for(i=0;i<data.data.length;i++){
                                htm+='<option value="'+data.data[i].id+'">'+data.data[i].number+'</option>';
                        }
                        //console.log(html);
                        $('#carpos').html(htm);
                    }
                });
            }
        });
    })


        //form表单通过Ajax无刷新方式提交数据给服务器端
        $('#form-admin-role-add').submit(function(evt){
            evt.preventDefault();
            var shuju = $(this).serialize();
//            console.log(shuju);
            $.ajax({
                url:'<?php echo \think\Request::instance()->url(); ?>',
                data:shuju,
                dataType:'json',
                type:'post',
                success:function(msg){
                    if(msg.status=='success'){
                        layer.alert('添加成功', {icon: 6},function(){
                            //主页面刷新
                            parent.window.location.href='<?php echo url("admin/carposs/lists"); ?>?province='+msg.province+'&city='+msg.city+'&area='+msg.area+'&manager='+msg.manager;
                            //当前窗口关闭
                            layer_close();
                        });
                    }else{
                        layer.alert('添加失败，【'+msg.errorinfo+'】', {icon: 5});
                    }
                }
            });
        });
</script>
<!--/请在上方写此页面业务相关的脚本-->
    </body>
</html>