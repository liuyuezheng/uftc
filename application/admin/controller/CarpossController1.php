<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;
use think\Session;
use app\admin\Model\Carpos;

class CarpossController extends Controller
{
  public function lists(Request $request)
    {
        $data['number']=$request->param('number');
        $data['type']=$request->param('type');
        $data['phone']=$request->param('phone');
        $data['province']=$request->param('province');
        //$data['province']==''?$data['province']='浙江省':$data['province'];
        $data['city']=$request->param('city');
        //$data['city']==''?$data['city']='杭州市':$data['city'];
        $data['area']=$request->param('area');
        //$data['area']==''?$data['area']='滨江区':$data['area'];

        $data['manager']=$request->param('manager');

        if(!empty($data['province']) && !empty($data['city']) && !empty($data['area'])){
            $manager=Db::table('pk_manager')->where(['province'=>$data['province'],'city'=>$data['city'],'area'=>$data['area']])->column('mg_id');//默认是显示该地区下的第一个停车场
        }else if(!empty($data['province']) && !empty($data['city']) && empty($data['area'])){
            $manager=Db::table('pk_manager')->where(['province'=>$data['province'],'city'=>$data['city']])->column('mg_id');//默认是显示该地区下的第一个停车场
        }else if(!empty($data['province']) && empty($data['city']) && empty($data['area'])){
            $manager=Db::table('pk_manager')->where(['province'=>$data['province']])->column('mg_id');//默认是显示该地区下的第一个停车场
        }else if(empty($data['province']) && empty($data['city']) && empty($data['area'])){
            $manager='';
        }
        /*$manager=Db::table('pk_manager')->where(['province'=>$data['province'],'city'=>$data['city'],'area'=>$data['area']])->order('mg_id asc')->value('mg_id');//默认是显示该地区下的第一个停车场*/
        $data['manager']==''?$data['manager']=$manager:$data['manager'];
        $data['mg_name']=Session::get('mg_name');
        $data['mg_id']=Session::get('mg_id');
        $data['mg_name']=$request->param('mg_name');
        $data['mg_id']=$request->param('mg_id');
        $carpos_model=new \app\admin\model\Carposs();
        $shuju=$carpos_model->getlist($data);
        $this->assign('shuju',$shuju);
        return $this -> fetch();
//        return json_encode($shuju);
    }

     //添加个人车位
    public function personadd(){
        if(request()->isPost()){
            $mg_name=input('mg_name');
            $mg_id=input('mg_id/d');
//            $mg_id=Session::get('mg_id');
//            $mg_name=Session::get('mg_name');
            $data['owner']=2;
            $data['style']=1;//后台添加直接不用审核
            $data['types']=1;//个人车位
            $data['logo']=input('logo');
            $data['create_time']=time();
            $data['update_time']=time();
            if($mg_name=='admin'){
                $data['number']=input('number');
                $data['uid']=input('uid');
                $data['mg_id']=input('mg_id/d');
            }else{
                $data['number']=input('number');
                $data['uid']=input('uid');
                $data['mg_id']=$mg_id;
            }
            $data['type']=input('type');
            $data['uid']=str_replace('编号','',explode('-',$data['uid'])[0]);
            $province=request()->param('province');
            $city=request()->param('city');
            $area=request()->param('area');
            $manager=input('mg_id');
            $exists = Db::table('pk_carpos')->where(['mg_id'=>$data['mg_id'],'number'=>$data['number']])->where('uid','neq',$data['uid'])->find();
//            return json_encode($exists);
            if(!empty($exists) || $exists!=null){
                return ['status'=>'failure','errorinfo'=>$data['uid']];
            }else{
                $excel = Db::table('pk_carpos')->where(['mg_id'=>$data['mg_id'],'number'=>$data['number'],'style'=>1,'uid'=>$data['uid']])->find();
                $excel1 = Db::table('pk_carpos')->where(['mg_id'=>$data['mg_id'],'number'=>$data['number'],'style'=>2,'uid'=>$data['uid']])->find();

                $carpos=new \app\admin\model\Carpos();
                if(!empty($excel) || $excel!=null){
                    return ['status'=>'failure','errorinfo'=>'此用户已拥有该车位'];
                }else if(!empty($excel1) || $excel1!=null){
//                    return json_encode($excel);
                    $res=$carpos->where('id',$excel1['id'])->update(['style'=>1,'update_time'=>time()]);
                }else{
                    if(empty($logo)){
                        return ['status'=>'failure','errorinfo'=>'请选择车位图片'];
                    }else{
                        $res=$carpos->save($data);
                    }

                }
                if(!empty($res)){
                    return ['status'=>'success','province'=>$province,'city'=>$city,'area'=>$area,'manager'=>$manager];
                }else{
                    return ['status'=>'failure'];
                }
            }

        }else{
            $info=DB::table('pk_user')->where('plate','neq','')->field('uid,username')->select();
            $data['mg_name']=Session::get('mg_name');
            $data['mg_id']=Session::get('mg_id');
            $this->assign(['data'=>$data,'info'=>$info]);
            return $this->fetch();
//            return json_encode($info);
        }
    }

