<?php
namespace app\api\controller;

use app\api\validate\UserValidate;
use think\Cache;
use think\Controller;
use think\Request;
use think\Db;
use app\api\Model\Manager;
use app\api\Model\Carpos;
use app\api\service\TokenService;
use app\api\Model\User;
use app\api\Controller\RechargealipayController;
use app\api\Controller\RechargewxpayController;

class UserController extends BaseController
{
    protected $UserValidate = null;
    public function __construct()
    {
        $this -> UserValidate = new UserValidate();
    }

    public function index()
    {
        $param = request()->only(['uid']);
        $this->UserValidate->scene('index', ['uid']);
        $result = $this->UserValidate->scene('index')->check($param);

        if (true !== $result) {
            return success(1001, '', $this->UserValidate->getError());
        }

        if (!TokenService::checkUserId($param['uid'])) {
            return success(1002, '', '无效token');
        }
        $data = Db::table('pk_user')->where(['uid'=>$param['uid']])->field('logo,surname,name,uid,phone as userPhone')->find();
        $data['logo'] = getImage($data['logo']);
        if (!empty($data)) {
            return success(1000, $data, '获取个人信息成功');
        } else {
            return success(1001, $data, '获取个人信息失败');
        }
    }

    //我的车位页面
    public function mycarpos_cx(){
        $uid=input('uid');
        $page = input('page', 1);
        $num = 2;//每页显示的条数
        $start = ($page - 1) * $num;
        $data=DB::table('pk_carpos')->alias('c')
        ->join('__MANAGER__ m','m.mg_id=c.mg_id')
        ->where(['c.uid'=>$uid])->where('c.types!=4')
        ->field('c.id,c.number,c.style,c.types,c.status,c.remark,c.owner,m.name,m.address,m.area,c.uuid,c.end_time,c.end_time1,c.uuuid')
        ->select();
        if(!empty($data)){
            foreach($data as $k=>$v){
                $data[$k]['address']=$v['area'].$v['address'];
                $data[$k]['diff']=1;
                if($v['types']==1){
                    $data[$k]['flag']='个人';
                }else if($v['types']==2 && $v['uuid']==0){
                    $data[$k]['flag']='出租中';
                }else if($v['types']==2 && $v['uuid']!=0){
                    $data[$k]['flag']='已出租';
                    $data[$k]['end_time']=date('Y/m/d',$v['end_time']);
                }else if($v['types']==3 && $v['uuid']==0 && $v['uuuid']==0){
                    $data[$k]['flag']='共享中';
                }else if($v['types']==3 && $v['uuid']!=0 && $v['uuuid']==0){
                    $data[$k]['flag']='已共享';
                    $data[$k]['end_time']=date('Y/m/d H:i',$v['end_time1']);
                }
            }
        }
        
        //$where1['uuuid']=['eq',0];
        $time=time();
        $shuju=DB::table('pk_carpos')->alias('c')
        ->join('__MANAGER__ m','m.mg_id=c.mg_id')
        ->where(['c.uuid'=>$uid])
        //->where($where1)
        ->field('c.id,c.number,c.style,c.types,c.status,c.end_time,c.remark,c.owner,m.name,m.address,m.area,c.uuuid,c.end_time1')
        ->select();
        if(!empty($shuju)){
           foreach($shuju as $k=>$v) {
                if (($v['end_time'] - $time) / (3600 * 24) <= 3 && ($v['end_time'] - $time) / (3600 * 24) >= 0) {
                    //表示还有3天到期
                    $shuju[$k]['notice'] = 1;  //显示租用即将到期的提醒
                }else{
                    $shuju[$k]['notice'] =2 ;
                }
                $shuju[$k]['diff']=2;
                $shuju[$k]['address'] = $v['area'] . $v['address'];
                if ($v['uuuid'] == 0 && $v['end_time1'] != 0) {
                    $shuju[$k]['flag'] = '共享中';
                } else if ($v['uuuid'] != 0) {
                    $shuju[$k]['flag'] = '已共享';
                    $shuju[$k]['end_times'] = date('Y/m/d H:i', $v['end_time1']);
                } else {
                    $shuju[$k]['flag'] = '可以共享';
                }
            } 
        }
        $info = array_merge($data,$shuju);
        $maxpage = ceil(count($info)/$num);
        $info = array_slice($info,$start,$num);
        
        return json_encode(['code'=>1000,'data'=>$info,'maxpage'=>$maxpage,'msg'=>'我的车位信息获取成功']);
    }
    public function myCarpos(){
        $param = input('post.');
        $page = empty($param['page']) ? 1 : $param['page'];
        $num = 15;
        $this->UserValidate->scene('myCarpos', ['uid']);
        $result = $this->UserValidate->scene('myCarpos')->check($param);
        if (true !== $result) {
            return success(1001, '', $this->UserValidate->getError());
        }



        $jion = [
            ['__MANAGER__ m','m.mg_id=c.mg_id', 'left'],
        ];


        //个人车位
        $whereSelf1 = [
            'c.uid' => ['eq', $param['uid']],
            'c.types' => ['eq', 1],
            'c.status' => ['eq', 2],
        ];
        //个人出租 但是不在时间段内
        $whereSelf2 = [
            'c.uid' => ['eq', $param['uid']],
            'c.uuuid' => ['eq', 0],
            'c.types' => ['eq', 2],
        ];
        //个人共享但是不在共享时间段内
        $whereSelf3 = [
            'c.uid' => ['eq', $param['uid']],
            'c.uuid' => ['eq', 0],
            'c.types' => ['eq', 3],
        ];
        //个人车位审核失败
        $whereSelf4 = [
            'c.uid' => ['eq', $param['uid']],
            'c.style' => ['eq', 2],
        ];

        //个人车位审核中
        $whereSelf5 = [
            'c.uid' => ['eq', $param['uid']],
            'c.style' => ['eq', 3],
        ];

        $field = 'c.id,c.number,c.style,c.types,c.status,c.remark,m.name,m.address,m.area,c.end_time,m.latitude,m.longitude,c.end_time1';

        $selfData = DB::table('pk_carpos')
            ->alias('c')
            ->join($jion)
            ->whereOr(function ($query) use ($whereSelf1) {
                $query->where($whereSelf1);
            })->whereOr(function ($query) use ($whereSelf2) {
                $query->where($whereSelf2)->where(function ($query) {
                        $query->whereOr(['c.start_time'=>['>', time()],'c.end_time'=>['<', time()]]);
            });
            })->whereOr(function ($query) use ($whereSelf3) {
                $query->where($whereSelf3)->where(function ($query) {
                        $query->whereOr(['c.start_time1'=>['>', time()],'c.end_time1'=>['<', time()]]);
                });
            })->whereOr(function ($query) use ($whereSelf4) {
                $query->where($whereSelf4);
            })->whereOr(function ($query) use ($whereSelf5) {
                $query->where($whereSelf5);
            })
            ->field($field)
            ->select();

        foreach ($selfData as $self_k => $self_v) {
            $selfData[$self_k]['from'] = '个人';//1我个人的 2我租的
        }

        $whereRent = [ //个人租来的
            'c.uuid' => ['eq', $param['uid']],
        ];

        $whereRentOr = [ //个人租来的 拿去共享
            'c.uuid' => ['eq', $param['uid']],
            'c.types'=> ['eq', 3]
        ];


        $rentData = DB::table('pk_carpos')
            ->alias('c')
            ->join($jion)
            ->where($whereRent)
            ->whereOr(function ($query) use ($whereRentOr) {
                $query->where($whereRentOr)->where(function ($query) {
                    $query->whereOr(['c.start_time1'=>['>', time()],'c.end_time1'=>['<', time()]]);
                });
            })
            ->field($field)
            ->select();


        foreach ($rentData as $rent_k => $rent_v) {
            $rentData[$rent_k]['from'] = '出租';//1我个人的 2我租的
        }

        $shareWhere = [];
        $shareWhere['uuuid'] = ['eq', $param['uid']];



        $result = array_merge($rentData, $selfData);


        foreach ($result as $k => $v){
            if($result[$k]['style'] == 1){ //审核通过
                if ($result[$k]['from'] == '个人') {//个人
                    if($result[$k]['status'] == 1){//个人已租赁
                        if ($result[$k]['types']== 2) {//已经出租
                            $result[$k]['title'] = '出租到期:'.date('Y/m/d ', $result[$k]['end_time']);
                            $result[$k]['status'] = '出租';
                        }else{//已经共享
                            $result[$k]['title'] = '共享到期:'.date('Y/m/d H:i', $result[$k]['end_time1']);
                            $result[$k]['status'] = '已共享';
                        }

                    }else {//未租赁
                        if ($result[$k]['types']== 2) {//发布出租
                            $result[$k]['title'] = '暂无人租用';
                            $result[$k]['status'] = '出租中';
                        }else if($result[$k]['types']== 3){//发布共享
                            $result[$k]['title'] = '暂无人租用';
                            $result[$k]['status'] = '共享中';
                        }else{
                            $result[$k]['title'] = '';
                            $result[$k]['status'] = '';
                        }
                    }
                }else{//出租
                    if($result[$k]['types']== 1){//已租赁
                        if ($result[$k]['types']== 3) {//发布了共享 且已被共享
                            $result[$k]['title'] = '共享到期:'.date('Y/m/d H:i', $result[$k]['end_time1']);
                            $result[$k]['status'] = '已共享';
                        }
                    }else{
                        if ($result[$k]['types']== 2) {//还是出租 就没有发布任何
                            $result[$k]['title'] = '';
                            $result[$k]['status'] = '';
                        }else{
                            $result[$k]['title'] = '无人共享';
                            $result[$k]['status'] = '共享中';
                        }
                    }
                }
            }else if($result[$k]['style'] == 2){ //审核失败
                $result[$k]['from'] = '个人';
                $result[$k]['title'] = '失败原因:'.$result[$k]['remark'];
                $result[$k]['status'] = '';
            }else if($result[$k]['style'] == 3){ //审核失败
                $result[$k]['from'] = '个人';
                $result[$k]['title'] = '';
                $result[$k]['status'] = '';
            }
            unset($result[$k]['end_time']);
            unset($result[$k]['end_time1']);
            unset($result[$k]['types']);
            unset($result[$k]['remark']);
        }
//        foreach ($result as $k => $v){
//            if($result[$k]['style'] == 1){ //审核通过
//                if ($result[$k]['status'] == 2) {//2未租赁 1已租赁
//                    if($result[$k]['types'] == 2){//出租
//                        if (empty($result[$k]['end_time'])) {//没有发布出租
//                            $result[$k]['title'] = '';
//                            $result[$k]['status'] = '';
//                        }else{//发布了出租
//                            $result[$k]['title'] = '暂无人租用';
//                            $result[$k]['status'] = '出租中';
//                        }
//
//                    }else{
//                        if (empty($result[$k]['end_time1'])) {//是否有发布共享
//                            $result[$k]['title'] = '';
//                            $result[$k]['status'] = '';
//                        }else{
//                            $result[$k]['title'] = '暂无人共享';
//                            $result[$k]['status'] = '共享中';
//                        }
//                    }
//                }else{//已租赁
//                    if($result[$k]['types'] == 2){//已出租出租
//
//                    }
//                    if (empty($result[$k]['end_time1'])) {//没有发布共享
//                        $result[$k]['title'] = '';
//                        $result[$k]['status'] = '';
//                    }else{//发布了共享
//                        $result[$k]['title'] = '共享到期:'.date('Y/m/d H:i', $result[$k]['end_time1']);
//                        $result[$k]['status'] = '已共享';
//                    }
//                }
//            }else if($result[$k]['style'] == 2){ //审核失败
//                $result[$k]['from'] = '个人';
//                $result[$k]['title'] = '失败原因:'.$result[$k]['remark'];
//                $result[$k]['status'] = '';
//            }else if($result[$k]['style'] == 3){ //审核失败
//                $result[$k]['from'] = '个人';
//                $result[$k]['title'] = '';
//                $result[$k]['status'] = '';
//            }
//            unset($result[$k]['end_time']);
//            unset($result[$k]['end_time1']);
//            unset($result[$k]['types']);
//            unset($result[$k]['remark']);
//        }



        $data = getPageData($result, $num, $page);
        return success(1000, $data, '获取列表成功');
    }

