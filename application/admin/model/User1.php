<?php

namespace app\admin\model;

use think\Model;
use app\admin\Model\Carpos;
use think\Db;

class User extends Model
{
    protected $table = 'pk_user';
    public function getList($data){
        (isset($data['username']) && !empty($data['username'])) ? $where1['username'] = ['like', '%' . $data['username'] . '%']:$where1=[];
        (isset($data['phone']) && !empty($data['phone'])) ? $where2['phone'] = ['like', '%' . $data['phone'] . '%']:$where2=[];
        //(isset($data['manager']) && !empty($data['manager'])) ? $where4['mg_id'] = $data['manager'] : $where4=[];
//       $manager=$data['manager'];
//       $mg_id=$data['mg_id'];
       //$data['mg_name']=='admin'?$where5="FIND_IN_SET('$manager',rmg_id)":$where5="FIND_IN_SET('$mg_id',rmg_id)";
        if($data['mg_name']=='admin' && $data['manager']===''){
            $where4=[];
        }else if($data['mg_name']=='admin' && $data['manager']!==''){
            if(is_array($data['manager'])){
                $data['manager']=implode(',',$data['manager']);
                $where4['mg_id']=array('in',$data['manager']);
            }else{
                $where4['mg_id']=$data['manager'];
            }
        }else if($data['mg_name']!='admin'){
            $where4['mg_id']=$data['mg_id'];
        }
        $whererent1=[
            'uuuid'=>['neq',0],
            'style'=>['=',1],
            'types'=>['=',3],
            'status'=>['=',1],
//            'c.owner'=>['=',2],
        ];
        $whererent2=[
            'uuid'=>['neq',0],
            'style'=>['=',1],
            'types'=>['=',2],
            'status'=>['=',1],
//            'c.owner'=>['=',2],
        ];
//       $data['mg_name']=='admin'?$mg_id=$data['manager']:$mg_id=$data['mg_id'];
//        $whererent2=[];

        $page = Db::table('pk_carpos')
            ->where($where1)
            ->where($where2)
            ->where($where4)
            ->where($whererent1)
            ->whereOr(function ($query) use ($whererent2) {
                $query->where($whererent2);
            })
            ->group('uuid,uuuid,mg_id')
//            ->paginate(15,false,['query'=>$data])
            ->paginate(15,false,['query'=>$data]);
         $tesa=$page->all();
         foreach ($tesa as $keys=>$vals){
             $id=$vals['id'];
             $owner=Db::table('pk_carpos')->where('id',$id)->field('types,uuid,uuuid,mg_id')->find();
             if(!empty($owner)){
                    if($owner['types']==2){
                        //出租
                        $res=Db::table('pk_user')->where('uid',$owner['uuid'])->field('username,phone,plate')->find();
                        $res2=Db::table('pk_manager')->where('mg_id',$owner['mg_id'])->field('name')->find();
                        $tesa[$keys]['mg_name']=$res2['name'];
                        $tesa[$keys]['username']=$res['username'];
                        $tesa[$keys]['phone']=$res['phone'];
                        $tesa[$keys]['plate']=$res['plate'];
                    }else if($owner['types']==3){
                        $res=Db::table('pk_user')->where('uid',$owner['uuuid'])->field('username,phone,plate')->find();
                        $res2=Db::table('pk_manager')->where('mg_id',$owner['mg_id'])->field('name')->find();
                        $tesa[$keys]['mg_name']=$res2['name'];
                        $tesa[$keys]['username']=$res['username'];
                        $tesa[$keys]['phone']=$res['phone'];
                        $tesa[$keys]['plate']=$res['plate'];
                    }
                }
         }
//                $id=$item['id'];
//                $owner=Db::table('pk_carpos')->where('id',$id)->field('types,uuid,uuuid,mg_id')->find();
//                if(!empty($owner)){
//                    if($owner['types']==2){
//                        //出租
//                        $res=Db::table('pk_user')->where('uid',$owner['uuid'])->field('username,phone,plate')->find();
//                        $item['username']=$res['username'];
//                        $item['phone']=$res['phone'];
//                        $item['plate']=$res['plate'];
//                    }else if($owner['types']==3){
//                        $res=Db::table('pk_user')->where('uid',$owner['uuuid'])->field('username,phone,plate')->find();
//                        $item['username']=$res['username'];
//                        $item['phone']=$res['phone'];
//                        $item['plate']=$res['plate'];
//                    }
//                }


//                $item['mg_name']=Db::table('pk_manager')->where('mg_id',$owner['mg_id'])->value('name');
//                $item['mg_name']=$owner['name'];
//            });
        $pagelist = $page -> render();
        return ['page'=>$page,'pages'=>$tesa,'pagelist'=>$pagelist,'data'=>$data];
//        return $page;
    }

