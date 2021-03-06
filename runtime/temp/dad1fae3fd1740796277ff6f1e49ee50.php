<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:63:"D:\park\public/../application/admin\view\carposs\personadd.html";i:1543819434;}*/ ?>
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
    <link rel="stylesheet" type="text/css" href="/admin/js/css/layui.css" />
    <script type="text/javascript" src="/admin/js/layui.all.js"></script>
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
        <input type="hidden" name='mg_id' value="<?php echo $data['mg_id']; ?>" id="mgid">
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-3"><span class="c-red"></span>车位号：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" id="number" class="input-text" value=""  name="number">
			</div>
		</div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red"></span>车位传感器编号：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" id="sensor" class="input-text" value=""  name="sensor">
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
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red"></span>所属用户：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <!-- <select name="uid" class="input-text" style="width:150px">
                    <option value="0">-请选择-</option>
                    <?php if(is_array($info) || $info instanceof \think\Collection || $info instanceof \think\Paginator): $i = 0; $__LIST__ = $info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                        <option value="<?php echo $v['uid']; ?>"><?php echo $v['username']; ?></option>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </select> -->
                <div id="container">
            <div id="cityContainer" class="selectContainer">
                <label></label>
                <input type="text" placeholder="请输入用户名" list="cityList" class="selectInput" name="uid" id="cityName" value="" onfocus="fuzzySearch.call(this)" />
                    <div class="picture_click dropDowns" style=""></div>
                    <div id="cityList" class="selectList">
                        <?php if(is_array($info) || $info instanceof \think\Collection || $info instanceof \think\Paginator): $i = 0; $__LIST__ = $info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                        <div id="<?php echo $v['uid']; ?>">编号<?php echo $v['uid']; ?>-<?php echo $v['username']; ?></div>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </div>
            </div>
        </div>
            </div>
        </div>

        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red"></span>车位状态：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="radio" value="1" name="type">维护中</input>

                <input type="radio" value='3' style="margin-left:10px;" name='type'>停车中</input>
                <input type="radio" value='4' style="margin-left:10px;" name='type'>空置中</input>
            </div>
        </div>
        <script src="<?php echo config('plugin'); ?>uploadify/jquery.uploadify.min.js" type="text/javascript"></script>
                <link rel="stylesheet" type="text/css" href="<?php echo config('plugin'); ?>uploadify/uploadify.css">
                <div class="row cl">
                    <label class="form-label col-xs-4 col-sm-3">图片：</label>
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
      <!--          <script type="text/javascript">
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
//                                alert(1111111);
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