    //添加物业车位
    public function wuyeadd(){
        if(request()->isPost()){
            $mg_id=Session::get('mg_id');
            $mg_name=Session::get('mg_name');
            if($mg_name=='admin'){
                $data['number']=input('number');
                $data['uid']=input('uid');
                $data['mg_id']=input('mg_id/d');
            }else{
                $data['number']=input('number');
                $data['uid']=input('uid');
                $data['mg_id']=$mg_id;
            }
            $data['type']=input('type');
            $data['types']=input('types');//2出租车位，4临时
            if($data['types']==2){
                $data['start_time']=strtotime(input('start_time'));
                $data['end_time']=strtotime(input('end_time'));
                $data['duration']=getMonthNum(input('start_time'),input('end_time'));
//                if($data['duration']<1){
//
//                }
            }else if($data['type']==4){
                $data['start_time']=null;
                $data['end_time']=null;
                $data['duration']=null;
            }
            $data['charge']=input('charge');
            $data['logo']=input('logo');
            $data['owner']=1;
            $data['style']=1;//后台添加直接不用审核
            $data['update_time']=time();
            $data['create_time']=time();
            $province=request()->param('province');
            $city=request()->param('city');
            $area=request()->param('area');
            $manager=input('mg_id/d');
            $exists = Db::table('pk_carpos')->where(['mg_id'=>$data['mg_id'],'number'=>$data['number']])->find();

            if(!empty($exists)){
                return ['status'=>'failure','errorinfo'=>'该车位已存在'];
            }else{
              if(empty($data['logo'])){
                    return ['status'=>'failure','errorinfo'=>'请添加车位图'];
                }else{
                    $carpos=new \app\admin\model\Carpos();
                    $res=$carpos->save($data);
                    if(!empty($res)){
                        return ['status'=>'success','province'=>$province,'city'=>$city,'area'=>$area,'manager'=>$manager];
                    }else{
                        return ['status'=>'failure'];
                    }
                }
            }
        }else{
            $data['mg_name']=Session::get('mg_name');
            $data['mg_id']=Session::get('mg_id');
            $this->assign('data',$data);
            return $this->fetch();
        }
    }