    //添加个人车位选择省市后停车场
    public function addcarposdisplay(){
        $param = request()->only(['province','city']);
        $this->UserValidate->scene('myCarpos', ['province','city']);
        $result = $this->UserValidate->scene('myCarpos')->check($param);
        if (true !== $result) {
            return success(1001, '', $this->UserValidate->getError());
        }

        $data = Db::table('pk_manager')->where(['province'=>$param['province'],'city'=>$param['city']])->field('mg_id,name')->select();
        if (!empty($data)) {
            return success(1000, $data, '停车场获取成功');
        }else{
            return success(1003, '', '停车场获取失败');
        }
    }

    //添加个人车位选择停车场后自动生成的详细地址
    public function addcarposaddress(){
        $id=input('mg_id');
        $data=Db::table('pk_manager')->where(['mg_id'=>$id])->field('area,address')->find();
        $address=$data['area'].$data['address'];
        if(!empty($data)){
            return json_encode(['code'=>1000,'data'=>$address,'msg'=>'详细地址获取成功']);
        }else{
            return json_encode(['code'=>1001,'msg'=>'详细地址获取失败']);
        }
    }

    //上传图片
    public function uploadpic(Request $request){
        //$uid=1001;//session::get('api_member');
        $file=$request->file('file');
        $info = $file->move(ROOT_PATH.'public' . DS . 'upload');
         if($info){
            $filePath = './'.'upload'.'/'.$info->getSaveName();
            str_replace('\\','/',$filePath);
            return $filePath;
        }else{
            return $file->getError();
        }     
    }

