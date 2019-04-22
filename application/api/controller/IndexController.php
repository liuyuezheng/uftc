<?php

namespace app\api\controller;

use think\Controller;
use think\Request;
use app\api\Model\Stopcarrecord;
use app\api\Model\Manager;
use app\api\Controller\IndexalipayController;
use app\api\Controller\IndexwxpayController;
use app\api\Controller\IndexsmoneyController;
use app\api\validate\IndexValidate;
use app\api\service\TokenService;
use think\Db;

class IndexController extends BaseController
{
    protected $validate = null;
    public function __construct()
    {
        $this ->validate = new IndexValidate();
    }

    //首页
    public function index()
    {
        $data=input();//给我当前经纬度
        $distance=5;
        $where="sqrt( ( ((".$data['longitude']."-longitude)*PI()*12656*cos(((".$data['latitude']."+latitude)/2)*PI()/180)/180) * ((".$data['longitude']."-longitude)*PI()*12656*cos (((".$data['latitude']."+latitude)/2)*PI()/180)/180) ) + ( ((".$data['latitude']."-latitude)*PI()*12656/180) * ((".$data['latitude']."-latitude)*PI()*12656/180) ) )/2 < ".$distance;
        $info=Db::table('pk_manager')->where($where)->field('longitude,latitude,mg_id,total,name,area,address,leftpos')->select();
        if(!empty($info)){
            foreach($info as $k=>$v){
                $info[$k]['lo']=$v['longitude'];
                $info[$k]['la']=$v['latitude'];
                $info[$k]['markerAddress']=$v['area'].$v['address'];
                $info[$k]['marker']='';
$info[$k]['name']=$v['name'];

            }
        }
        return json_encode(['code'=>1000,'shuju'=>$info,'msg'=>'第一页信息获取成功']);
    }
    //出租共享是否过期
    public function isoverdue(){
        $uid=input('uid/d');
//        $arr=[];
        if (!TokenService::checkUserId($uid)) {
            return success(1002, '', '无效token');
        }

        if(!empty($res)){
            $res=overdue($uid);
            $key='id';
            $key2='type';
            $info=array_remove_by_key($res,$key);
            $rans=array_remove_by_key($info,$key2);
            $res2=Db::table('pk_historycarpos')->where('uid',$uid)->whereOr('uuid',$uid)->whereOr('uuuid',$uid)->select();
            $info2=array_remove_by_key($res2,$key);
            $rans2=array_remove_by_key($info2,$key2);
            if(!empty($res2)){
                $info2=array_remove_by_key($res2,$key);
                $rans2=array_remove_by_key($info2,$key2);
//               $aa=in_array($rans[1],$rans2) ;
               foreach ($rans as $keys){
                   $isresult=in_array($keys,$rans2);
                   if($isresult==false){
                       $val=Db::table('pk_historycarpos')->insert($keys);
                   }else{
                       $val=0;
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
        if($val==1){
            return json_encode(['code'=>1000,'data'=>$val,'msg'=>'操作成功']);
        }else{
            return json_encode(['code'=>1001,'data'=>$val,'msg'=>'操作失败']);
        }


    }
   //最近使用
    public function indexs(){
        $uid=input('uid/d');
        if (!TokenService::checkUserId($uid)) {
            return success(1002, '', '无效token');
        }
        if(!empty($uid)){
            $shuju=Db::table('pk_carpos')->alias('c')
            ->join('__MANAGER__ m','m.mg_id=c.mg_id')
            ->where(['c.uid'=>$uid])->whereor(['c.uuid'=>$uid])
                ->whereor(['c.uuuid'=>$uid])
            ->order('c.create_time desc')
            ->limit(1)
            ->field('m.name,c.number,c.mg_id')
            ->find();
            if(empty($shuju)){
               $shuju=[];
            }
        }else{
            $shuju=[];
        }
        return json_encode(['code'=>1000,'data'=>$shuju,'msg'=>'第一页信息获取成功']);
    }
 //点击停车场获取收费及距离页面
    public function gocarpos(){
    	$longitude=input('longitude');
    	$latitude=input('latitude');//当前位置的经纬度
        $longitude1=input('longitude1');
        $latitude1=input('latitude1');//点击位置的经纬度
        $sql = "select mg_id,name,charge from pk_manager where longitude=".$longitude1." and latitude=".$latitude1;
    	/*$data=Db::table('pk_manager')->where(['longitude'=>$longitude1])->field('name,charge')->find();*/
        $shuju = Db::query($sql);
        $data = $shuju[0];
    	$data['distance']=getdistance($longitude1,$latitude1,$longitude,$latitude);
    	if(!empty($data)){
    		return json_encode(['code'=>1000,'data'=>$data,'msg'=>'停车场收费及距离信息获取成功']);
    	}else{
    		return json_encode(['code'=>1001,'msg'=>'停车场收费及距离信息获取失败']);
    	}
    }

    //点击找停车场
    public function searchpos(){
        $uid=input('uid/d');
        if (!TokenService::checkUserId($uid)) {
            return success(1002, '', '无效token');
        }
        $status=DB::table('pk_stopcarorder')->where(['uid'=>$uid])->order('id desc')->limit(1)->field('status,order_id')->find();
        if(!empty($status)){
            if($status['status']==2){
                //此时表示未支付
                return json_encode(['code'=>1001,'data'=>$status['order_id'],'msg'=>'跳到支付页面']);
            }else{
                return json_encode(['code'=>1000,'msg'=>'跳到搜索页面']);
            }
        }else{
            return json_encode(['code'=>1000,'msg'=>'跳到搜索页面']);
        }
    }

    //点击去车场图标
    public function goposflag(){
        $uid=input('uid');
        if (!TokenService::checkUserId($uid)) {
            return success(1002, '', '无效token');
        }
        $status=DB::table('pk_stopcarorder')->where(['uid'=>$uid])->order('id desc')->limit(1)->field('status,order_id')->find();
        if(!empty($status)){
            if($status['status']==2){
                //此时表示未支付
                return json_encode(['code'=>1001,'data'=>$status['order_id'],'msg'=>'跳到未支付支付页面']);
            }else{
                return json_encode(['code'=>1000,'msg'=>'跳到停车场详情页面']);
            }
        }else{
            return json_encode(['code'=>1000,'msg'=>'跳到停车场详情页面']);
        }
    }

    Public function search(){
        $longitude=input('longitude');
        $latitude=input('latitude');
        $distance=10;//1公里以内的信息，这里的10公里为半径
        $sql = "select name,mg_id,address,area,sqrt( ( ((".$longitude."-longitude)*PI()*12656*cos(((".$latitude."+latitude)/2)*PI()/180)/180) * ((".$longitude."-longitude)*PI()*12656*cos (((".$latitude."+latitude)/2)*PI()/180)/180) ) + ( ((".$latitude."-latitude)*PI()*12656/180) * ((".$latitude."-latitude)*PI()*12656/180) ) )/2 as dis
  from pk_manager group by dis asc having dis <".$distance;
        $data=Db::query($sql);
        if(!empty($data)){
            foreach($data as $k=>$v){
                $data[$k]['dis']=round($v['dis']*1000);
                $data[$k]['address']=$v['area'].$v['address'];
            }
        }
        if(!empty($data)){
            return json_encode(['code'=>1000,'data'=>$data,'msg'=>'搜索推荐获取成功']);
        }else{
            return json_encode(['code'=>1001,'msg'=>'搜索推荐获取失败']);
        }
    }


    //在搜索停车场里面输入内容后
    public function searchcontent(){
        $city=input('city');
        $longitude=input('longitude');
        $latitude=input('latitude');
        $flag=input('flag',1);
        $page=input('page',1);
        $num=2;
        $start=($page-1)*$num;
        //$distance=5;//5公里以内的信息，这里的1公里为半径
        $sql1="select count('mg_id') from pk_manager where city=".$city;
        $total=DB::query($sql1);
        $total=$total[0]["count('mg_id')"];
        if($flag==1){
            //表示按距离排序
            $sql = "select name,mg_id,charge,total,leftpos,sqrt( ( ((".$longitude."-longitude)*PI()*12656*cos(((".$latitude."+latitude)/2)*PI()/180)/180) * ((".$longitude."-longitude)*PI()*12656*cos (((".$latitude."+latitude)/2)*PI()/180)/180) ) + ( ((".$latitude."-latitude)*PI()*12656/180) * ((".$latitude."-latitude)*PI()*12656/180) ) )/2 as dis
from pk_manager where city=".$city." group by dis asc " ." limit " .$start.",".$num;

            $data=Db::query($sql);
        }else if($flag==2){
            //表示按收费排序
            $sql = "select name,mg_id,charge,total,leftpos,sqrt( ( ((".$longitude."-longitude)*PI()*12656*cos(((".$latitude."+latitude)/2)*PI()/180)/180) * ((".$longitude."-longitude)*PI()*12656*cos (((".$latitude."+latitude)/2)*PI()/180)/180) ) + ( ((".$latitude."-latitude)*PI()*12656/180) * ((".$latitude."-latitude)*PI()*12656/180) ) )/2 as dis
from pk_manager where city=".$city." order by charge asc limit " .$start.",".$num;
            $data=Db::query($sql);
        }else if($flag==3){
            //表示按空车位数排序
            $sql = "select name,mg_id,charge,total,leftpos,sqrt( ( ((".$longitude."-longitude)*PI()*12656*cos(((".$latitude."+latitude)/2)*PI()/180)/180) * ((".$longitude."-longitude)*PI()*12656*cos (((".$latitude."+latitude)/2)*PI()/180)/180) ) + ( ((".$latitude."-latitude)*PI()*12656/180) * ((".$latitude."-latitude)*PI()*12656/180) ) )/2 as dis
from pk_manager where city=".$city." order by leftpos desc limit " .$start.",".$num;
            $data=Db::query($sql);
        }
     if(!empty($data)){
        foreach($data as $k=>$v){
            $data[$k]['dis']=round($v['dis']*1000);
        }
    }
    $maxpage=ceil($total/$num);
    return json_encode(['code'=>1000,'data'=>$data,'maxpage'=>$maxpage,'msg'=>'搜索停车场信息获取成功']);
}

     //在搜索停车场里面输入内容后
    public function searchcontents(){
        $city=input('city');
        $content=input('content','');
        $longitude=input('longitude');
        $latitude=input('latitude');
        $flag=input('flag',1);
        $page=input('page',1);
        $num=2;
        $start=($page-1)*$num;
        /*return json_encode(['city'=>$city,'content'=>$content,'longitude'=>$longitude,'latitude'=>$latitude,'flag'=>$flag,'page'=>$page]);*/
        //$distance=5;//5公里以内的信息，这里的1公里为半径
        $sql1="select count('mg_id') from pk_manager where city='".$city."'";
        $total=DB::query($sql1);
        $total=$total[0]["count('mg_id')"];

        if(!empty($content)){
            //此时表示搜索了
            $sql="select name,mg_id,charge,total,leftpos,sqrt( ( ((".$longitude."-longitude)*PI()*12656*cos(((".$latitude."+latitude)/2)*PI()/180)/180) * ((".$longitude."-longitude)*PI()*12656*cos (((".$latitude."+latitude)/2)*PI()/180)/180) ) + ( ((".$latitude."-latitude)*PI()*12656/180) * ((".$latitude."-latitude)*PI()*12656/180) ) )/2 as dis
from pk_manager where city='".$city."' and name like '" ."%".trim($content,'\'')."%' order by dis asc";
        $data=Db::query($sql);
        if(!empty($data)){
        foreach($data as $k=>$v){
            $data[$k]['dis']=round($v['dis']*1000);
        }
    }
        return json_encode(['code'=>1000,'data'=>$data,'msg'=>'搜索停车场信息获取成功']);
        }else{
           if($flag==1){
            //表示按距离排序
            $sql = "select name,mg_id,charge,total,leftpos,sqrt( ( ((".$longitude."-longitude)*PI()*12656*cos(((".$latitude."+latitude)/2)*PI()/180)/180) * ((".$longitude."-longitude)*PI()*12656*cos (((".$latitude."+latitude)/2)*PI()/180)/180) ) + ( ((".$latitude."-latitude)*PI()*12656/180) * ((".$latitude."-latitude)*PI()*12656/180) ) )/2 as dis
from pk_manager where city='".$city."' group by dis asc " ." limit " .$start.",".$num;

            $data=Db::query($sql);
        }else if($flag==2){
            //表示按收费排序
            $sql = "select name,mg_id,charge,total,leftpos,sqrt( ( ((".$longitude."-longitude)*PI()*12656*cos(((".$latitude."+latitude)/2)*PI()/180)/180) * ((".$longitude."-longitude)*PI()*12656*cos (((".$latitude."+latitude)/2)*PI()/180)/180) ) + ( ((".$latitude."-latitude)*PI()*12656/180) * ((".$latitude."-latitude)*PI()*12656/180) ) )/2 as dis
from pk_manager where city='".$city."' order by charge asc limit " .$start.",".$num;
            $data=Db::query($sql);
        }else if($flag==3){
            //表示按空车位数排序
            $sql = "select name,mg_id,charge,total,leftpos,sqrt( ( ((".$longitude."-longitude)*PI()*12656*cos(((".$latitude."+latitude)/2)*PI()/180)/180) * ((".$longitude."-longitude)*PI()*12656*cos (((".$latitude."+latitude)/2)*PI()/180)/180) ) + ( ((".$latitude."-latitude)*PI()*12656/180) * ((".$latitude."-latitude)*PI()*12656/180) ) )/2 as dis
from pk_manager where city='".$city."' order by leftpos desc limit " .$start.",".$num;
            $data=Db::query($sql);
        } 
        }

     if(!empty($data)){
        foreach($data as $k=>$v){
            $data[$k]['dis']=round($v['dis']*1000);
        }
    }
    $maxpage=ceil($total/$num);
    return json_encode(['code'=>1000,'data'=>$data,'maxpage'=>$maxpage,'msg'=>'搜索停车场信息获取成功']);
}


    //首页停车场详细信息
    public function detail(){
        $mg_id=input('mg_id/d');

        $longitude=input('longitude');
        $latitude=input('latitude');
        $shuju=Db::table('pk_goods_pics')->where(['mg_id'=>$mg_id])->column('pics_mid');
//        if(!empty($shuju)){
//            foreach($shuju as $k=>$v){
//                $shuju[$k]=$v;
//            }
//        }
        $data=DB::table('pk_manager')->where(['mg_id'=>$mg_id])->field('name,address,charge,total,longitude,latitude,logo,area,city,leftpos,types')->find();
        $data['distance']=round(getdistance($data['longitude'],$data['latitude'],$longitude,$latitude));
        $data['address']=$data['city'].$data['area'].$data['address'];
        $data['logo']=getImage($data['logo']);

        if(!empty($data)){
            return json_encode(['code'=>1000,'data'=>$data,'shuju'=>getImage($shuju),'msg'=>'首页停车场详细信息获取成功']);
        }else{
            return json_encode(['code'=>1001,'msg'=>'首页停车场信息获取失败']);
        }
    }

    //点击进入停车场
    public function clickenter(){
        $uid=input('uid/d');
        if (!TokenService::checkUserId($uid)) {
            return success(1002, '', '无效token');
        }
        $mg_id=input('mg_id');
        //首先查出这个人有没有绑定车牌号
        $plate=Db::table('pk_user')->where(['uid'=>$uid])->field('plate')->find();
        if(empty($plate)){
            //此时表示该用户没有绑定车牌号
            return json_encode(['code'=>1001,'msg'=>'请先绑定车牌号']);
        }else{
            return json_encode(['code'=>1000,'msg'=>'操作成功']);
            //此时表示已绑定车牌号,那就展示停车场的平面图
            /*$shuju['in_time']=time();
            $shuju['uid']=$uid;
            $shuju['order_id'] = date('YmdHis') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            $shuju['mg_id']=$mg_id;
            $res=Db::table('pk_stopcarrecord')->insert($shuju);*/
            //调用接口，展示车场平面图

        }
    }

    //绑定车牌
    public function bindplate(){
        $oldchar=array(" ","　","\t","\n","\r");
        $newchar=array("","","","","");
        $data['plate']=str_replace($oldchar,$newchar,input('plate'));
//        $data['plate']=trim(input('plate'));
//        $data['plate']=substr(' ',trim(input('plate')));
        $uid=input('uid');
        if (!TokenService::checkUserId($uid)) {
            return success(1002, '', '无效token');
        }
        $pattern="/[京津沪渝冀豫云辽黑湘皖鲁新苏浙赣鄂桂甘晋蒙陕吉闽贵粤青藏川宁琼使领A-Z]{1}[A-Z]{1}[A-Z0-9]{4}[A-Z0-9挂学警港澳]{1}/";
        $isplate=preg_match($pattern,$data['plate']);

        if (!TokenService::checkUserId($uid)) {
            return success(1002, '', '无效token');
        }

        if($isplate==1){
            $res=DB::table('pk_user')->where(['uid'=>$uid])->update($data);
        }else{
            $res='';
        }
        if(!empty($res)){
            return json_encode(['code'=>1000,'msg'=>'车牌绑定成功']);
        }else{
            return json_encode(['code'=>1001,'msg'=>'车牌格式有误','data'=>$data['plate']]);
        }
    }
    public function bindCar(){
        $param = request()->only(['uid','carImg','brand','version','displacement','plate'], 'post');
        $this ->validate->scene('bindPlate', ['uid','carImg','brand','version','displacement']);
        $result = $this ->validate->scene('bindPlate')->check($param);
        if (true !== $result) {
            return success(1001, '', $this ->validate->getError());
        }

        if (!TokenService::checkUserId($param['uid'])) {
            return success(1002, '', '无效token');
        }

        $findRes  = DB::table('pk_cars')->where('uid', $param['uid'])->find();
        if ($findRes['type'] == 3 ) {
            return success(1004, '', '您提交的车牌正在审核中,请勿重复提交');
        }


        $userInfo = DB::table('pk_user')->where('uid', $param['uid'])->find();
        $param['phone'] = $userInfo['phone'];
        DB::table('pk_user')->where('uid', $param['uid'])->setField('plate', $param['plate']);

        $carImg = getBase64Image($param['carImg']);
        unset($param['carImg']);
        if ($carImg === false) {
            return success(1003, '', 'base64转换失败');
        }
        $param['logo'] = implode(',,,', array_slice($carImg, 0, 2));
        $param['dlogo'] = implode(',,,', array_slice($carImg, 2, 2));


        $param['type'] = 3;
        $param['create_time'] = time();
        $param['update_time'] = time();
        if (empty($findRes)) {
            DB::table('pk_cars')->insert($param);
        } else {
            DB::table('pk_cars')->where('uid', $param['uid'])->update($param);
        }

        return success(1000, '', '申请已提交');
    }
    public function getCar(){
        $param = request()->only(['uid']);
        $this ->validate->scene('getPlate', ['uid']);
        $result = $this ->validate->scene('getPlate')->check($param);
        if (true !== $result) {
            return success(1001, '', $this ->validate->getError());
        }

        if (!TokenService::checkUserId($param['uid'])) {
            return success(1002, '', '无效token');
        }

        $result  = DB::table('pk_cars')->where('uid', $param['uid'])->find();
        if (empty($result)) {
            return success(1000, '', '获取车辆信息成功');
        } else {
            $imgStr = $result['logo'].',,,'.$result['dlogo'];
            $data['carImg'] = getImage(explode(',,,', $imgStr));
            $data['brand'] = $result['brand'];
            $data['version'] = $result['version'];
            $data['type'] = $result['type'];
            $data['displacement'] = $result['displacement'];
            $data['plate'] = $result['plate'];

            return success(1000, $data, '获取车辆信息成功');


        }

    }

    //消息
    public function message(){
        $uid = input('uid/d',0);
        $res=Db::table('pk_message')->where('uid',$uid)->order('createtime desc')->select();
        return json_encode(['code'=>1000,'msg'=>$res]);
    }

    //测试数据
    public function test(){
        $mg_id = input('mg_id');
        $uid = input('uid');
        $number = input('number');//车位号

        //首先判断这个车位是不是该用户拥有的
        $where1=['mg_id'=>$mg_id,'owner'=>2,'uid'=>$uid,'uuid'=>0,'types'=>1];//此时是个人车位并且没被出租
        $where2=['mg_id'=>$mg_id,'uuid'=>$uid,'uuuid'=>0,'types'=>2,'status'=>1];//此时表示该用户租用了出租车位
        $where3=['mg_id'=>$mg_id,'uuuid'=>$uid,'types'=>3,'status'=>1];//此时表示该用户租用了别人的共享车位
        $where4=['mg_id'=>$mg_id,'uuid'=>$uid,'types'=>3,'charge1'=>null,'status'=>1];//表示租用了个人车位直接共享的车位
        $data1=DB::table('pk_carpos')->where($where1)->field('number')->select();
        $data2=DB::table('pk_carpos')->where($where2)->field('number')->select();
        $data3=DB::table('pk_carpos')->where($where3)->field('number')->select();
        $data4=DB::table('pk_carpos')->where($where4)->field('number')->select();
        dump($data1);
        dump($data2);
        dump($data3);
        dump($data4);
    }

    //点击停车位
    public function clickpos(){
        $mg_id=input('mg_id');
        $uid=input('uid');
        $number=input('number');//车位号
        if (!TokenService::checkUserId($uid)) {
            return success(1002, '', '无效token');
        }
        //首先判断这个车位是不是该用户拥有的
        $where1=['mg_id'=>$mg_id,'owner'=>2,'uid'=>$uid,'uuid'=>0,'types'=>1];//此时是个人车位并且没被出租
        $where2=['mg_id'=>$mg_id,'uuid'=>$uid,'uuuid'=>0,'types'=>2,'status'=>1];//此时表示该用户租用了出租车位
        $where3=['mg_id'=>$mg_id,'uuuid'=>$uid,'types'=>3,'status'=>1];//此时表示该用户租用了别人的共享车位
        $where4=['mg_id'=>$mg_id,'uuid'=>$uid,'types'=>3,'charge1'=>null,'status'=>1];//表示租用了个人车位直接共享的车位
        $data1=DB::table('pk_carpos')->where($where1)->field('number')->select();
        $data2=DB::table('pk_carpos')->where($where2)->field('number')->select();
        $data3=DB::table('pk_carpos')->where($where3)->field('number')->select();
        $data4=DB::table('pk_carpos')->where($where4)->field('number')->select();
        $shuju=array_merge($data1,$data2,$data3,$data4);
        $shuju=array_column($shuju,'number');
        if(in_array($number,$shuju)){
            $arr['flag']=2;//表示可以直接停车，是否产生订单要判断

            $time = time();
            //根据停车场id和车位号来定位车位表中的一条数据
            $info=Db::table('pk_carpos')->where(['mg_id'=>$mg_id,'number'=>$number])->find();

            if(in_array($number,$data2)){
                //此时表示是租用车位
                if($info['start_time'] - $time > 0 ){
                    //此时就要产生订单
                }else{
                    //可以直接停车
                }
            }else if(in_array($number,$data3)){
                //此时表示租用之后在共享的车位
                if($info['start_time1'] - $time > 0 ){
                    //此时就要产生订单
                }else{
                    //可以直接停车
                }

            }else if(in_array($number,$data4)){
                //此时表示租用了别人直接共享的车位
                if($info['start_time'] - $time > 0 ){
                    //此时就要产生订单
                }else{
                    //可以直接停车
                }
            }

            return json_encode(['code'=>1001,'data'=>$arr,'msg'=>'该车位是该用户所拥有车位，可直接停车']);
        }else{
            //这时表示要产生订单
            $data=Db::table('pk_manager')->where(['mg_id'=>$mg_id])->field('charge')->find();
            $data['number']=$number;
            $data['flag']=1;
            return json_encode(['code'=>1000,'data'=>$data,'msg'=>'停车位收费信息获取成功']);
        }
    }

    //设备检测到车牌
    public function recplate($type=1,$chanum=null){
        if($type==1){
            $doc = file_get_contents("php://input");
            $jsondecode = json_decode($doc,true);//加上true参数表示解析为数组
            if($jsondecode == null){
                return;
            }
            $license = $jsondecode['AlarmInfoPlate']['result']['PlateResult']['license'];//这里可以获取到车牌号
            $channelnum = $jsondecode['AlarmInfoPlate']['channel'];//这里可以获取开闸的端口号
            $serialno = $jsondecode['AlarmInfoPlate']['serialno']; //这里可以获取设备序列号
            $imagePath = $jsondecode['AlarmInfoPlate']['result']['PlateResult']['imagePath'];//图片地址

            $devicedata = Db::table('pk_device')->where(['serialno'=>$serialno])->find();

            if($devicedata['type']==1){
                //此时表示进口
                //根据设备序列号可以知道车进入哪个停车场
                $mginfo = Db::table('pk_manager')->where(['mg_id'=>$devicedata['mg_id']])->find();
                //根据车牌号可以知道用户信息
                $uinfo = Db::table('pk_user')->where(['plate'=>$license])->field('uid')->find();

                //首先判断该用户在该停车场下有没有车位存在
                $where1=['mg_id'=>$mginfo['mg_id'],'owner'=>2,'uid'=>$uinfo['uid'],'uuid'=>0,'types'=>1];//此时是个人车位并且没被出租
                $where2=['mg_id'=>$mginfo['mg_id'],'uuid'=>$uinfo['uid'],'uuuid'=>0,'types'=>2,'status'=>1];//此时表示该用户租用了出租车位
                $where3=['mg_id'=>$mginfo['mg_id'],'uuuid'=>$uinfo['uid'],'types'=>3,'status'=>1];//此时表示该用户租用了别人的共享车位
                $where4=['mg_id'=>$mginfo['mg_id'],'uuid'=>$uinfo['uid'],'types'=>3,'charge1'=>null,'status'=>1];//表示租用了个人车位直接共享的车位

                $data1=DB::table('pk_carpos')->where($where1)->field('number')->select();
                $data2=DB::table('pk_carpos')->where($where2)->field('number')->select();
                $data3=DB::table('pk_carpos')->where($where3)->field('number')->select();
                $data4=DB::table('pk_carpos')->where($where4)->field('number')->select();

                if(!empty($data1)){
                    //产生一条停车记录
                    $shuju['in_time'] =time();
                    $shuju['mg_id']   =$mginfo['mg_id'];
                    $shuju['uid']     =$uinfo['uid'];
                    $shuju['plate']   =$license;
                    $shuju['in_photo']=$imagePath;
                    $shuju['status']=1;
                    $pid = Db::table('pk_stopcarrecord')->insertGetId($shuju);
                    echo '{"Response_AlarmInfoPlate":{"info":"ok","channelNum":'.$channelnum .',"content":"个人车","is_pay":"true"}}';
                }else if(!empty($data2)){
                    //产生一条停车记录
                    $shuju['in_time'] =time();
                    $shuju['mg_id']   =$mginfo['mg_id'];
                    $shuju['uid']     =$uinfo['uid'];
                    $shuju['plate']   =$license;
                    $shuju['in_photo']=$imagePath;
                    $shuju['status']=2;
                    $pid = Db::table('pk_stopcarrecord')->insertGetId($shuju);
                    echo '{"Response_AlarmInfoPlate":{"info":"ok","channelNum":'.$channelnum .',"content":"专属车","is_pay":"true"}}';
                }else if(!empty($data3) || !empty($data4)){
                    //产生一条停车记录
                    $shuju['in_time'] =time();
                    $shuju['mg_id']   =$mginfo['mg_id'];
                    $shuju['uid']     =$uinfo['uid'];
                    $shuju['plate']   =$license;
                    $shuju['in_photo']=$imagePath;
                    $shuju['status']=3;
                    $pid = Db::table('pk_stopcarrecord')->insertGetId($shuju);
                    echo '{"Response_AlarmInfoPlate":{"info":"ok","channelNum":'.$channelnum .',"content":"共享车","is_pay":"true"}}';
                }else{

                    $res = Db::table('pk_manager')->where(['serialno'=>$serialno])->setDec('leftpos');
                    if($mginfo['leftpos']<=0){
                        echo '{"Response_AlarmInfoPlate":{"info":"false","channelNum":'.$channelnum .',"content":"没有空余车位","is_pay":"true"}}';
                    }else{
                        //产生一条停车记录
                        $shuju['in_time'] =time();
                        $shuju['mg_id']   =$mginfo['mg_id'];
                        $shuju['uid']     =$uinfo['uid'];
                        $shuju['plate']   =$license;
                        $shuju['in_photo']=$imagePath;
                        $shuju['status']  =4;
                        $pid = Db::table('pk_stopcarrecord')->insertGetId($shuju);

                        //产生订单
                        $info['order_id'] = date('YmdHis') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
                        $info['uid'] = $uinfo['uid'];
                        $info['sid'] = $pid;
                        $info['mg_id'] = $mginfo['mg_id'];
                        $ress=Db::table('pk_stopcarorder')->insert($info);

                        echo '{"Response_AlarmInfoPlate":{"info":"ok","channelNum":'.$channelnum .',"content":"临时车","is_pay":"true"}}';
                    }
                }

            }else if($devicedata['type']==2){
                //此时表示出口
                //根据设备序列号可以知道车进入哪个停车场
                $mginfo = Db::table('pk_manager')->where(['mg_id'=>$devicedata['mg_id']])->find();
                //根据车牌号可以知道用户信息
                $uinfo = Db::table('pk_user')->where(['plate'=>$license])->field('uid')->find();

                $stopinfo = Db::table('pk_stopcarrecord')->where(['uid'=>$uinfo['uid']])->order('id desc')->limit(1)->find();
                $shuju['out_time'] = time();
                $res=Db::table('pk_stopcarrecord')->where(['uid'=>$uinfo['uid']])->order('id desc')->limit(1)->update($shuju);
                if($stopinfo['status']==1){
                    echo '{"Response_AlarmInfoPlate":{"info":"ok","channelNum":'.$channelnum .',"content":"个人车","is_pay":"true"}}';
                }else if($stopinfo['status']==2){
                    echo '{"Response_AlarmInfoPlate":{"info":"ok","channelNum":'.$channelnum .',"content":"专属车","is_pay":"true"}}';
                }else if($stopinfo['status']==3){
                    echo '{"Response_AlarmInfoPlate":{"info":"ok","channelNum":'.$channelnum .',"content":"共享车","is_pay":"true"}}';
                }else{
                    $shuju['timelong'] = $shuju['out_time']-$stopinfo['in_time'];
                    $price = $mginfo['charge']*(ceil($shuju['timelong']/3600));
                     echo '{"Response_AlarmInfoPlate":{"info":"ok","channelNum":'.$channelnum .',"content":"临时车,共需付费'.$price.'","is_pay":"true"}}';
                }
            }
        }else{
            echo '{"Response_AlarmInfoPlate":{"info":"ok","channelNum":'.$chanum .',"manualTigger":"ok","content":"手动开闸","is_pay":"true"}}';
        }
    }
    //进入停车场，调用接口,将这儿的地址配置到一体机里,车辆进入停车场要将停车场的剩余车位数量减一
    public function enterpos(){
        $doc = file_get_contents("php://input");
        $jsondecode = json_decode($doc,true);//加上true参数表示解析为数组
        if($jsondecode == null){
            return;
        }
        $license = $jsondecode['AlarmInfoPlate']['result']['PlateResult']['license'];//这里可以获取到车牌号
        $channelnum = $jsondecode['AlarmInfoPlate']['channel'];//这里可以获取开闸的端口号
        $serialno = $jsondecode['AlarmInfoPlate']['serialno']; //这里可以获取设备序列号
        //根据设备序列号可以知道车进入哪个停车场
        $mginfo = Db::table('pk_manager')->where(['serialno'=>$serialno])->find();
        //根据车牌号可以知道用户信息
        $uinfo = Db::table('pk_user')->where(['plate'=>$license])->field('uid')->find();

        //首先判断该用户在该停车场下有没有车位存在
        $where1=['mg_id'=>$mginfo['mg_id'],'owner'=>2,'uid'=>$uinfo['uid'],'uuid'=>0,'types'=>1];//此时是个人车位并且没被出租
        $where2=['mg_id'=>$mginfo['mg_id'],'uuid'=>$uinfo['uid'],'uuuid'=>0,'types'=>2,'status'=>1];//此时表示该用户租用了出租车位
        $where3=['mg_id'=>$mginfo['mg_id'],'uuuid'=>$uinfo['uid'],'types'=>3,'status'=>1];//此时表示该用户租用了别人的共享车位
        $where4=['mg_id'=>$mginfo['mg_id'],'uuid'=>$uinfo['uid'],'types'=>3,'charge1'=>null,'status'=>1];//表示租用了个人车位直接共享的车位

        $data1=DB::table('pk_carpos')->where($where1)->field('number')->select();
        $data2=DB::table('pk_carpos')->where($where2)->field('number')->select();
        $data3=DB::table('pk_carpos')->where($where3)->field('number')->select();
        $data4=DB::table('pk_carpos')->where($where4)->field('number')->select();

        $res = Db::table('pk_manager')->where(['serialno'=>$serialno])->setDec('leftpos');
        if(!empty($data1)){
            //产生一条停车记录
            $shuju['in_time'] =time();
            $shuju['mg_id']   =$mginfo['mg_id'];
            $shuju['uid']     =$uinfo['uid'];
            $shuju['plate']   =$license;
            $pid = Db::table('pk_stopcarrecord')->insertGetId($shuju);
            echo '{"Response_AlarmInfoPlate":{"info":"ok","channelNum":'.$channelnum .',"content":"个人车","is_pay":"true"}}';
        }else if(!empty($data2)){
            //产生一条停车记录
            $shuju['in_time'] =time();
            $shuju['mg_id']   =$mginfo['mg_id'];
            $shuju['uid']     =$uinfo['uid'];
            $shuju['plate']   =$license;
            $pid = Db::table('pk_stopcarrecord')->insertGetId($shuju);
            echo '{"Response_AlarmInfoPlate":{"info":"ok","channelNum":'.$channelnum .',"content":"专属车","is_pay":"true"}}';
        }else if(!empty($data3) || !empty($data4)){
            //产生一条停车记录
            $shuju['in_time'] =time();
            $shuju['mg_id']   =$mginfo['mg_id'];
            $shuju['uid']     =$uinfo['uid'];
            $shuju['plate']   =$license;
            $pid = Db::table('pk_stopcarrecord')->insertGetId($shuju);
            echo '{"Response_AlarmInfoPlate":{"info":"ok","channelNum":'.$channelnum .',"content":"共享车","is_pay":"true"}}';
        }else{
            //产生一条停车记录
            $shuju['in_time'] =time();
            $shuju['mg_id']   =$mginfo['mg_id'];
            $shuju['uid']     =$uinfo['uid'];
            $shuju['plate']   =$license;
            $pid = Db::table('pk_stopcarrecord')->insertGetId($shuju);
            //产生订单
            $info['order_id'] = date('YmdHis') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            $info['uid'] = $uinfo['uid'];
            $info['sid'] = $pid;
            $info['mg_id'] = $mginfo['mg_id'];
            $ress=Db::table('pk_stopcarorder')->insert($info);
            echo '{"Response_AlarmInfoPlate":{"info":"ok","channelNum":'.$channelnum .',"content":"临时车","is_pay":"true"}}';
        }
        //产生一条停车记录
        $shuju['in_time'] =time();
        $shuju['mg_id']   =$mginfo['mg_id'];
        $shuju['uid']     =$uinfo['uid'];
        $shuju['plate']   =$license;
        $pid = Db::table('pk_stopcarrecord')->insertGetId($shuju);
        //是否需要产生订单
        $info['order_id'] = date('YmdHis') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        $info['uid'] = $uinfo['uid'];
        $info['sid'] = $pid;
        $info['mg_id'] = $mginfo['mg_id'];
        $ress=Db::table('pk_stopcarorder')->insert($info);



        $res = Db::table('pk_manager')->where(['serialno'=>$serialno])->setDec('leftpos');

        $data=Db::table('pk_user')->where(['plate'=>$license])->find();
        if(empty($data)){
            //此时表示该用户没在app绑定车牌

        }else{
            //此时表示该用户已在app绑定了车牌，这里是否可以直接进入停车场
            // 如果可以,发送开闸命令
            echo '{"Response_AlarmInfoPlate":{"info":"ok","channelNum":'.$channelnum .',"content":"...","is_pay":"true"}}';
        }
        $data['in_time']=time();//进入时间
        $flag=input('flag');
        $data['uid']=input('uid');
        $data['mg_id']=input('mg_id');
        $data['number']=input('number');
        $info['type']=3;//表示停放车中
        $res2=Db::table('pk_carpos')->where(['mg_id'=>$mg_id,'number'=>$number])->update($info);
        if($flag==1){
            //此时表示要产生订单
            $data['status']=1;
            $res1=Db::table('pk_stopcarrecord')->insertGetId($data);
            $shuju['order_id'] = date('YmdHis') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            $shuju['uid']=$data['uid'];
            $shuju['dateline']=time();
            $shuju['mg_id']=$data['mg_id'];
            $res3=DB::table('pk_stopcarorder')->insertGetId($shuju);
        }else{
            //此时表示不需要产生订单,但是会产生一条停车记录
            $res1=Db::table('pk_stopcarrecord')->insertGetId($data);
        }
        return json_encode(['code'=>1000,'data'=>$data,'oid'=>$res3,'sid'=>$res1,'msg'=>'车辆成功进入停车场']);
    }

    //离开停车场,调用接口，车辆离开停车场，要将停车场的剩余车位数加一
    public function leavepos(){
        $doc = file_get_contents("php://input");
        $jsondecode = json_decode($doc,true);//加上true参数表示解析为数组
        if($jsondecode == null){
            return;
        }
        $license = $jsondecode['AlarmInfoPlate']['result']['PlateResult']['license'];//这里可以获取到车牌号
        //根据车牌号可以获取到用户的信息
        $channelnum = $jsondecode['AlarmInfoPlate']['channel'];//这里可以获取开闸的端口号
        $serialno = $jsondecode['AlarmInfoPlate']['serialno']; //这里可以获取设备序列号


        //根据设备序列号可以知道车进入那个停车场
        $mginfo = Db::table('pk_manager')->where(['serialno'=>$serialno])->find();
        //根据车牌号可以知道用户信息
        $uinfo = Db::table('pk_user')->where(['plate'=>$license])->field('uid')->find();

        $shuju['out_time']=time();
        $in_time = Db::table('pk_stopcarrecord')->where(['mg_id'=>$mginfo['mg_id'],'uid'=>$uinfo['uid']])->order('id desc')->limit(1)->value('in_time');
        $shuju['timelong']=$shuju['out_time']-$in_time;
        $res = Db::table('pk_stopcarrecord')->where(['mg_id'=>$mginfo['mg_id'],'uid'=>$uinfo['uid']])->order('id desc')->limit(1)->update($shuju);




        $res = Db::table('pk_manager')->where(['serialno'=>$serialno])->setInc('leftpos');

        $data=Db::table('pk_user')->where(['plate'=>$license])->find();
        if(empty($data)){
            //此时表示该用户没在app绑定车牌

        }else{
            //此时表示该用户已在app绑定了车牌，这里是否可以直接进入停车场,如果可以
            // 发送开闸命令
            echo '{"Response_AlarmInfoPlate":{"info":"ok","channelNum":'.$channelnum.',"content":"...","is_pay":"true"}}';
        }






        $sid=input('sid');
        $data['out_time']=time();
        $data['plate']=input('plate');//车牌号
        $data['number']=input('number');
        $in_time=Db::table('pk_stopcarrecord')->where(['id'=>$sid])->value('in_time');
        $data['timelong']=$data['out_time']-$in_time;
        $res=Db::table('pk_stopcarrecord')->where(['id'=>$sid])->update($data);
        //更新停车位信息
        $info['type']=4;//表示停车位空置中
        $ress=Db::table('pk_carpos')->where(['mg_id'=>$mg_id,'number'=>$number])->update($info);
        if(!empty($res)){
            return json_encode(['code'=>1000,'msg'=>'离开停车场成功']);
        }else{
            return json_encode(['code'=>1001,'msg'=>'离开停车场失败']);
        }
    }


    //支付展示页面
    public function pay(){
        $uid=input('uid');
        if (!TokenService::checkUserId($uid)) {
            return success(1002, '', '无效token');
        }
        $order_id=input('order_id');
        $sid=Db::table('pk_stopcarorder')->where(['order_id'=>$order_id])->value('sid');
        $flag=DB::table('pk_stopcarrecord')->where(['id'=>$sid])->find();
        $data=Db::table('pk_stopcarrecord')->alias('s')
        ->join('__MANAGER__ m','m.mg_id=s.mg_id')
        ->where(['s.id'=>$sid])
        ->field('m.name,m.logo,m.charge,s.timelong')
        ->find();
        $data['logo']=substr($data['logo'],1);
        $timelong=ceil($data['timelong']/3600);
        $data['timelong']=time_difference($data['timelong']);
        $data['charge']=$data['charge']*$timelong;
        $data['count']=Db::table('pk_stopcarrecord')->where(['id'=>$sid])->count('id');
        $shuju['price']=$data['charge'];
        $shuju['timelong']=$timelong;
        $res=Db::table('pk_stopcarorder')->where(['order_id'=>$order_id])->update($shuju);
        if(!empty($data)){
            return json_encode(['code'=>1000,'data'=>$data,'msg'=>'首页支付页面获取成功']);
        }else{
            return json_encode(['code'=>1001,'msg'=>'首页支付页面获取失败']);
        }
    }

    //点击支付
    public function clickpay(){
        $order_id=input('order_id');
        $type=input('type');
        if($type==1){
            //此时表示零钱支付
            $smallmoney=new IndexsmoneyController;
            $smallmoney->smallpay($order_id);
        }else if($type==2){
            //此时表示支付宝支付
            $indexalipay=new IndexalipayController;
            $indexalipay->alipay_pay($order_id);
        }else if($type==3){
            //此时表示微信支付
            $indexwxpay=new IndexwxpayController;
            $indexwxpay->wx_pay($order_id);
        }
    }




    

    //支付成功后要做的操作
    public function payaccomplish(){
        $uid=input('uid');
        $info['account']=input('account');
        $info['duration']=time();
        $info['reason']=3;
        $info['timelong']=input('timelong');
        $info['pay']=input('pay');//1：支付宝2：微信3：零钱
        $info['type']=1;
        $info['oid']=input('mg_id');
        $info['mg_id']=input('mg_id');
        $shuju['uid']=$uid;
        $data['pay']=1;
        //查出停车场的id以及车位号就可以定位到车位表中的值
        $flag=Db::table('pk_stopcarrecord')->where(['uid'=>$uid])->order('id desc')->limit(1)->field('mg_id,number')->find();
        //$shuju=Db::table('pk_carpos')->where(['mg_id'=>$flag['mg_id'],'number'=>$flag['number']])->field('owner')->find();

        $res2=Db::table('pk_manager')->where(['mg_id'=>$flag['mg_id']])->setInc('moneybag',$info['account']);
        $res1=DB::table('pk_revenue')->insert($info);
        //$res=DB::table('pk_stopcarrecord')->where(['uid'=>$uid])->order('id desc')->limit(1)->update($data);
        if(!empty($res)){
            return json_encode(['code'=>1000,'msg'=>'支付成功']);
        }else{
            return json_encode(['code'=>1001,'msg'=>'支付失败']);
        }
    }
}