    //修改车位
    public function edit(){
        if(request()->isPost()){
            $mg_id=Session::get('mg_id');
            $mg_name=Session::get('mg_name');
            $id=request()->param('id');
            $manager=input('mg_id');
            $data=Db::table('pk_carpos')->where(['id'=>$id])->find();
            if($mg_name=='admin'){
                $data['number']=input('number');
                $data['mg_id']=input('mg_id');
                $data['uid']=input('uid');
                $data['logo']=input('logo');
            }else{
                $data['number']=input('number');
                $data['mg_id']=$mg_id;
                $data['uid']=input('uid');
                $data['logo']=input('logo');
            }
            $data['type']=input('type');
            $types=input('types');
            if(empty($types)){
                $data['types']=$data['types'];
            }else{
                $data['types']=$types;
                if($data['types']==2){
                    $data['start_time']=strtotime(input('start_time'));
                    $data['end_time']=strtotime(input('end_time'));
                    $data['duration']=getMonthNum(input('start_time'),input('end_time'));
                }else if($data['types']==4){
                    $data['start_time']=null;
                    $data['end_time']=null;
                    $data['duration']=null;
                }
            }
            /*if($data['types']==2){
                    $data['start_time']=strtotime(input('start_time'));
                    $data['end_time']=strtotime(input('end_time'));
                    $data['duration']=getMonthNum(input('start_time'),input('end_time'));
                }else if($data['types']==4){
                    $data['start_time']=null;
                    $data['end_time']=null;
                    $data['duration']=null;
                }*/
            
            if(!empty(input('charge'))){
                $data['charge']=input('charge');
            }else if(!empty(input('charge1'))){
                $data['charge1']=input('charge1');
            }
            
            $data['uid']=str_replace('编号','',explode('-',$data['uid'])[0]);
            $province=input('province');
            $city=input('city');
            $area=input('area');
            
            $res=\app\admin\model\Carpos::where(['id'=>$id])->update($data);
            if(!empty($res)){
                return json(['status'=>'success','province'=>$province,'city'=>$city,'area'=>$area,'manager'=>$manager]);
            }else{
                return json(['status'=>'failure']);
            }
        }else{
            $id=request()->param('id');
            $data=\app\admin\model\Carpos::alias('c')
            ->join('__USER__ u','u.uid=c.uid','left')
            ->join('__MANAGER__ m','m.mg_id=c.mg_id','left')
            ->where(['c.id'=>$id])
            ->field('c.*,u.username,m.name,m.province,m.city,m.area,c.mg_id,c.charge,c.charge1,c.types')->find();
            if(!empty($data['start_time'])){
                $data['start_time']=date('Y-m-d',$data['start_time']);
                $data['end_time']=date('Y-m-d',$data['end_time']);
            }
            $shuju['mg_name']=Session::get('mg_name');
            $shuju['mg_id']=Session::get('mg_id');
            $info=DB::table('pk_user')->field('uid,username')->select();
            $this->assign(['data'=>$data,'shuju'=>$shuju,'info'=>$info,'id'=>$id]);
            return $this->fetch();
        }
    }
//个人车位审核列表
    public function sindex(Request $request){
        $data['username']=$request->param('username');
        $data['phone']=$request->param('phone');
        $data['province']=$request->param('province');
        //$data['province']==''?$data['province']='浙江省':$data['province'];
        $data['city']=$request->param('city');
        //$data['city']==''?$data['city']='杭州市':$data['city'];
        $data['area']=$request->param('area');
        //$data['area']==''?$data['area']='滨江区':$data['area'];

        $data['manager']=$request->param('manager');
        if(!empty($data['province']) && !empty($data['city']) && !empty($data['area'])){
            $manager=Db::table('pk_manager')->where(['province'=>$data['province'],'city'=>$data['city'],'area'=>$data['area']])->column('mg_id');//默认是显示该地区下的第一个停车场
        }else if(!empty($data['province']) && !empty($data['city']) && empty($data['area'])){
            $manager=Db::table('pk_manager')->where(['province'=>$data['province'],'city'=>$data['city']])->column('mg_id');//默认是显示该地区下的第一个停车场
        }else if(!empty($data['province']) && empty($data['city']) && empty($data['area'])){
            $manager=Db::table('pk_manager')->where(['province'=>$data['province']])->column('mg_id');//默认是显示该地区下的第一个停车场
        }else if(empty($data['province']) && empty($data['city']) && empty($data['area'])){
            $manager='';
        }
        $data['manager']==''?$data['manager']=$manager:$data['manager'];
        $data['mg_name']=Session::get('mg_name');
        $data['mg_id']=Session::get('mg_id');
        $carpos_model=new \app\admin\model\Carposs();
        $shuju=$carpos_model->getslist($data);
        $this->assign('shuju',$shuju);
        return $this -> fetch();
    }
    //审核操作
    public function verify(Request $request){
        if($request->isPost()){
            $data['id']=$request->post('id');
            $data['style']=$request->post('style');
            $uid=$request->post('uid');
            $mg_id=input('mg_id');
            $data['remark']=input('remark');
            $data['number']=input('number');
            $data['update_time']=time();
            $mg_name=Db::table('pk_manager')->where(['mg_id'=>$mg_id])->value('name');
            if($data['style']==1){
                //此时表示审核通过，然后该用户成为个人车位用户
                //$shuju['type']=1;
                $mg_ids=Db::table('pk_user')->where(['uid'=>$uid])->value('mg_id');
//                $mg_ids=Db::table('pk_user')->where(['uid'=>$uid])->value('');
                if(!empty($mg_ids)){
                    $mg_ids_array=explode(',',$mg_ids);
                    if(!in_array($mg_id,$mg_ids_array)){
                        array_push($mg_ids_array,$mg_id);
                    }
                    $shuju['mg_id']=implode(',',$mg_ids_array);
                }else{
                    $shuju['mg_id']=$mg_id;
                }
                $shuju['update_time']=time();
                $up3=array(
                    'uid'=>$uid,
                    'mg_id'=>$shuju['mg_id'],
                    'p_number'=>$data['number'],
                    // 'end_time'=> $list['endtime'],
                    'title'=>'系统消息',
                    'message'=>"您在{$mg_name}申请的个人车位{$data['number']}车位审核通过。",
                    'createtime'=>$shuju['update_time'],
                    'updatetime'=>$shuju['update_time'],
//                    'endtime'=>$list['endtime'],
                    'type'=>6,
                    'status'=>4
                );
                Db::table('pk_message')->insert($up3);
                $ress=Db::table('pk_user')->where(['uid'=>$uid])->update($shuju);
            }else{
                $up3=array(
                    'uid'=>$uid,
                    'mg_id'=>$mg_id,
                    'p_number'=>$data['number'],
                    // 'end_time'=> $list['endtime'],
                    'title'=>'系统消息',
                    'message'=>"您在{$mg_name}申请的个人车位{$data['number']}车位审核未通过。",
                    'createtime'=>time(),
                    'updatetime'=>time(),
//                    'endtime'=>$list['endtime'],
                    'type'=>6,
                    'status'=>4
                );
                Db::table('pk_message')->insert($up3);
            }
            $res=\app\admin\model\Carpos::update($data);
            if($res){
                return ['status'=>'success'];
            }else{
                return ['status'=>'failure'];
            }
        }else{
            $id=$request->param('id');
            $logo=DB::table('pk_carpos')->where(['id'=>$id])->field('logo,uid,mg_id,number')->find();
            $this->assign(['id'=>$id,'logo'=>$logo['logo'],'uid'=>$logo['uid'],'mg_id'=>$logo['mg_id'],'number'=>$logo['number']]);
            return $this->fetch();
        }
    }
    
