<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

function mess($arr){
   $res= \think\Db::table('pk_message')->insert($arr);
   if($res){
       return true;
   }else{
       return false;
   }
}
function getTime($time, $types)
{
    $newTime = null;
    if ($types == 2) {//出租
        $newTime =  ceil(($time)/(3600*24*30));
    } else {//共享
        $newTime =  ceil(($time)/(3600));
    }
    return $newTime;
}

function getBase64Image($imgStr)
{
    $imgs = [];
    $imgArr = explode(',,,', $imgStr);
    foreach ($imgArr as $k => $v) {
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $v, $result)) {
            $type = $result[2];
            $new_file = "uploads/goods/" . date('Ymd', time()) . "/";
            if (!file_exists($new_file)) {
                mkdir($new_file, 0777);
            }
            $new_file = $new_file . time() .mt_rand(10, 10000000). ".{$type}";
            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $v)))) {
                $imgs[] = './' . $new_file;
            } else {
                $imgs[] = '';
            }
        } else {
            return false;
        }
    }
    return $imgs;
}

function getImage($path)
{
    $url = 'http://park.mumarenkj.com';
    if (is_array($path)) {
        foreach ($path as $k => $v) {
            $path[$k] = $url.substr($v, 1);
        }
        return $path;
    }
    return $url.substr($path, 1);
}

function getPageData($data, $num, $page)
{
    if (!empty($data)) {
        $data = array_chunk($data, $num, $page);
        $key = (($page) >= count($data)) ? count($data): ($page);
        $result['list'] = $data[$key-1];
        $result['maxPage'] = count($data);
        return $result;
    } else {
        $result['list'] = [];
        $result['maxPage'] = 0;
    }
    return $result;
}

/**
 * 出租共享列表数据处理
 * @param $data
 * @param $longitude
 * @param $latitude
 * @param $types
 * @param $flag
 * @return mixed
 */
function listData($data, $longitude, $latitude, $types, $flag)
{
    if (!empty($data)) {
        foreach ($data as $k => $v) {
            if ($types == 3) {
                $data[$k]['duration'] = getTime($data[$k]['duration'], 3);//小时
            } else {
                $data[$k]['duration'] = getTime($data[$k]['duration'], 2);//月
            }
            $data[$k]['address']=$v['area'].$v['address'];
            $data[$k]['distance'] = round(getdistance($longitude, $latitude, $v['longitude'], $v['latitude']));
        }

        if ($flag == 1) {
            $distance = array_column($data, 'distance');
            array_multisort($distance, SORT_ASC, $data);
        }
    }

    return $data;
}

function getRandChar($length)
{
    $str = null;
    $strPol = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
    $max = strlen($strPol) - 1;

    for ($i = 0;
         $i < $length;
         $i++) {
        $str .= $strPol[rand(0, $max)];
    }

    return $str;
}

function success($code, $data, $msg)
{
    return json_encode(['code'=>$code, 'data'=>$data, 'msg'=>$msg]);
}

function makeCode()
{
    $str = '1234567890';
    $randStr = str_shuffle($str);
    $code = substr($randStr, 0, 6);
    return $code;
}

function vv($data)
{
    var_dump($data);
    die();
}
// 应用公共文件
//共享出租过期的数据
//function overdue($uid){
    function overdue(){
        $time=time();
        //出租车位
        $wherea1=[
            'types'=>['eq',2],
            'style'=>['eq',1],
            'end_time'=>['<',$time],
        ];
        //共享
        $wherea3=[
            'types'=>['eq',3],
            'style'=>['eq',1],
            'end_time1'=>['<',$time],
        ];

        $data = \think\Db::table('pk_carpos')
            ->where($wherea1)
            ->whereOr(function ($query) use ($wherea3) {
                $query->where($wherea3);
            })
            ->select();

//    $where1['uuuid']=['eq',0];
//    $shuju=\think\Db::table('pk_carpos')->alias('c')
//        ->join('__MANAGER__ m','c.mg_id=m.mg_id')
//        ->where(['c.uuid'=>$uid,'c.style'=>1,'c.types'=>2])
//        ->where($where1)
////        ->where('c.start_time','<=',$time)
//        ->where('c.end_time','<',$time)
//        ->field('c.*')
//        ->select();
//    $shuju1=\think\Db::table('pk_carpos')->alias('c')
//        ->join('__MANAGER__ m','c.mg_id=m.mg_id')
//        ->where(['c.uuid'=>$uid,'c.style'=>1,'c.types'=>3,'c.charge1'=>null])
//        ->where($where1)
////        ->where('c.start_time','<=',$time)
//        ->where('c.end_time','<=',$time)
//        ->field('c.*')
//        ->select();
//    $info=\think\Db::table('pk_carpos')->alias('c')
//        ->join('__MANAGER__ m','c.mg_id=m.mg_id')
//        ->where(['c.uuuid'=>$uid,'c.style'=>1,'c.types'=>3])
////        ->where('c.start_time1','<=',$time)
//        ->where('c.end_time1','<',$time)
//        ->field('c.*')
//        ->select();
//    foreach($info as $k=>$v){
//        $info[$k]['end_time']=$v['end_time1'];
//        unset($info[$k]['end_time1']);
//    }
//    $data=array_merge($shuju,$info);
//    foreach($info as $k=>$v){
//        $info[$k]['end_time']=$v['end_time1'];
//        unset($info[$k]['end_time1']);
//    }
//    $data=array_merge($shuju,$shuju1,$info);
        return $data;
    }