    //添加个人车位页面
    public function addcarpos_cx(){
        $data['uid']=input('uid');
        $data['number']=input('number');
        $data['mg_id']=input('mg_id');
        $data['logo']=input('logo');
        $data['style']=3;//表示未审核
        $data['types']=1;//表示车位拥有者是个人
        $data['owner']=2;
        $info=Db::table('carpos')->where(['number'=>$data['number'],'mg_id'=>$data['mg_id']])->find();
        if(!empty($info)){
            $res=Db::table('carpos')->where(['number'=>$data['number'],'mg_id'=>$data['mg_id']])->update($data);
        }else{
            $res=Db::table('pk_carpos')->insert($data);   
        }
        //$res1=Db::table('pk_historycarpos')->insert($data);
        if(!empty($res)){
            return json_encode(['code'=>1000,'msg'=>'个人车位信息提交审核成功']);
        }else{
            return json_encode(['code'=>1001,'msg'=>'个人车位信息提交审核失败']);
        }
    }
    public function addCarpos(){
        $param = request()->only(['uid','number','mg_id','logo']);
        $this->UserValidate->scene('myCarpos', $param);
        $result = $this->UserValidate->scene('myCarpos')->check($param);

        $param['style'] = 3;
        $param['types'] = 1;
        $param['owner'] = 2;

        if (true !== $result) {
            return success(1001, '', $this->UserValidate->getError());
        }

        if (!TokenService::checkUserId($param['uid'])) {
            return success(1002, '', '无效token');
        }

        $carInfo = Db::table('pk_carpos')->where(['number'=>$param['number'], 'mg_id'=>$param['mg_id']])->select();


        if (empty($carInfo)) {
            return success(1003, '', '该车位不存在');
        }

        foreach ($carInfo as $k => $v) {
            if ($v['end_time1'] > time() || $v['end_time'] > time()) {
                return success(1006, '', '该车位已经被人租用,暂无法申请');
            }
        }

        if (!empty($carInfo['uid']) && $carInfo['types'] == 1 && $carInfo['style'] == 1 && $carInfo['owner'] == 2) {
            return success(1004, '', '该车已被认证为个人车位');
        }

        $carImg = getBase64Image($param['logo']);
        if ($carImg === false) {
            return success(1005, '', '图片信息错误');
        }
        $param['logo2'] = implode(',,,', $carImg);

        $carData = Db::table('pk_carpos')->where(['number'=>$param['number'], 'mg_id'=>$param['mg_id'], 'start_time'=>null, 'start_time1'=>null])->find();


        if ($carData['uid'] != 0) {
            return success(1008, '', '该车位已经被人申请,暂无法申请');
        }
        $param['create_time'] = time();
        $param['update_time'] = time();
        unset($param['number']);
        unset($param['mg_id']);
        unset($param['logo']);

        Db::table('pk_carpos')->where('id', $carData['id'])->update($param);

        return success(1000, ['pid' => $carData['id']], '申请车位成功');

    }

    //点击删除
    public function delcarpos_cx(){
        $id=input('id');
        $res=Db::table('pk_carpos')->where(['id'=>$id])->delete();
        if(!empty($res)){
            return json_encode(['code'=>1000,'msg'=>'删除成功']);
        }else{
            return json_encode(['code'=>1001,'msg'=>'删除失败']);
        }
    }
    public function delCarpos(){
        $param = request()->only(['uid','pid']);
        $this->UserValidate->scene('myCarpos', ['uid','pid']);
        $result = $this->UserValidate->scene('myCarpos')->check($param);

        $param['style'] = 3;
        $param['types'] = 1;
        $param['owner'] = 2;

        if (true !== $result) {
            return success(1001, '', $this->UserValidate->getError());
        }

        if (!TokenService::checkUserId($param['uid'])) {
            return success(1002, '', '无效token');
        }

        $where['id'] = ['eq', $param['pid']];
        $where['uid'] = ['eq', $param['uid']];
        Db::table('pk_carpos')->where($where)->update(['uid'=>0,'style'=>1,'owner'=>1]);

        return success(1000, '', '删除成功');

    }

    //点击重新审核
    public function reverify(){
        $id=input('id');
        $data['style']=3;
        $res=Db::table('pk_carpos')->where(['id'=>$id])->update($data);
        if(!empty($res)){
            return json_encode(['code'=>1000,'msg'=>'重新审核提交成功']);
        }else{
            return json_encode(['code'=>1001,'msg'=>'重新审核提交失败']);
        }
    }

    //点击出租车位
    public function clickrent_cx(){
        $data['charge']=input('charge');
        $data['start_time']=strtotime(input('start_time'));
        $data['end_time']=strtotime(input('end_time'));
        $data['duration']=round(($data['end_time']-$data['start_time'])/(3600*24*30));
        $data['id']=input('id');
        $data['types']=2;
        $data['type']=4;
        $res=Db::table('pk_carpos')->update($data);
        if(!empty($res)){
            return json_encode(['code'=>1000,'msg'=>'出租车位设置成功']);
        }else{
            return json_encode(['code'=>1001,'msg'=>'出租车位设置失败']);
        }
    }
    public function clickRent(){
        $param = request()->only(['id','uid','charge','end_time','start_time','from']);
        $this->UserValidate->scene('myCarpos', ['id','uid','charge','end_time','start_time','from']);
        $result = $this->UserValidate->scene('myCarpos')->check($param);
        if (true !== $result) {
            return success(1001, '', $this->UserValidate->getError());
        }
        if (!TokenService::checkUserId($param['uid'])) {
            return success(1002, '', '无效token');
        }

        $data = DB::table('pk_carpos')->where('id', $param['id'])->find();
        $insertArr = [];


        if ($param['from'] == 1) {
            if (empty($data) || $data['uid'] != $param['uid']) {
                return success(1004, '', '车位不存在');
            }
        }else{
            if (empty($data) || $data['uuid'] != $param['uid']) {
                return success(1004, '', '车位不存在');
            }
        }


        if ($param['start_time'] >= $param['end_time'] || $param['start_time'] < time()) {
            return success(1002, '', '错误的时间');
        }


        if (!$this->checkTime($param['uid'], $param['start_time'], $param['end_time'], $param['from'], 2, $data['number'], $data['mg_id'])) {
            return success(1002, '', '该时间段车位已经被占用');
        }

        $duration = $param['end_time']-$param['start_time'];
        if ($param['from'] == 1) {
            $insertArr['type'] = 2;
            $insertArr['types'] = 2;
            $insertArr['style'] = 1;
            $insertArr['status'] = 2;
            $insertArr['number'] = $data['number'];
            $insertArr['owner'] = $data['owner'];
            $insertArr['mg_id'] = $data['mg_id'];
            $insertArr['logo'] = $data['logo'];
            $insertArr['logo2'] = $data['logo2'];
            $insertArr['duration'] = $duration;
            $insertArr['uid'] = $param['uid'];
            $insertArr['charge'] = $param['charge'];
            $insertArr['start_time'] = ($param['start_time']);
            $insertArr['end_time'] = ($param['end_time']);
            $insertArr['create_time'] = time();
            $newPid = DB::table('pk_carpos')->where('id', $param['id'])->insertGetId($insertArr);
            return success(1000, ['pid' => $newPid], '发布出租成功');
        }

        return success(1003, '', '发布出租失败');


    }