    //删除
    public function shanchu(){
        $id=request()->param('id');
        $res=\app\admin\model\Carpos::where(['id'=>$id])->delete();
        if(!empty($res)){
            return ['status'=>'success'];
        }else{
            return ['status'=>'failure'];
        }
    }

    //车位租赁用户显示
    public function index(Request $request)
    {
        $data['number']=$request->param('number');
        $data['types']=$request->param('types');
        $data['phone']=$request->param('phone');
        $data['province']=$request->param('province');
        //$data['province']==''?$data['province']='浙江省':$data['province'];
        $data['city']=$request->param('city');
        //$data['city']==''?$data['city']='杭州市':$data['city'];
        $data['area']=$request->param('area');
        //$data['area']==''?$data['area']='滨江区':$data['area'];

        $data['manager']=$request->param('manager');
        if(!empty($data['province']) && !empty($data['city']) && !empty($data['area'])){
            $manager=Db::table('pk_manager')->where(['province'=>$data['province'],'city'=>$data['city'],'area'=>$data['area']])->column('mg_id');//默认是显示该地区下的第一个停车场
        }else if(!empty($data['province']) && !empty($data['city']) && empty($data['area'])){
            $manager=Db::table('pk_manager')->where(['province'=>$data['province'],'city'=>$data['city']])->column('mg_id');//默认是显示该地区下的第一个停车场
        }else if(!empty($data['province']) && empty($data['city']) && empty($data['area'])){
            $manager=Db::table('pk_manager')->where(['province'=>$data['province']])->column('mg_id');//默认是显示该地区下的第一个停车场
        }else if(empty($data['province']) && empty($data['city']) && empty($data['area'])){
            $manager='';
        }
        $data['manager']==''?$data['manager']=$manager:$data['manager'];
        $data['mg_name']=Session::get('mg_name');
        $data['mg_id']=Session::get('mg_id');
//        $data['mg_name']=$request->param('mg_name');
//        $data['mg_id']=$request->param('mg_id');
        $carpos_model=new \app\admin\model\Carposs();
        $shuju=$carpos_model->getlists($data);
        $this->assign('shuju',$shuju);
        return $this -> fetch();
//        return json_encode($shuju);
    }

