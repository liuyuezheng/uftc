<?php

namespace app\admin\controller;

use app\admin\model\GoodsPics;
use think\Controller;
use app\admin\model\Manager;
use app\admin\model\Role;
use think\Request;
use think\Session;
use think\Db;

class ManagerController extends Controller
{
    public function login(Request $request){
        if($request->isPost()){
            $code=$request->param('verify_code');
            if(captcha_check($code)){
                //验证码正确
                $name=$request->param('mg_name');
                $pwd=md5($request->param('password'));
                $exists=Manager::where(['mg_name'=>$name,'password'=>$pwd])->find();
                if($exists){
                    Session::set('mg_name',$exists->mg_name);
                    Session::set('mg_id',$exists->mg_id);
                    $this->redirect('admin/index/index');
                }else{
                    $this->assign('errorinfo','用户名或密码不正确');
                }
            }else{
                $this->assign('errorinfo','验证码不正确');
            }
        }
        return $this->fetch();
    }
    //导入
    public function enterport(Request $request){
        if($request->isPost()){
            vendor('phpoffice.phpexcel');
            Vendor("phpoffice.PHPExcel.IOFactory");
            $objPHPExcel = new \PHPExcel();
            $file = $request->file('excel');
            $info = $file->move(ROOT_PATH . 'public' . DS . 'excel');
//            return json_encode($info);
            if($info){
                $exclePath = $info->getSaveName();  //获取文件名
                $extension = pathinfo($exclePath, PATHINFO_EXTENSION);
                $file_name = ROOT_PATH . 'public' . DS . 'excel' . DS . $exclePath;   //上传文件的地址
                if($extension == 'xlsx'){
                    $objReader =\PHPExcel_IOFactory::createReader('excel2007');
                }else{
                    $objReader =\PHPExcel_IOFactory::createReader('Excel5');
                }
                $obj_PHPExcel =$objReader->load($file_name, $encode = 'utf-8');  //加载文件内容,编码utf-8
                echo "<pre>";

                $excel_array=$obj_PHPExcel->getsheet(0)->toArray();   //转换为数组格式
                array_shift($excel_array);  //删除第一个数组(标题);
                $data = [];
//                $i=1;
//                print_r($excel_array);
                for ($i=1;$i<count($excel_array);$i++){
                    $k=0;
                    $data['mg_name']=$excel_array[$i][0];
                    $data['password']=md5($excel_array[$i][1]);
                    $data['name']=$excel_array[$i][2];
                    $data['province']=$excel_array[$i][3];
                    $data['city']=$excel_array[$i][4];
                    $data['area']=$excel_array[$i][5];
                    $data['role_id']=2;
                    $data['address']=$excel_array[$i][6];
                    $data['latitude']=$excel_array[$i][8];
                    $data['longitude']=$excel_array[$i][7];
                    $data['total']=$excel_array[$i][9];
                    $data['leftpos']=$excel_array[$i][10];
                    $data['types']=$excel_array[$i][12];
                    $data['charge']=$excel_array[$i][11];
                    $list=Db::table('pk_manager')->where('mg_name',$data['mg_name'])->find();
                    if(empty($list)){
                        $success[]=Db::table('pk_manager')->insert($data);
                    }

                }
//                dump($success);
//                $success=Db::table('pk_manager')->insertAll($data); //批量插入数据
                if($success){
                    $nums=count($excel_array)-1;
//                    return true;
                    $num=count($success);
                    $low=$nums-$num;
                    $url=url('admin/manager/index');
                    if ($nums==$num){
                        echo <<<AAA
          <script type='text/javascript'>
            alert('导入成功');
            window.parent.location.href="$url";
          </script>
AAA;
                    }else{
                        echo <<<AAA
          <script type='text/javascript'>
            alert("$num 条导入成功，$low 条导入失败");
            window.parent.location.href="$url";
          </script>
AAA;
                    }
                }else{
//                    return false;
                    $url=url('admin/manager/index');
                    echo <<<AAA
          <script type='text/javascript'>
            alert('导入失败，请检查数据');
            window.parent.location.href="$url";
          </script>
AAA;
                }
            }
        }else{
            return $this->fetch();
        }
    }

    //管理员退出系统

    public function logout()
    {
        Session::clear();//清除session
        $this -> redirect('admin/manager/login');//页面跳转
    }

    public function index(Request $request){
        $data['name']=request()->param('name');
        $data['province']=$request->param('province');
        //$data['province']==''?$data['province']='浙江省':$data['province'];
        $data['city']=$request->param('city');
        //$data['city']==''?$data['city']='杭州市':$data['city'];
        $data['area']=$request->param('area');
        //$data['area']==''?$data['area']='滨江区':$data['area'];

        $data['manager']=$request->param('manager');
        $data['mg_name']=Session::get('mg_name');
        $data['mg_id']=Session::get('mg_id');
        $manager_model=new \app\admin\model\Manager();
        $shuju=$manager_model->getlist($data);
        $this->assign('shuju',$shuju);
        return $this -> fetch();
    }