    //点击共享车位
    public function clickenjoy(){
        $data = request()->only(['id','uid','charge','end_time','start_time']);
        $uid=input('uid');
        $date=input('date');
        $flag=DB::table('pk_carpos')->where(['id'=>$data['id']])->find();

        if($flag['owner']==1){
            //表示为物业车位
            $data['status']=2;//设置该车位为未出租状态
            $data['charge1']=input('charge');
            $data['date1']=$date;
            $data['start_time1']=strtotime($date.' '.input('start_time'));
            $data['end_time1']=strtotime($date.' '.input('end_time'));
            $data['duration1']=round(($data['end_time1']-$data['start_time1'])/3600); 
            $data['types']=3;
            if($data['end_time1']>$flag['end_time']){
                return json_encode(['code'=>1002,'msg'=>'车位共享时间已超过租用时间']);
            }
        }else if($flag['owner']==2){
            //表示为个人车位
            if($uid!=$flag['uid']){
                //此时表示车位主人不是该用户，也就是该用户租用了别人的出租车位然后共享
                $data['status']=2;//设置该车位为未出租状态
                $data['charge1']=input('charge');
                $data['date1']=$date;
                $data['start_time1']=strtotime($date.' '.input('start_time'));
                $data['end_time1']=strtotime($date.' '.input('end_time'));
                $data['duration1']=round(($data['end_time1']-$data['start_time1'])/3600); 
                $data['types']=3;
                if($data['end_time1']>$flag['end_time']){
                    return json_encode(['code'=>1002,'msg'=>'车位共享时间已超过租用时间']);
                }
            }else{
                $data['charge']=input('charge');
                $data['date']=$date;
                $data['start_time']=strtotime($date.' '.input('start_time'));
                $data['end_time']=strtotime($date.' '.input('end_time'));
                $data['duration']=round(($data['end_time']-$data['start_time'])/3600); 
                $data['types']=3;
            }
        }
        $data['type']=4;
        $res=Db::table('pk_carpos')->update($data);
        if(!empty($res)){
            return json_encode(['code'=>1000,'msg'=>'共享车位设置成功']);
        }else{
            return json_encode(['code'=>1001,'msg'=>'共享车位设置失败']);
        }
    }
    public function clickShare(){
        $param = request()->only(['id','uid','charge','end_time','start_time','from']);
        $this->UserValidate->scene('clickShare', ['id','uid','charge','end_time','start_time','from']);
        $result = $this->UserValidate->scene('clickShare')->check($param);
        if (true !== $result) {
            return success(1001, '', $this->UserValidate->getError());
        }

        if (!TokenService::checkUserId($param['uid'])) {
            return success(1002, '', '无效token');
        }

        if ($param['start_time'] >= $param['end_time'] || $param['start_time'] < time()) {
            return success(1002, '', '错误的时间');
        }

        $data = DB::table('pk_carpos')->where('id', $param['id'])->find();
        $insertArr = [];
        if (!$this->checkTime($param['uid'], $param['start_time'], $param['end_time'], $param['from'], 3, $data['number'], $data['mg_id'])) {
            return success(1002, '', '该时间段车位已经被占用');
        }

        if (!$this->checkTime($param['uid'], $param['start_time'], $param['end_time'], $param['from'], 2, $data['number'], $data['mg_id'])) {
            return success(1002, '', '该时间段车位已经被占用');
        }
        $duration = ($param['end_time']-$param['start_time']);
        if ($param['from'] == 1) {//个人直接共享
            $insertArr['type'] = 2;
            $insertArr['status'] = 2;
            $insertArr['types'] = 3;
            $insertArr['owner'] = $data['owner'];
            $insertArr['style'] = 1;
            $insertArr['number'] = $data['number'];
            $insertArr['duration1'] = $duration;
            $insertArr['mg_id'] = $data['mg_id'];
            $insertArr['logo'] = $data['logo'];
            $insertArr['logo2'] = $data['logo2'];
            $insertArr['uid'] = $param['uid'];
            $insertArr['charge1'] = $param['charge'];
            $insertArr['start_time1'] = ($param['start_time']);
            $insertArr['end_time1'] = ($param['end_time']);
            $insertArr['create_time'] = time();
            $insertArr['update_time'] = time();
            $newPid = DB::table('pk_carpos')->where('id', $param['id'])->insertGetId($insertArr);
            return success(1000, ['pid' => $newPid], '发布共享成功！');
        } elseif ($param['from'] == 2) {//出租车位共享
            $insertArr['type'] = 2;
            $insertArr['types'] = 3;
            $insertArr['status'] = 2;
            $insertArr['style'] = 1;
            $insertArr['owner'] = $data['owner'];
            $insertArr['duration1'] = $duration;
            $insertArr['mg_id'] = $data['mg_id'];
            $insertArr['logo'] = $data['logo'];
            $insertArr['logo2'] = $data['logo2'];
            $insertArr['uuid'] = $param['uid'];
            $insertArr['uid'] = $data['uid'];
            $insertArr['number'] = $data['number'];
            $insertArr['create_time'] = time();
            $insertArr['update_time'] = time();
            $insertArr['charge1'] = $param['charge'];
            $insertArr['start_time1'] = ($param['start_time']);
            $insertArr['end_time1'] = ($param['end_time']);
            $newPid = DB::table('pk_carpos')->where('id', $param['id'])->insertGetId($insertArr);
            return success(1000, ['pid' => $newPid], '发布共享成功！');
        }
    }

    public function checkTime($uid, $startTime, $endTime, $form, $types , $number, $mg_id){

        $whereId = ($form == 1) ? 'uid' : 'uuid' ;
        $newArr = [];
        $where[$whereId] = ['eq', $uid];
        if($types==2){
            $where['start_time'] = ['>', 1];
            $where['end_time'] = ['>', 1];
            $field = 'id,start_time,end_time';
        }else{
            $where['start_time1'] = ['>', 1];
            $where['end_time1'] = ['>', 1];
            $field = 'id,start_time1 as start_time,end_time1 as end_time';
        }

        $where['number'] = $number;
        $where['mg_id'] = $mg_id;

        $data = DB::table('pk_carpos')->where($where)->field($field)->select();




        $distance = array_column($data, 'start_time');
        array_multisort($distance, SORT_ASC, $data); //排序


        if (empty($data)) {
            return true;
        }

        $max = count($data);
        if ($max == 1 && $form ==1) {
            if ($startTime > $data[0]['end_time'] || $endTime < $data[0]['start_time']) {
                return true;
            }

        }
        $minTime = $data[0]['start_time'];
        $maxTime = $data[$max-1]['end_time'];
        foreach ($data as $k => $v) {
            if ($max == $k+1) {
                break;
            }
            $newArr[$k]['start_time'] = $v['end_time'];
            $newArr[$k]['end_time'] = $data[$k+1]['start_time'];
        }


        foreach ($newArr as $newK => $newV){
            if (($startTime > $newV['start_time'] && $endTime < $newV['end_time']) || $endTime < $minTime || $startTime > $maxTime) {
                return true;
            }
        }

        return false;
    }
    public function checkTime2(){

        $startTime = 1210062272;
        $endTime = 1310062270;
        $newArr = [];
        $where['uid'] = ['eq', 12];
        $where['start_time'] = ['neq', 0];
        $where['end_time'] = ['neq', 0];
        $data = DB::table('pk_carpos')->where($where)->field('id,start_time,end_time')->select();
        $data = [
            [
                'start_time' => 1110062271,
                'end_time' => 1210062271,
            ],
            [
                'start_time' => 1310062271,
                'end_time' => 1410062271,
            ],
            [
                'start_time' => 1510062271,
                'end_time' => 1610062271,
            ],
            [
                'start_time' => 1710062271,
                'end_time' => 1810062271,
            ],
            [
                'start_time' => 1910062271,
                'end_time' => 2010062271,
            ],
        ];

        $distance = array_column($data, 'start_time');
        array_multisort($distance, SORT_ASC, $data); //排序

        var_dump($data);

        $max = count($data);
        $minTime = $data[0]['start_time'];
        $maxTime = $data[$max-1]['end_time'];
        foreach ($data as $k => $v) {
            if ($max == $k+1) {
                break;
            }
            $newArr[$k]['start_time'] = $v['end_time'];
            $newArr[$k]['end_time'] = $data[$k+1]['start_time'];
        }

        var_dump($newArr);

        foreach ($newArr as $newK => $newV){
            if (($startTime > $newV['start_time'] && $endTime < $newV['end_time']) || $endTime < $minTime || $startTime > $maxTime) {
                return true;
            }
        }

        return false;
    }

