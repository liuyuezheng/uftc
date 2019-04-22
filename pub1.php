<?php
	//预约发布任务的执行文件
	date_default_timezone_set('Asia/Shanghai');
    $mysql_conf = array(
    'host'    => '47.96.8.108', 
    'db'      => 'youfu', 
    'db_user' => 'yf_root', 
    'db_pwd'  => 'Youfu123.',  
    );

    $connect = mysqli_connect($mysql_conf['host'], $mysql_conf['db_user'], $mysql_conf['db_pwd']);
    if (!$connect) {
         die("could not connect to the database:\n" . mysqli_error());
    }

    mysqli_select_db($connect,$mysql_conf['db']);
    
    if (!mysqli_query($connect, "set names utf8")) {
        printf("Errormessage: %s\n", mysqli_error($conn));
    }

    $selectSql = "select * from pk_carpos where types!=4 and types!=1";
    $res = mysqli_query($connect,$selectSql);
    if (!$res) {
        die("could get the res:\n" . mysqli_error($res));
    }
    while ($row = mysqli_fetch_assoc($res)) {
        $total[]=$row;
    }
    //var_dump($total);
     //当前时间
     $time = strtotime(date('Y-m-d H:i:s',time()));
     foreach($total as $k=>$v){
        
        if(!empty($v['uuuid'])){
            //此时说明存在二级租用
            if($time - strtotime(date('Y-m-d H:i:s',$v['end_time1'])) >= 0 && $v['status']==1){
                $insertSql = "insert into pk_historycarpos (number,type,price,pay,status,mg_id,create_time,update_time,owner,uid,types,start_time,end_time,style,logo,remark,charge,date,uuid,uuuid,charge1,start_time1,end_time1,duration1,date1) values ('".$v['number']."','".$v['type']."','".$v['price']."','".$v['pay']."','".$v['status']."','".$v['mg_id']."','".$v['create_time']."','".$v['update_time']."','".$v['owner']."','".$v['uid']."','".$v['types']."','".$v['start_time']."','".$v['end_time']."','".$v['style']."','".$v['logo']."','".$v['remark']."','".$v['charge']."','".$v['date']."','".$v['uuid']."','".$v['uuuid']."','".$v['charge1']."','".$v['start_time1']."','".$v['end_time1']."','".$v['duration1']."','".$v['date1']."')";

                $updateSql="update pk_carpos set status=1,type=2,types=2,uuuid=0,charge1=0,start_time1=null,end_time1=mull,duration1=0,date1=null where id=".$v['id'];//此时表示该车位又可以被共享,且有变为出租车位

                /*if($v['end_time']==$v['end_time1']){
                    $updateSql="update pk_carpos set status=1,types=2,uuuid=0,charge1=0,start_time1=0,end_time1=0,duration1=0,date1=0 where id=".$v['id'];//此时表示该车位又可以被出租,且有变为出租车位
                }else{
                     $updateSql="update pk_carpos set status=2,types=2,uuuid=0,charge1=0,start_time1=0,end_time1=0,duration1=0,date1=0 where id=".$v['id'];//此时表示该车位又可以被出租,且有变为出租车位
                }*/
                //var_dump($insertSql);
            //设置不自动提交
                mysqli_query($connect,"SET AUTOCOMMIT=0");
                //开启事务
                mysqli_begin_transaction($connect);
                    if(!mysqli_query($connect,$insertSql)){
                        //回滚
                        mysqli_query($connect,"ROLLBACK");
                    }
                    if(!mysqli_query($connect,$updateSql)){
                         //回滚
                         mysqli_query($connect,"ROLLBACK");
                     }
                //执行事务
                mysqli_commit($connect);
            }
        }else{
            //此时表示都是一级租用
            if($time - strtotime(date('Y-m-d H:i:s',$v['end_time'])) >= 0 && $v['status']==1){
                $insertSql = "insert into pk_historycarpos (number,type,price,pay,status,mg_id,create_time,update_time,owner,uid,types,start_time,end_time,style,logo,remark,charge,date,uuid,uuuid,charge1,start_time1,end_time1,duration1,date1) values ('".$v['number']."','".$v['type']."','".$v['price']."','".$v['pay']."','".$v['status']."','".$v['mg_id']."','".$v['create_time']."','".$v['update_time']."','".$v['owner']."','".$v['uid']."','".$v['types']."','".$v['start_time']."','".$v['end_time']."','".$v['style']."','".$v['logo']."','".$v['remark']."','".$v['charge']."','".$v['date']."','".$v['uuid']."','".$v['uuuid']."','".$v['charge1']."','".$v['start_time1']."','".$v['end_time1']."','".$v['duration1']."','".$v['date1']."')";
                if($v['owner']==1){
                    //此时表示是物业车位
                    $updateSql="update pk_carpos set status=2,types=4,type=4,start_time=null,end_time=null,date=null where id=".$v['id'];//此时表示该车位又可以被出租
                }else if($v['owner']==2){
                    $updateSql="update pk_carpos set status=2,types=1,type=4,charge=0,start_time=null,end_time=null,date=null where id=".$v['id'];//此时表示该车位又可以被出租,且有变为出租车位
                }
                //var_dump($insertSql);
            //设置不自动提交
                mysqli_query($connect,"SET AUTOCOMMIT=0");
                //开启事务
                mysqli_begin_transaction($connect);
                    if(!mysqli_query($connect,$insertSql)){
                        //回滚
                        mysqli_query($connect,"ROLLBACK");
                    }
                    if(!mysqli_query($connect,$updateSql)){
                         //回滚
                         mysqli_query($connect,"ROLLBACK");
                     }
                //执行事务
                mysqli_commit($connect);
            }
        }

     }

     mysqli_close($connect);