<style type="text/css">
    #container {
        width: 500px;
       /* text-align: center;
        margin: 0 auto;*/
        font-family: "微软雅黑";
        margin-top: 5px;
    }
    .selectContainer {
        position: relative;
    }
    .selectInput {
        width: 145px;
        height: 25px;
        border-style: none;
        border: 1px solid #999;
        border-radius: 3px;
        padding: 0 3px;
    }
    .picture_click {
        background: url(http://sandbox.runjs.cn/uploads/rs/382/gzpur0hb/select-default.png) no-repeat; 
        opacity: 1; 
        width: 15px; 
        height: 8px;
        position: absolute;
        top: 10px;
        right: 355px;
    }
    .picture_click:hover {
        background-image: url(http://sandbox.runjs.cn/uploads/rs/382/gzpur0hb/select-hover.png);
    }
    .selectList {
        width: 150px;
        height: 120px;
        overflow-y: scroll;
        text-align: left;
        margin: 0;
        border: 1px solid #999;
        display: none;
        position: relative;
    }
    .selectList div {
        cursor: pointer;
    }
</style>
<script src="<?php echo config('plugin'); ?>jQueryDistpicker/js/distpicker.data.js"></script>
<script src="<?php echo config('plugin'); ?>jQueryDistpicker/js/distpicker.js"></script>
<script src="<?php echo config('plugin'); ?>jQueryDistpicker/js/main.js"></script>
<!-- <script type="text/javascript">
    $('#number').blur(function(){
        var number = $("#number").val();
        alert(number);
    })
</script> -->
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
<script type="text/javascript">
    //初始化下拉框
    initSearchInput();

    function fuzzySearch(e) {
        var that = this;
        //获取列表的ID
        var listId = $(this).attr("list");
        //列表
        var list = $('#' + listId + ' div');
        //列表项数组  包列表项的id、内容、元素
        var listArr = [];
        //遍历列表，将列表信息存入listArr中
        $.each(list, function(index, item){
            var obj = {'eleId': item.getAttribute('id'), 'eleName': item.innerHTML, 'ele': item};
            listArr.push(obj);
        })
        
        //current用来记录当前元素的索引值
        var current = 0;
        //showList为列表中和所输入的字符串匹配的项
        var showList = [];
        //为文本框绑定键盘引起事件
        $(this).keyup(function(e){
            //如果输入空格自动删除
            this.value=this.value.replace(' ','');
            //列表框显示
            $('#' + listId).show();
            if(e.keyCode == 38) {
                //up
                console.log('up');
                current --;
                if(current <= 0) {
                    current = 0;
                }
                console.log(current);
            }else if(e.keyCode == 40) {
                //down
                console.log('down');
                current ++;
                if(current >= showList.length) {
                    current = showList.length -1;
                }
                console.log(current);

            }else if(e.keyCode == 13) {
                //enter
                console.log('enter');
                //如果按下回车，将此列表项的内容填充到文本框中
                $(that).val(showList[current].innerHTML);
                //下拉框隐藏
                $('#' + listId).hide();
            }else {
                //other
                console.log('other');
                //文本框中输入的字符串
                var searchVal = $(that).val();
                showList = [];
                //将和所输入的字符串匹配的项存入showList
                //将匹配项显示，不匹配项隐藏
                $.each(listArr, function(index, item){
                    if(item.eleName.indexOf(searchVal) != -1) {
                        item.ele.style.display = "block";
                        showList.push(item.ele);
                    }else {
                        item.ele.style.display = 'none';
                    }
                })
                console.log(showList);
                current = 0;
            }
            //设置当前项的背景色及位置
            $.each(showList, function(index, item){
                if(index == current) {
                    item.style.background = "#eee";
                    $('#' + listId).scrollTop(item.offsetTop);
                }else {
                    item.style.background = "";
                }
            })
            //设置下拉框的高度
            //212为列表框的最大高度
            if(212 > $('#' + listId + ' div').eq(0).height() * showList.length) {
                $('#' + listId).height($('#' + listId + ' div').eq(0).height() * showList.length);
            }else {
                $('#' + listId).height(212);
            }
        })
    }

    function initSearchInput() {
        //给下拉箭头绑定点击事件  点击下拉箭头显示/隐藏对应的列表
        //输入框的类名为selectInput
        //下拉箭头的类名为picture_click、dropDowns
        //下拉列表的类名为selectList
        for(var i = 0; i < $('.picture_click').length; i++) {
             $('.picture_click').eq(i).click(function(){
                $(this).parent().find('.selectList').toggle();
             })
        }
        //为列表中的每一项绑定鼠标经过事件
        $('.selectList div').mouseenter(function(){
            $(this).css("background", "#eee").siblings().css("background", "");
        });
        //为列表中的每一项绑定单击事件
        $('.selectList div').click(function(){
            //文本框为选中项的值
            $(this).parent().parent().find('.selectInput').val($(this).html());
            //下拉框隐藏
            $(this).parent().hide();
        });     

        //点击下拉框外部的时候使下拉框隐藏
        var dropDowns = document.getElementsByClassName('dropDowns');
        var selectList = document.getElementsByClassName('selectList');
        document.body.onclick = function(e){
            e = e || window.event;
            var target = e.target || e.srcElement;
            for(var i = 0; i < dropDowns.length; i++) {
                if(target != dropDowns[i] && target != selectList[i]){
                    selectList[i].style.display = 'none';
                }
            }
        }
    }   
</script>
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
                for(i=0;i<data.data.length;i++){   
                    html+='<option value="'+data.data[i].mg_id+'">'+data.data[i].name+'</option>';
                }
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