    public function hisindex(Request $request){
        $data['number']=$request->param('number');
        $data['types']=$request->param('types');
        $data['phone']=$request->param('phone');
        $data['province']=$request->param('province');
        //$data['province']==''?$data['province']='浙江省':$data['province'];
        $data['city']=$request->param('city');
        //$data['city']==''?$data['city']='杭州市':$data['city'];
        $data['area']=$request->param('area');
        //$data['area']==''?$data['area']='滨江区':$data['area'];

        $data['manager']=$request->param('manager');
        if(!empty($data['province']) && !empty($data['city']) && !empty($data['area'])){
            $manager=Db::table('pk_manager')->where(['province'=>$data['province'],'city'=>$data['city'],'area'=>$data['area']])->column('mg_id');//默认是显示该地区下的第一个停车场
        }else if(!empty($data['province']) && !empty($data['city']) && empty($data['area'])){
            $manager=Db::table('pk_manager')->where(['province'=>$data['province'],'city'=>$data['city']])->column('mg_id');//默认是显示该地区下的第一个停车场
        }else if(!empty($data['province']) && empty($data['city']) && empty($data['area'])){
            $manager=Db::table('pk_manager')->where(['province'=>$data['province']])->column('mg_id');//默认是显示该地区下的第一个停车场
        }else if(empty($data['province']) && empty($data['city']) && empty($data['area'])){
            $manager='';
        }
        $data['manager']==''?$data['manager']=$manager:$data['manager'];
        $data['mg_name']=Session::get('mg_name');
        $data['mg_id']=Session::get('mg_id');
//        $data['mg_name']=$request->param('mg_name');
//        $data['mg_id']=$request->param('mg_id');
        $carpos_model=new \app\admin\model\Historycarpos();
        $res=overdue();
        $key='id';
        $key2='type';
        $info=array_remove_by_key($res,$key);
        $rans=array_remove_by_key($info,$key2);
        $res2=Db::table('pk_historycarpos')->select();
//        $info2=array_remove_by_key($res2,$key);
//        $rans2=array_remove_by_key($info2,$key2);
        if(!empty($res)){
            if(!empty($res2)){
                $info2=array_remove_by_key($res2,$key);
                $rans2=array_remove_by_key($info2,$key2);
//               $aa=in_array($rans[1],$rans2) ;
                foreach ($rans as $keys){
                    $isresult=in_array($keys,$rans2);
                    if($isresult==false){
                        $val=Db::table('pk_historycarpos')->insert($keys);
                    }
                }
            }else{
                foreach ($res as $k){
                    $val=Db::table('pk_historycarpos')->insert($k);
                }
            }
//            return json_encode(['code'=>1000,'data'=>$val,'msg'=>'操作成功']);
        }else{
            $val=0;
//            return json_encode(['code'=>1001,'data'=>$val,'msg'=>'操作失败']);
        }
        $shuju=$carpos_model->getlist ($data);
        $this->assign('shuju',$shuju);
        return $this -> fetch();
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
            $shuju = [];
            $i=0;
            $j=0;
            //dump($excel_array);die;
            foreach($excel_array as $k=>$v) {
                $exists = Db::table('pk_carpos')->where(['mg_id'=>$v[1],'number'=>$v[0]])->find();
                if(empty($exists)){
                    $data[$i]['number'] = $v[0];
                    $data[$i]['mg_id'] = $v[1];
                    $data[$i]['owner'] = $v[2];
                    $data[$i]['uid'] = $v[3];
                    $data[$i]['types'] = $v[4];
                    $arr=date_parse_from_format('m-d-Y',$v[5]);
                    $data[$i]['start_time'] = mktime(0,0,0,$arr['month'],$arr['day'],$arr['year']);
                    $arr1=date_parse_from_format('m-d-Y',$v[6]);
                    $data[$i]['end_time'] = mktime(0,0,0,$arr1['month'],$arr1['day'],$arr1['year']);
                    $data[$i]['style']= $v[7];
                    $data[$i]['charge']= $v[8];
                    $data[$i]['uuid']= $v[9];
                    $data[$i]['type']= $v[10];
                    $i++;
                }else{
                    $shuju[$j]['number'] = $v[0];
                    $shuju[$j]['mg_id'] = $v[1];
                    $shuju[$j]['owner'] = $v[2];
                    $shuju[$j]['uid'] = $v[3];
                    $shuju[$j]['types'] = $v[4];
                    $arr=date_parse_from_format('m-d-Y',$v[5]);
                    $shuju[$j]['start_time'] = mktime(0,0,0,$arr['month'],$arr['day'],$arr['year']);
                    $arr1=date_parse_from_format('m-d-Y',$v[6]);
                    $shuju[$j]['end_time'] = mktime(0,0,0,$arr1['month'],$arr1['day'],$arr1['year']);
                    $shuju[$j]['style']= $v[7];
                    $shuju[$j]['charge']= $v[8];
                    $shuju[$j]['uuid']= $v[9];
                    $shuju[$j]['type']= $v[10];
                    $j++;
                }
            }
/*            dump($shuju);die;*/
            Db::startTrans();
            try{
                if(!empty($data)){
                    $success=Db::table('pk_carpos')->insertAll($data); //批量插入数据
                    if(empty($success)){
                        Db::rollback();
                    }
                }

                if(!empty($shuju)){
                    foreach($shuju as $k=>$v){
                        $succes=Db::table('pk_carpos')->where(['mg_id'=>$v['mg_id'],'number'=>$v['number']])->update($v);
                        if(empty($succes)){
                            Db::rollback();
                        }
                    }
                }

                Db::commit();
                $url=url('admin/carposs/index');
          echo <<<AAA
          <script type='text/javascript'>
            alert('导入成功');
            window.parent.location.href="$url";
          </script>
AAA;
                
            }catch(\Exception $e){
                Db::rollback();
            }
        }
        }else{
            return $this->fetch();
        }
    }

}