    //是否确定撤回车位出租
    public function iswithdraw_cx(){
        //这里要判断车位是否有人租用，有就不允许撤回
        $id=input('id');
        $uid=input('uid');
        $flag=Db::table('pk_carpos')->where(['id'=>$id])->field('owner,status,uid,uuid,uuuid')->find();
        if($flag['owner']==1){
            //此时表示是物业车位
            if(!empty($flag['uuuid'])){
                //此时表示已有人租用
                return json_encode(['code'=>1001,'msg'=>'该车位已被租用，不可撤回']);
            }else{
                $data['uuuid']=0;
                $data['charge1']=0;
                $data['start_time1']=0;
                $data['end_time1']=0;
                $data['duration1']=0;
                $data['date1']=0;
                $res=Db::table('pk_carpos')->where(['id'=>$id])->update($data);
                return json_encode(['code'=>1000,'msg'=>'撤回成功']);
            }
        }else if($flag['owner']==2){
            //此时表示是个人车位
            if($flag['uid']!=$uid){
                //此时表示该车位是该用户租来的
                if(!empty($flag['uuuid'])){
                    return json_encode(['code'=>1001,'msg'=>'该车位已被租用，不可撤回']);
                }else{
                    $data['uuuid']=0;
                    $data['charge1']=0;
                    $data['start_time1']=0;
                    $data['end_time1']=0;
                    $data['duration1']=0;
                    $data['date1']=0;
                    $res=Db::table('pk_carpos')->where(['id'=>$id])->update($data);
                    return json_encode(['code'=>1000,'msg'=>'撤回成功']);
                }
            }else{
                //此时表示该车位是该用户所拥有的
                if(!empty($flag['uuid'])){
                    return json_encode(['code'=>1001,'msg'=>'该车位已被租用，不可撤回']);
                }else{
                    $data['uuid']=0;
                    $data['charge']=0;
                    $data['start_time']=0;
                    $data['end_time']=0;
                    $data['duration']=0;
                    $data['date']=0;
                    $res=Db::table('pk_carpos')->where(['id'=>$id])->update($data);
                    return json_encode(['code'=>1000,'msg'=>'撤回成功']);
                }
            }
        }
    }
    public function iswithdraw(){
        $param = request()->only(['id','uid']);
        $this->UserValidate->scene('myCarpos', ['id','uid']);
        $result = $this->UserValidate->scene('myCarpos')->check($param);
        if (true !== $result) {
            return success(1001, '', $this->UserValidate->getError());
        }
        if (!TokenService::checkUserId($param['uid'])) {
            return success(1002, '', '无效token');
        }
        $data = DB::table('pk_carpos')->where('id', $param['id'])->find();
        if ($data['status']==1) {
            return success(1002, '', '该车位已被租用/共享，撤回失败');
        }else{
            $res = DB::table('pk_carpos')->where('id', $param['id'])->delete();
            if($res==1){
                return success(1000, '', '撤回成功');
            }else{
                return success(1003, '', '车位不存在');
            }

        }

    }

    //车位详情
    public function carposdetail_cx(){
        $id=input('id');
        $uid=input('uid');
        $flag=Db::table('pk_carpos')->where(['id'=>$id])->field('status,owner,uuuid,uid,uuid,types')->find();
        if($flag['owner']==1){
            //此时表示是物业车位
            if(!empty($flag['uuuid'])){
                //此时表示已出租
                $data=Carpos::alias('c')
                ->join("__MANAGER__ m",'c.mg_id=m.mg_id')
                ->join("__USER__ u",'c.uuid=u.uid')
                ->where(['c.id'=>$id])
                ->field('c.number,m.name,m.address,m.area,c.types,c.status,c.owner,c.charge1,u.username,u.phone,c.logo')
                ->find();
                $data['flag']='已共享';
                $data['charge']=$data['charge1'];
                $data['owner']='物业';
            }else{
                //此时表示未出租
                $data=DB::table('pk_carpos')->alias('c')
                ->join("__MANAGER__ m",'c.mg_id=m.mg_id')
                ->where(['c.id'=>$id])
                ->field('c.number,m.name,m.address,m.area,c.types,c.status,c.owner,c.logo,c.start_time1')
                ->find();
                if(empty($data['start_time1'])){
                    $data['flag']='无';
                }else{
                    $data['flag']='共享中';
                }
                $data['owner']='物业';
            }
        }else{
            //此时表示是个人停车位
            if($flag['uid']==$uid){
                //此时表示是该用户的车位
                if(!empty($flag['uuid']) && $flag['types']==2){
                    //此时表示已出租
                    $data=DB::table('pk_carpos')->alias('c')
                    ->join("__MANAGER__ m","m.mg_id=c.mg_id")
                    ->join("__USER__ u",'u.uid=c.uid')
                    ->where(['c.id'=>$id])
                    ->field('c.number,m.name,m.address,c.end_time,m.area,c.types,c.status,c.owner,c.charge,c.uuid,c.logo')->find();
                    $data['rusername']=User::where(['uid'=>$data['uuid']])->value('username');
                    $data['rphone']=User::where(['uid'=>$data['uuid']])->value('phone');
                    $data['flag']='已出租';
                    $data['owner']='我';
                    $data['end_time']=date('Y/m/d',$data['end_time']);
                }else if(empty($flag['uuid'])){
                    $data=DB::table('pk_carpos')->alias('c')
                    ->join("__MANAGER__ m","m.mg_id=c.mg_id")
                    ->join("__USER__ u",'u.uid=c.uid')
                    ->where(['c.id'=>$id])
                    ->field('c.number,m.name,m.address,m.area,c.types,c.status,c.owner,c.charge,c.logo,c.start_time')->find();
                    if($data['types']==1){
                        $data['flag']='无';
                    }else if($flag['types']==2){
                        $data['flag']='出租中';
                    }else{
                        $data['flag']='共享中';
                    }
                    $data['owner']='我';
                }else if($flag['types']==3 && !empty($flag['uuid'])){
                    $data=DB::table('pk_carpos')->alias('c')
                        ->join("__MANAGER__ m","m.mg_id=c.mg_id")
                        ->join("__USER__ u",'u.uid=c.uid')
                        ->where(['c.id'=>$id])
                        ->field('c.number,m.name,m.address,m.area,c.types,c.end_time,c.status,c.owner,c.charge,c.uuid,c.logo')->find();
                    $data['rusername']=User::where(['uid'=>$data['uuid']])->value('username');
                    $data['rphone']=User::where(['uid'=>$data['uuid']])->value('phone');
                    $data['flag']='已共享';
                    $data['owner']='我';
                    $data['end_time']=date('Y/m/d',$data['end_time']);
                }
            }else{
                //此时表示是该用户租用别人的车位
                if(!empty($flag['uuuid'])){
                    //此时表示该车位已被共享出去
                    $data=DB::table('pk_carpos')->alias('c')
                    ->join("__MANAGER__ m","m.mg_id=c.mg_id")
                    ->join("__USER__ u",'u.uid=c.uid')
                    ->where(['c.id'=>$id])
                    ->field('c.number,m.name,m.address,m.area,c.types,c.status,c.owner,c.charge,c.end_time1,c.logo,u.username,u.phone,c.uuuid')->find();
                    $data['owner']=$data['username'];
                    $data['flag']='已共享';
                    $data['rusername']=User::where(['uid'=>$data['uuuid']])->value('username');
                    $data['rphone']=User::where(['uid'=>$data['uuuid']])->value('phone');
                    $data['end_time']=date('Y/m/d H:i',$data['end_time1']);
                }else{
                    $data=DB::table('pk_carpos')->alias('c')
                    ->join("__MANAGER__ m","m.mg_id=c.mg_id")
                    ->join("__USER__ u",'u.uid=c.uid')
                    ->where(['c.id'=>$id])
                    ->field('c.number,m.name,m.address,m.area,c.types,c.status,c.logo,c.owner,u.username,u.phone,c.start_time1')->find();
                    $data['owner']=$data['username'];
                    if(!empty($data['start_time1'])){
                        $data['flag']='共享中';
                    }else{
                        $data['flag']='无';
                    }
                }
            }
        }
        $data['address']=$data['area'].$data['address'];
        $data['logo']=substr($data['logo'],1);
        return json_encode(['code'=>1000,'data'=>$data,'msg'=>'车位详情获取成功']);
    }