    public function add($data){
    	if(!empty($data['uid'])){
    		$data1  = $data['uid'];
    		unset($data['uid']);unset($data['/admin/user/rentadd_html']);
            $res = $this::where(['uid'=>$data1])->update($data);
            if(!empty($res)){
                return 2;
            }
    	}else{
    		unset($data['uid']);unset($data['/admin/user/rentadd_html']);
            $res = $this::insert($data);
            if(!empty($res)){
                return 1;
            }
    	}
    }

     //获取修改内容
    public function edit($id){
        $res = $this::where(['uid'=>$id])->find();
        if(!empty($res)){
            return $res;
        }
    }


    public function getpsList($data){
        (isset($data['username']) && !empty($data['username'])) ? $where1['username'] = ['like', '%' . $data['username'] . '%']:$where1=[];
        (isset($data['phone']) && !empty($data['phone'])) ? $where2['phone'] = ['like', '%' . $data['phone'] . '%']:$where2=[];
        //(isset($data['manager']) && !empty($data['manager'])) ? $where5['mg_id'] = $data['manager'] : $where5=[];
        $manager=$data['manager'];
        $mg_id=$data['mg_id'];
        /*$data['mg_name']=='admin'?$where6="FIND_IN_SET('$manager',mg_id)":$where6="FIND_IN_SET('$mg_id',mg_id)";*/
        if($data['mg_name']=='admin' && $data['manager']===''){
            $where4=[];
        }else if($data['mg_name']=='admin' && $data['manager']!==''){
            if(is_array($data['manager'])){
                $data['manager']=implode(',',$data['manager']);
                $where4['c.mg_id']=array('in',$data['manager']);
            }else{
                $where4['c.mg_id']=$data['manager'];
            }
        }else if($data['mg_name']!='admin'){
            $where4['c.mg_id']=$data['mg_id'];
        }
        /*$page = $this::order('uid desc')->where($where1)->where($where2)->where($where6)->paginate(20,false,['query'=>$data]);*/
//   $where5=[''];
        $page = $this::alias('u')
        ->join('__CARPOS__ c','c.uid=u.uid')
        ->order('u.uid desc')
            ->where($where1)
            ->where($where2)
            ->where($where4)
            ->where(['c.owner'=>2,'c.style'=>1])
            ->group('c.uid,c.mg_id')
            ->paginate(15,false,['query'=>$data])->each(function($item,$key){
                $id=$item['id'];
                $owner=Db::table('pk_carpos')->alias('c')
                    ->join('pk_manager m','c.mg_id=m.mg_id')
                    ->where(['c.id'=>$id])->field('m.name')->find();
                $item['mg_name']=$owner['name'];
            });
       //dump($page);
        //获得分页的页码列表信息 并 传递给模版：
        $pagelist = $page -> render();
        return ['page'=>$page,'pagelist'=>$pagelist,'data'=>$data];
    }

    public function psadd($data){
        if(!empty($data['uid'])){
            $data1  = $data['uid'];
            unset($data['uid']);unset($data['/admin/user/personsadd_html']);
            $res = $this::where(['uid'=>$data1])->update($data);
            if(!empty($res)){
                return 2;
            }
        }else{
            unset($data['uid']);unset($data['/admin/user/personsadd_html']);
            $res = $this::insert($data);
            if(!empty($res)){
                return 1;
            }
        }
    }

     //获取修改内容
    public function psedit($id){
        $res = $this::where(['uid'=>$id])->find();
        if(!empty($res)){
            return $res;
        }
    }
    

        public function getpList($data){
        (isset($data['username']) && !empty($data['username'])) ? $where1['username'] = ['like', '%' . $data['username'] . '%']:$where1=[];
        (isset($data['phone']) && !empty($data['phone'])) ? $where2['phone'] = ['like', '%' . $data['phone'] . '%']:$where2=[];
         //(isset($data['manager']) && !empty($data['manager'])) ? $where5['mg_id'] = $data['manager'] : $where5=[];
        /*$where3['type']=1;
        $where4['flag']=1;*/
        $data['mg_name']=='admin'?$where6['mg_id']=$data['manager']:$where6['mg_id']=$data['mg_id'];
        $page = $this::order('uid desc')->where($where1)->where($where2)->where($where6)->paginate(20,false,['query'=>$data]);
        //获得分页的页码列表信息 并 传递给模版：
        $pagelist = $page -> render();
        return ['page'=>$page,'pagelist'=>$pagelist,'data'=>$data];
    }


    public function padd($data){
        if(!empty($data['uid'])){
            $data1  = $data['uid'];
            unset($data['uid']);unset($data['/admin/user/personadd_html']);
            $res = $this::where(['uid'=>$data1])->update($data);
            if(!empty($res)){
                return 2;
            }
        }else{
            unset($data['uid']);unset($data['/admin/user/personadd_html']);
            $res = $this::insert($data);
            if(!empty($res)){
                return 1;
            }
        }
    }

     //获取修改内容
    public function pedit($id){
        $res = $this::where(['uid'=>$id])->find();
        if(!empty($res)){
            return $res;
        }
    }
}