//}
function array_remove_by_key($arr, $key){
    foreach ($arr as $k){
        if(!array_key_exists($key, $k)){
            return $data[]=$k;
        }
        $keys = array_keys($k);
        $index = array_search($key, $keys);
        if($index !== FALSE){
            array_splice($k, $index, 1);
        }
        $data[]=$k;
    }

    return $data;

}
/***********递归方式获取上下级权限信息****************/
function generateTree($data){
    $items = array();
    foreach($data as $v){
        $items[$v['ps_id']] = $v;
    }
    $tree = array();
    foreach($items as $k => $item){
        if(isset($items[$item['ps_pid']])){
            $items[$item['ps_pid']]['son'][] = &$items[$k];
        }else{
            $tree[] = &$items[$k];
        }
    }
    return getTreeData($tree);
}

function getTreeData($tree,$level=0){
    static $arr = array();
    foreach($tree as $t){
        $tmp = $t;
        unset($tmp['son']);
        //$tmp['level'] = $level;
        $arr[] = $tmp;
        if(isset($t['son'])){
            getTreeData($t['son'],$level+1);
        }
    }
    return $arr;
}
/***********递归方式获取上下级权限信息****************/

/**
 * 求两个已知经纬度之间的距离,单位为米
 * 
 * @param lng1 $ ,lng2 经度
 * @param lat1 $ ,lat2 纬度
 * @return float 距离，单位米
 * @author www.Alixixi.com 
 */
function getdistance($lng1, $lat1, $lng2, $lat2) {
    // 将角度转为狐度
    $radLat1 = deg2rad($lat1); //deg2rad()函数将角度转换为弧度
    $radLat2 = deg2rad($lat2);
    $radLng1 = deg2rad($lng1);
    $radLng2 = deg2rad($lng2);
    $a = $radLat1 - $radLat2;
    $b = $radLng1 - $radLng2;
    $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;
    return $s;
} 

/**
 * 计算时间差值
 * /**
 *type $start
 * type $type $end
 */
function time_difference($timediff){
    //计算天数
    $d = intval($timediff/86400);
    //计算小时数
    $remain = $timediff%86400;
    $h = intval($remain/3600);
    //计算分钟数
    $remain = $remain%3600;
    $m = intval($remain/60);
    //计算秒数
    $secs = $remain%60;

    return ($d*24+$h)."小时".$m."分".$secs."秒"; 
}

//判断手机号是否合法
function check_phone_number($subject){
    $pattern = '/^[0-9]{11}$/';
    if (preg_match($pattern, $subject)){
        return true;
    }
    return false;
}

//二维数组按某个值排序
 function my_sort($arrays,$sort_key,$sort_order=SORT_ASC,$sort_type=SORT_NUMERIC ){  
        if(is_array($arrays)){  
            foreach ($arrays as $array){  
                if(is_array($array)){  
                    $key_arrays[] = $array[$sort_key];  
                }else{  
                    return false;  
                }  
            }  
        }else{  
            return false;  
        } 
        array_multisort($key_arrays,$sort_order,$sort_type,$arrays);  
        return $arrays;  
    } 


/**
 * @author injection(injection.mail@gmail.com)
* @var date1日期1
* @var date2 日期2
* @var tags 年月日之间的分隔符标记,默认为'-' 
* @return 相差的月份数量
* @example:
$date1 = "2003-08-11";
$date2 = "2008-11-06";
$monthNum = getMonthNum( $date1 , $date2 );
echo $monthNum;
*/
function getMonthNum( $date1, $date2, $tags='-' ){
 $date1 = explode($tags,$date1);
 $date2 = explode($tags,$date2);
 return ($date2[0] - $date1[0]) * 12 + ($date2[1] - $date1[1]);
}

//PHP stdClass Object转array
function object_array($array) {
    if(is_object($array)) {
        $array = (array)$array;
    } if(is_array($array)) {
        foreach($array as $key=>$value) {
            $array[$key] = object_array($value);
        }
    }
    return $array;
}