<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\Car;
use think\Session;
use think\Db;

class CarController extends Controller
{
    public function index(Request $request)
    {
        $data['username']=$request->param('username');
        $data['phone']=$request->param('phone');
        $data['type']=$request->param('type');
        $data['province']=$request->param('province');
        $data['province']==''?$data['province']='浙江省':$data['province'];
        $data['city']=$request->param('city');
        $data['city']==''?$data['city']='杭州市':$data['city'];
        $data['area']=$request->param('area');
        $data['area']==''?$data['area']='滨江区':$data['area'];

        $data['manager']=$request->param('manager');
        $manager=Db::table('pk_manager')->where(['province'=>$data['province'],'city'=>$data['city'],'area'=>$data['area']])->order('mg_id asc')->value('mg_id');
        $data['manager']==''?$data['manager']=$manager:$data['manager'];
        $data['mg_name']=Session::get('mg_name');
        $data['mg_id']=Session::get('mg_id');
        $car_model=new \app\admin\model\Car();
        $shuju=$car_model->getlist($data);
        $this->assign('shuju',$shuju);
        return $this -> fetch();
    }

    public function verify(Request $request){
        if($request->isPost()){
            $data=$request->post();
            $data['update_time']=time();
            $res=\app\admin\model\Car::update($data);
            if($res){
                return ['status'=>'success'];
            }else{
                return ['status'=>'failure'];
            }
        }else{
            $id=$request->param('car_id');
            $info=\app\admin\model\Car::where(['car_id'=>$id])->field('car_id,type,logo,dlogo,remark')->find();
            $this->assign(['info'=>$info]);
            return $this->fetch();
        }
    }

    //导入
     public function enterport(Request $request){
        if($request->isPost()){
            vendor('phpoffice.phpexcel');
            Vendor("phpoffice.PHPExcel.IOFactory");
            $objPHPExcel = new \PHPExcel();
            $file = $request->file('excel');
            $info = $file->move(ROOT_PATH . 'public' . DS . 'excel');
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
            $i=0;
            foreach($excel_array as $k=>$v) {
                $data[$k]['plate'] = $v[0];
                $data[$k]['brand'] = $v[1];
                $data[$k]['version'] = $v[2];
                $data[$k]['displacement'] = $v[3];
                $data[$k]['phone'] = $v[4];
                $data[$k]['type'] = $v[5];
                $data[$k]['uid']=Db::table('pk_user')->where(['plate'=>$v[0]])->value('uid');
                $i++;
            }
            $success=Db::table('pk_cars')->insertAll($data); //批量插入数据
            if($success){
                $url=url('admin/car/index');
          echo <<<AAA
          <script type='text/javascript'>
            alert('导入成功');
            window.parent.location.href="$url";
          </script>
AAA;
            }
        }
        }else{
            return $this->fetch();
        }
    }
}