    public function add(){
        if(request()->ispost()){
            $data=input();
            if(!empty($data['pic'])){
                $data['logo']='.'.$data['pic'];
            }
            if(!empty($data['pics'])){
                foreach ($data['pics'] as $k){
                    $data['pics_mid'][]='.'.$k;
                }
            }
//            if(strpos($data['logo'],'goodstmp')!==false){
//                //删除(有的情况)旧图片
//                if(!empty($data['logo'])){
//
//                $dir="./uploads/goods/".date('Ymd');
//                if(!file_exists($dir)){
//                    mkdir($dir,0777,true);
//                }
//            }
//                //移动上传图片到真实位置，并存储真实位置到数据库
//                $falsepath=$data['logo'];
//                $truepath = str_replace('goodstmp','goods',$data['logo']);
//                copy($data['logo'],$truepath);
//                $data['logo'] = $truepath; //修改为真实的图片路径名
//                unlink(ltrim($falsepath,'./'));
//            }
            $data['create_time']=time();

            $data['password']=md5($data['password']);
            $data['role_id']=2;

            $manager=new \app\admin\model\Manager();
            //$manager_model=new \app\admin\model\Manager();
            $result = $manager->allowField(true)->save($data); //返回添加个数

            $this->pics_deal($manager->mg_id,$data['pics_mid']);
            if($result){
                return ['status'=>'success','province'=>$data['province'],'city'=>$data['city'],'area'=>$data['area']];
            }else{
                return ['status'=>'failure'];
            }
        }else{
            $info=Db::table('pk_type')->select();
            $this->assign('info',$info);
            return $this->fetch();
        }
    }
    // 添加/修改 关于相册的存储维护
    public function pics_deal($c_id,$pics_mid)
    {
        $mid_info =$pics_mid;
        $goodspics=new GoodsPics();
        $res=$goodspics->where('mg_id',$c_id)->select();
        if($res){
            $goodspics->where('mg_id',$c_id)->delete();
        }
        if(!empty($mid_info)){
            //1) 把年月日的图片存储目录创建好
//            $dir = "./uploads/pics/".date('Ymd');
//            if (!file_exists($dir)){
//                mkdir ($dir,0777,true);
//            }

            //2) 把相册图片挪到真实位置去[遍历挪动多个]
            foreach($mid_info as $k => $v){
                //①挪动相册
                //$mid_info[$k]:代表每个路径名
//                $falsepath=$mid_info[$k];
//                $midtruepath = str_replace('picstmp','pics',$mid_info[$k]);
//                copy($mid_info[$k],$midtruepath);
//                unlink(ltrim($falsepath,'./'));
                //②把相册的路径名存储给数据库
//                $data=[];

                $data['mg_id']=$c_id;
                $data['pics_mid']=$v;
                $data['update_time']=time();
                $data['create_time']=time();
                $goodspics->insert($data);
            }
        }
    }
    //获取修改信息
    public function edit(Request $request){
        if($request->isPost()){
            $data=$request->post();
            $data['create_time']=time();
            if(!empty($data['pic'])){
                $data['logo']='.'.$data['pic'];
            }
            if(!empty($data['pics'])){
                foreach ($data['pics'] as $k){
                    $data['pics_mid'][]='.'.$k;
                }
            }
//            if(strpos($data['logo'],'goodstmp')!==false){
//                //删除(有的情况)旧图片
//                if(!empty($data['logo'])){
//
//                $dir="./uploads/goods/".date('Ymd');
//                if(!file_exists($dir)){
//                    mkdir($dir,0777,true);
//                }
//            }
//                //移动上传图片到真实位置，并存储真实位置到数据库
//                $falsepath=$data['logo'];
//                $truepath = str_replace('goodstmp','goods',$data['logo']);
//                copy($data['logo'],$truepath);
//                $data['logo'] = $truepath; //修改为真实的图片路径名
//                unlink(ltrim($falsepath,'./'));
//            }
//
//            //维护相册
//            if(array_key_exists("pics_mid",$data)){
//                unset($data['pics_mid']);
//            }
            $mg_id=$request->param('mg_id');
            $this->pics_deal($mg_id,$data['pics_mid']);
            unset($data['pic']);
            unset($data['pics']);
            unset($data['pics_mid']);
            $man=new Manager();
            $result = $man->where('mg_id',$mg_id)->update($data);//返回被修改数据的对象

            if ($result) {
                return ['status' => 'success','province'=>$data['province'],'city'=>$data['city'],'area'=>$data['area']];
            }else{
                return ['status' => 'failure'];
            }
        }else{
            $mg_id=request()->param('mg_id');
            $shuju=Db::table('pk_type')->select();
            $info=Manager::where(['mg_id'=>$mg_id])->find();
            $picsinfo=\app\admin\model\GoodsPics::where('mg_id',$mg_id)->select();
            $this->assign(['info'=>$info,'picsinfo'=>$picsinfo,'shuju'=>$shuju]);
            return $this->fetch();
        }
    }