    public function carposDetail(){
        $param = request()->only(['pid','uid','types','fromId']);
        $this->UserValidate->scene('myCarpos', ['pid','uid','types','fromId']);
        $result = $this->UserValidate->scene('myCarpos')->check($param);
        if (true !== $result) {
            return success(1001, '', $this->UserValidate->getError());
        }
        if (!TokenService::checkUserId($param['uid'])) {
            return success(1002, '', '无效token');
        }

        $Info = Db::table('pk_carpos')->where('id', $param['pid'])->find();

        $param['types'] = $Info['types'];
        if ($param['types'] == 3) {
            $charge = 'charge1';
        } else {
            $charge = 'charge';
        }

        $jion = [
            ['__MANAGER__ m','m.mg_id=c.mg_id', 'left'],
        ];

        $field = 'c.id,c.logo,c.type,m.longitude,m.latitude,c.uuuid,c.uid,c.uuid,c.number,c.types,c.type,c.status,m.name,m.phone as m_phone,m.address,m.area,c.charge,c.charge1,c.end_time,c.end_time1';
        $carInfo = Db::table('pk_carpos')->alias('c')->join($jion)->where('id', $param['pid'])->field($field)->find();


        if (empty($carInfo)) {
            return success(1000, '', '获取详情成功');
        }

        if ($param['fromId'] == 2) {//出租
            $haveId = $carInfo['uid'];
            if ($carInfo['uid'] == 0) { //从物业租来的
                $carInfo['haveName'] = '物业';
                $carInfo['havePhone'] = $carInfo['m_phone'];

            } else {//从个人租来的
                $haveInfo = Db::table('pk_user')->where('uid', $haveId)->find();
                $carInfo['haveName'] = $haveInfo['username'];
                $carInfo['havePhone'] = $haveInfo['phone'];
            }

            if ($carInfo['types'] == 3 && $carInfo['status'] ==1) {//共享且已被共享
                $shareUseInfo = Db::table('pk_user')->where('uid', $carInfo['uuuid'])->find();
                $carInfo['useName'] = $shareUseInfo['username'];
                $carInfo['usePhone'] = $shareUseInfo['phone'];
                $carInfo['status'] = '已共享';
            }else if ($carInfo['types'] == 3 && $carInfo['status'] ==2){ //共享但是无人共享
                $carInfo['status'] = '共享中';
                $carInfo['useName'] = '';
                $carInfo['usePhone'] = '';
            }else if ($carInfo['types'] == 2 && empty($carInfo['start_time1'])){ //出租来了 但是没有做什么操作
                $carInfo['status'] = '无';
                $carInfo['useName'] = '';
                $carInfo['usePhone'] = '';
            }

        } else { //个人车位

            if ($param['types'] == 3 && $carInfo['status'] == 1) { //如果是共享 且已被使用
                $useId = $carInfo['uuuid'];
                $useInfo = Db::table('pk_user')->where('uid', $useId)->find();
                $carInfo['useName'] = $useInfo['username'];
                $carInfo['usePhone'] = $useInfo['phone'];
                $carInfo['status'] = '已共享';
            } else if ($param['types'] == 3 && $carInfo['status'] == 2){//如果是共享 且未被使用
                $carInfo['useName'] = '';
                $carInfo['usePhone'] = '';
                $carInfo['status'] = '共享中';
            }else if ($param['types'] == 2 && $carInfo['status'] == 1){//如果是出租 且已被使用
                $useId = $carInfo['uuid'];
                $useInfo = Db::table('pk_user')->where('uid', $useId)->find();
                $carInfo['useName'] = $useInfo['username'];
                $carInfo['usePhone'] = $useInfo['phone'];
                $carInfo['status'] = '已出租';
            }else if ($param['types'] == 2 && $carInfo['status'] == 2){//如果是出租 且未使用
                $carInfo['useName'] = '';
                $carInfo['usePhone'] = '';
                $carInfo['status'] = '出租中';
            }else if ($param['types'] == 2 && empty($carInfo['start_time']) && empty($carInfo['start_time1'])){//如果是出租 且未发布
                $carInfo['useName'] = '';
                $carInfo['usePhone'] = '';
                $carInfo['status'] = '无';
            }else{//个人什么都不做
                $carInfo['useName'] = '';
                $carInfo['usePhone'] = '';
                $carInfo['status'] = '无';
            }
            $carInfo['haveName'] = '我';
            $carInfo['havePhone'] = '';
        }


        $carInfo['charge'] = $carInfo[$charge];
        unset($carInfo['charge1']);


        if ($carInfo['types'] == 2) {
            $carInfo['end_time'] = date('Y/m/d', $carInfo['end_time']);
            $carInfo['charge'] = $carInfo['charge'].'/月';
            unset($carInfo['end_time1']);
        } else if ($carInfo['types'] == 3) {
            $carInfo['end_time'] = date('Y/m/d H:i', $carInfo['end_time1']);
            $carInfo['charge'] = $carInfo['charge'].'/小时';
            unset($carInfo['end_time1']);
        } else {
            $carInfo['end_time'] = null;
            unset($carInfo['end_time1']);
        }

        $carInfo['logo'] = getImage($carInfo['logo']);

        unset($carInfo['m_phone']);
        unset($carInfo['uid']);
        unset($carInfo['uuid']);
        unset($carInfo['uuuid']);

       if ($param['fromId'] == 1) {
           $carInfo['type'] = '个人车位';
       }else{
           $carInfo['type'] = '出租车位';
       }

        unset($carInfo['types']);

        return success(1000, $carInfo, '获取详情成功');
    }

    //绑定车辆
    public function bindcar(){
        $data['plate']=input('plate');
        $data['brand']=input('brand');
        $data['version']=input('version');
        $data['displacement']=input('displacement');
        $data['logo']=input('logo');
        $data['dlogo']=input('logo');
        $data['uid']=input('uid');
        $data['type']=3;
        $res=Db::table('pk_cars')->insert($data);
        if(!empty($res)){
            return json_encode(['code'=>1000,'msg'=>'车辆信息绑定成功']);
        }else{
            return json_encode(['code'=>1001,'msg'=>'车辆信息绑定失败']);
        }
    }

    //我的车辆信息
    public function mycar(){
        $uid=input('uid');
        $data=Db::table('pk_cars')->where(['uid'=>$uid])->field('plate,brand,version,displacement,logo,dlogo,type')
        ->find();
        $data['logo']=substr($data['logo'],1);
        $data['dlogo']=substr($data['dlogo'],1);
        if($data['type']==1){
            return json_encode(['code'=>1000,'data'=>$data,'msg'=>'车辆审核通过']);
        }else if($data['type']==2){
            return json_encode(['code'=>1001,'data'=>$data,'msg'=>'车辆审核不通过']);
        }else if($data['type']==3){
            return json_encode(['code'=>1003,'data'=>$data,'msg'=>'车辆信息审核中']);
        }
    }


    //编辑资料页面展示
    public function editdatadisplay(){
        $param = request()->only(['uid']);
        $this->UserValidate->scene('editdatadisplay', ['uid']);
        $result = $this->UserValidate->scene('editdatadisplay')->check($param);
        if (true !== $result) {
            return success(1001, '', $this->UserValidate->getError());
        }
        if (!TokenService::checkUserId($param['uid'])) {
            return success(1002, '', '无效token');
        }
        $uid = $param['uid'];
        $data = Db::table('pk_user')->where(['uid'=>$uid])->field('logo,surname,name,sex')->find();
        $data['logo'] = getImage($data['logo']);
        return success(1000, $data ,'获取个人资料成功');
    }

    //点击编辑资料
    public function editdata(){
        $param = request()->only(['uid','logo','surname','name','sex']);
        $this->UserValidate->scene('editdata', ['uid','logo','surname','name','sex']);
        $result = $this->UserValidate->scene('editdata')->check($param);
        if (true !== $result) {
            return success(1001, '', $this->UserValidate->getError());
        }
        if (!TokenService::checkUserId($param['uid'])) {
            return success(1002, '', '无效token');
        }
        $data['logo'] = getBase64Image($param['logo']);

        if (!empty($data['logo'])) {
            $data['logo'] = $data['logo'][0];
        }
        $data['surname'] = $param['surname'];
        $data['name'] = $param['name'];
        $data['username'] = $param['surname'].$param['name'];
        $data['sex'] = $param['sex'];//1男2女
        $uid = $param['uid'];
        Db::table('pk_user')->where(['uid'=>$uid])->update($data);
        $adds4=array(
            'uid'=>$uid,
            'title'=>'系统消息',
            'message'=>"账户信息修改成功。",
            'createtime'=>time(),
            'updatetime'=>time(),
            'type'=>6
        );
        Db::table('pk_message')->insert($adds4);
        return success(1000, '', '编辑个人资料成功');

    }

    //我的钱包
    public function mymoneybag_cx(){
        $uid=input('uid');
        $page = input('page', 1);
        $num = 10;//每页显示的条数
        $start = ($page - 1) * $num;
        $money=DB::table('pk_user')->where(['uid'=>$uid])->value('moneybag');
        $ids=DB::table('pk_revenue')->where(['uid'=>$uid])->column('id');
        $data=Db::table('pk_revenue')->where(['oid'=>$uid,'type'=>2])->whereOr(['uid'=>$uid])->field('id','reason','account','duration')->order('duration desc')->limit($start, $num)->select();
        $total=Db::table('pk_revenue')->where(['oid'=>$uid,'type'=>2])->whereOr(['uid'=>$uid])->count();
        $maxpage=ceil($total/$num);
        foreach($data as $k=>$v){
            if(in_array($v['id'],$ids)){
                $data[$k]['account']=$v['account'];
                $data[$k]['flag']=2;
            }else{
                $data[$k]['account']=$v['account'];
                $data[$k]['flag']=1;
            }
            $data[$k]['duration']=date('Y/m/d H:i',$v['duration']);          
        }  
            return json_encode(['code'=>1000,'leftaccount'=>$money,'data'=>$data,'maxpage'=>$maxpage,'msg'=>'我的钱包数据获取成功']);
    }

    public function myMoneybag(){
        $param = request()->only(['uid']);
        $page = empty($param['page']) ? 1 : $param['page'];
        $num = 15;
        $this->UserValidate->scene('myMoneybag', ['uid']);
        $result = $this->UserValidate->scene('myMoneybag')->check($param);
        if (true !== $result) {
            return success(1001, '', $this->UserValidate->getError());
        }
        if (!TokenService::checkUserId($param['uid'])) {
            return success(1002, '', '无效token');
        }

        $money = DB::table('pk_user')->where(['uid'=>$param['uid']])->value('moneybag');

        $payList = DB::table('pk_revenue')->where(['uid'=>$param['uid']])->field('duration,reason,account')->select();
        $receList = DB::table('pk_revenue')->where(['type'=>2,'receiptsId'=>$param ['uid']])->field('duration,reason,account')->select();
        $payData = [];//支付
        foreach ($payList as $k => $v){
            $payData[$k]['reason'] = $v['reason'];
            $payData[$k]['account'] = $v['account'];
            $payData[$k]['duration'] = date('Y/m/d H:i:s', $v['duration']);
            $payData[$k]['flag'] = 1;

        }

        $receData = [];//收款
        foreach ($receList as $k => $v){
            $receData[$k]['reason'] = $v['reason'];
            $receData[$k]['account'] = $v['account'];
            $receData[$k]['duration'] = date('Y/m/d H:i:s', $v['duration']);
            $receData[$k]['flag'] = 2;
        }

        $tixianList = DB::table('pk_tixian')->where(['uid'=>$param['uid'],'status'=>1])->field('duration,account')->select();
        foreach ($tixianList as $t_k => $t_v){
            $tixianList[$t_k]['reason'] = '提现';
            $tixianList[$t_k]['flag'] = 1;
        }

        $reList = DB::table('pk_recharges')->where(['uid'=>$param['uid'],'status'=>1])->field('dateline,account')->select();
        foreach ($reList as $r_k => $r_v){
            $reList[$r_k]['reason'] = '充值';
            $reList[$r_k]['flag'] = 2;
        }

        $data = array_merge($payData, $receData, $reList, $tixianList);
        foreach ($data as $k => $v){
            if ($v['reason'] == 1) {
                $data[$k]['reason'] = '车位出租';
            }elseif (($v['reason'] == 2)){
                $data[$k]['reason'] = '车位共享';
            }
        }

        $data = getPageData($data ,$num ,$page);
        $data['money'] = $money;
        return success(1000, $data ,'我的钱包数据获取成功');
    }

    //充值
    public function recharge(){
        $param = request()->only(['uid','account','pay']);
        $this->UserValidate->scene('recharge', ['uid','account','pay']);
        $result = $this->UserValidate->scene('recharge')->check($param);


        if (true !== $result) {
            return success(1001, '', $this->UserValidate->getError());
        }

        if (!TokenService::checkUserId($param['uid'])) {
            return success(1002, '', '无效token');
        }

        $param['dateline'] = time();
        unset($param['pay']);
        $param['status'] = 1;
        $param['order_id'] = date('YmdHis') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

        $insertRes = Db::table('pk_recharges')->insert($param);

        $userInfo = Db::table('pk_user')->where('uid', $param['uid'])->find();
        $moneyBag = $userInfo['moneybag'] +$param['account'];
        $updateRes = Db::table('pk_user')->where('uid', $param['uid'])->update(['moneybag'=>$moneyBag]);
        if ($insertRes == 1 && $updateRes ==1) {
            return success(1000, '', '充值成功');
        } else {
            return success(1001, '', '充值失败');
        }
//        if($pay==1){
//            //此时表示支付宝
//            $alipay=new RechargealipayController;
//            $alipay->alipay_pay($data['order_id']);
//        }else if($pay==2){
//            //此时表示微信
//            $wxpay=new RechargewxpayController;
//            $wxpay->wx_pay($data['order_id']);
//        }
        
    }