    public function shanchu(Request $request)
    {
        $id = $request -> param('id');
        $result = \app\admin\model\Manager::where(['mg_id'=>$id]) -> delete();
        if($result){
            return ['status'=>'success'];
        }else{
            return ['status'=>'failure'];
        }
    }

    public function uploads()
    {
        if($this->request->isPost()){
            $res['code']=1;
            $res['msg'] = '上传成功！';
            $file = $this->request->file('file');
            $info = $file->move('./uploads/picstmp/');
            //halt( $info)
            if($info){
                $res['name'] = $info->getFilename();
                $pic=str_replace("\\","/",$info->getsavename());//替换"\"为"/"
                $res['filepath'] = '/uploads/picstmp/'.$pic;
            }else{
                $res['code'] = 0;
                $res['msg'] = '上传失败！'.$file->getError();
            }
            return $res;
        }
    }
    public function upload()
    {
        $file = request()->file('file');
        if($file) {
            $info=$file->move('./uploads/goodstmp/');
            if($info) {
                $res['code'] =1;
                $pic=str_replace("\\","/",$info->getsavename());//替换"\"为"/"
                $res['name']='/uploads/goodstmp/'.$pic;
//                $savename=$info->getsavename();
            } else {
                $res['code'] = 0;
                $res['msg'] = '上传失败！'.$file->getError();
//                $msg=$info->getError(); // 错误信息
            }
            return $res;
        }

    }

    /*
    * 承接 添加/修改 任务两个地方uploadify上传图片处理
    * 接收uploadify上传的图片，并处理添加到服务器上
    * 图片最终保存在 public/uploads/goods/wlek23k2okwkdlw.jpg
    */
    public function logo_up(Request $request)
    {
        //接收图片的附件信息
        //$request -> file();  接收上传的附件域信息
        $file = $request -> file('Filedata');  //返回think\File对象

        //把附件从临时位置挪到真是位置去,$file对象中有move方法可以实现
        $path = "./uploads/goodstmp/"; //附件存储位置(临时的)
        $result = $file->move($path); //系统会给附件创建一个随机的字符串名字
        //move()方法上传成功返回当前$file对象，否则false
        if($result){
            //上传成功，返回当前上传的附件路径名 用于给客户端使用
            //附件上传成功后的路径吗效果：./uploads/goods/20171231/wliedll232ekw2ewo2.jpg
            $logopathname = $path.$result->getSaveName();
            $logopathname = str_replace("\\","/",$logopathname);//替换"\"为"/"
            $info = ['status'=>'success','logopathname'=>$logopathname];
            echo json_encode($info);
        }else{
            //上传失败，返回错误信息即可
            $info = ['status'=>'failure','errorinfo'=>$result->getError()];
            echo json_encode($info);
        }
        exit; //禁止后续跟踪信息获取
    }

    public function pics_up(Request $request)
    {
        //接收图片的附件信息
        //$request -> file();  接收上传的附件域信息
        $file = $request -> file('Filedata');  //返回think\File对象

        //把附件从临时位置挪到真是位置去,$file对象中有move方法可以实现
        $path = "./uploads/picstmp/"; //附件存储位置(临时的)
        $result = $file->move($path); //系统会给附件创建一个随机的字符串名字
        //move()方法上传成功返回当前$file对象，否则false
        if($result){
            //上传好的源图片相册路径名
            //附件上传成功后的路径效果：./uploads/picstmp/20171231/wliedll232ekw2ewo2.jpg
            $picspathname = $path.$result->getSaveName();
            $picspathname = str_replace("\\","/",$picspathname);//替换"\"为"/"
            $image = \think\Image::open($picspathname);



            $image -> thumb(400,400,6); //6:图片拉伸
            $midpathname = $path.date('Ymd')."/mid_".$result->getFilename();
            //保存中图
            $image -> save($midpathname);


            //返回中图的路径名给客户端
            $info = [
                'status'=>'success',
                'midpathname'=>$midpathname,
            ];
            return json_encode($info);
        }else{
            //上传失败，返回错误信息即可
            $info = ['status'=>'failure','errorinfo'=>$result->getError()];
            return json_encode($info);
        }
        exit; //禁止后续跟踪信息获取
    }



    public function delpics(Request $request)
    {
        $pics_id=$request->post('pics_id');
        $picsinfo=GoodsPics::get($pics_id);
        //物理删除图片
        unlink($picsinfo->pics_mid);
        //删除数据库记录
        if($picsinfo->delete()){
            return ['status'=>'success'];
        }else{
            return ['status'=>'failure'];
        }

    }
}