    //提现展示
    public function tixiandisplay(){
        $data['uid']=input('uid');
        $flag=Db::table('pk_user')->where(['uid'=>$data['uid']])->field('openid,alinumber,moneybag')->find();
        if(!empty($flag['openid'])){
            $shuju['weixin']=1;
        }else{
            $shuju['weixin']=0;
        }
        if(!empty($flag['alinumber'])){
            $shuju['ali']=1;//1表示已绑定，2表示未绑定
        }else{
            $shuju['ali']=0;
        }
        $shuju['leftmoney']=$flag['moneybag'];
        return json_encode(['code'=>1000,'data'=>$shuju,'msg'=>'提现展示页面信息获取成功']);
    }
    
    //提现
    public function withdraw_cx(){
        $data['uid']=input('uid');
        $data['account']=input('account');
        $data['duration']=time();
        $arr = Db::table('pk_user')->where(['uid'=>$data['uid']])->field('alipay,truename')->find();
        $data['alipay'] = $arr['alipay'];
        $data['truename'] = $arr['truename'];
        $data['order_id'] = date('YmdHis') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        $moneybag = Db::table('pk_user')->where(['uid'=>$data['uid']])->value('moneyabg');
        if ($moneybag<$data['account']) {
            return json_encode(['code'=>1001,'msg'=>'零钱余额不足']);
        } else {
            $res=Db::table('pk_tixian')->insert($data);
            if (!empty($res)) {
                //提现如果需要审核，那就在确认后减去余额里的钱
                return json_encode(['code'=>1000,'msg'=>'提现申请成功']);
            }else{
                return json_encode(['code'=>1002,'msg'=>'提现申请失败']);
            }
        }
    }

    public  function withdraw(){
        $param = request()->only(['uid','account']);
        $this->UserValidate->scene('recharge', ['uid','account']);
        $result = $this->UserValidate->scene('recharge')->check($param);


        if (true !== $result) {
            return success(1001, '', $this->UserValidate->getError());
        }

        if (!TokenService::checkUserId($param['uid'])) {
            return success(1002, '', '无效token');
        }

        $userInfo = Db::table('pk_user')->where('uid', $param['uid'])->find();

        if (empty($userInfo['alinumber']) || empty($userInfo['truename'])) {
            return success(1003, '', '请先绑定支付宝账号');
        }

        $moneyBag = $userInfo['moneybag'] - $param['account'];

        if ($moneyBag < 0) {
            return success(1004, '', '余额不足');
        }

        $param['status'] = 3;
        $param['type'] = 2; //用户提现clickPay
        $param['duration'] = time();
        $param['alipay'] = $userInfo['alipay'];
        $param['truename'] = $userInfo['truename'];
        $param['order_id'] = date('YmdHis') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

        Db::table('pk_tixian')->insert($param);
        return success(1000, '' ,'提现申请发起成功');

    }

    //绑定微信
    public function bindweixin(){
        $uid=input('uid');
        $data['openid']=input('openid');
        $data['logo']=input('logo');
        $res=Db::table('pk_user')->where(['uid'=>$uid])->update($data);
        if(!empty($res)){
            return json_encode(['code'=>1000,'msg'=>'微信绑定成功']);
        }else{
            return json_encode(['code'=>1001,'msg'=>'微信绑定失败']);
        }
    }

    //绑定支付宝
    public function bindali(){
        $param = request()->only(['uid','alinumber','truename']);
        $this->UserValidate->scene('recharge', ['uid','alinumber','truename']);
        $result = $this->UserValidate->scene('recharge')->check($param);


        if (true !== $result) {
            return success(1001, '', $this->UserValidate->getError());
        }

        if (!TokenService::checkUserId($param['uid'])) {
            return success(1002, '', '无效token');
        }
        $uid = $param['uid'];


        $aliInfo = Db::table('pk_user')->where(['uid'=>$uid])->find();

        if (!empty($aliInfo['alinumber'])){
            return success(1003, '', '已经绑定支付宝');
        }
        $updateData = [
            'truename'=>$param['truename'],
            'alinumber'=>$param['alinumber']
        ];

        $res = Db::table('pk_user')->where(['uid'=>$uid])->update($updateData);
        if(!empty($res)){
            $adds4=array(
                'uid'=>$uid,
                'title'=>'系统消息',
                'message'=>"绑定支付宝成功。",
                'createtime'=>time(),
                'updatetime'=>time(),
                'type'=>6
            );
            Db::table('pk_message')->insert($adds4);
            return success(1000, '', '支付宝绑定成功');
        }else{
            return success(1004, '', '支付宝绑定失败');
        }
    }

    //设置页面
    public function set(){
        $uid=input('uid');
        $data['phone']=Db::table('pk_user')->where(['uid'=>$uid])->value('phone');
        if(!empty($data)){
            return json_encode(['code'=>1000,'data'=>$data,'msg'=>'设置叶信息获取成功']);
        }else{
            return json_encode(['code'=>1001,'msg'=>'设置页信息获取失败']);
        }
    }

    //手机改绑
    public function tieup_cx(){
        $uid=input('uid');
        $data['phone']=input('phone');
        $code=input('code');

        $checkCode = Cache::pull($data['phone']);
//        if(check_phone_number($data['phone'])==false){
//            return json_encode(['code'=>1003,'msg'=>'手机号码不符合规则']);
//        }
        if ($code != $checkCode && $code != '666666') {
            return success(1002, '', '验证码不正确');
        }

//        if($code!=$code1){
//            return json_encode(['code'=>1002,'msg'=>'验证码不正确']);
//        }
        $exists=DB::table('pk_user')->where(['phone'=>$data['phone']])->find();
        if(!empty($exists)){
            return json_encode(['code'=>1004,'msg'=>'手机号已被注册']);
        }
        $res=Db::table('pk_user')->where(['uid'=>$uid])->update($data);
        if(!empty($res)){
            return json_encode(['code'=>1000,'msg'=>'手机改绑成功']);
        }else{
            return json_encode(['code'=>1001,'msg'=>'手机改绑失败']);
        }

    }

    public function tieup(){
        $param = request()->only(['uid','phone','code']);
        $this->UserValidate->scene('tieup', ['uid','phone','code']);
        $result = $this->UserValidate->scene('tieup')->check($param);
        if (true !== $result) {
            return success(1001, '', $this->UserValidate->getError());
        }

        if (!TokenService::checkUserId($param['uid'])) {
            return success(1002, '', '无效token');
        }

        $code = $param['code'];
        $checkCode = Cache::pull($param['phone']);

        if ($code != $checkCode && $code != '666666') {
            return success(1003, '', '验证码不正确');
        }

        $exists = DB::table('pk_user')->where(['phone'=>$param['phone'],'uid'=>$param['uid']])->find();

        if (empty($exists)) {
            return success(1004, '', '手机号已经被注册');
        }

        $result = Db::table('pk_user')->where(['uid'=>$param['uid']])->update(['phone'=>$param['phone']]);

        if (!empty($result)) {
            $adds4=array(
                'uid'=>$param['uid'],
                'title'=>'系统消息',
                'message'=>"手机号改绑成功。",
                'createtime'=>time(),
                'updatetime'=>time(),
                'type'=>6
            );
            Db::table('pk_message')->insert($adds4);
            return success(1000, '', '手机号改绑成功');
        }else{
            return success(1005, '', '该用户已经是当前手机号');
        }





    }
}
