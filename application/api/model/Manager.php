<?php

namespace app\api\model;

use think\Db;
use think\Model;

class Manager extends Model
{
    public function getManager($managerId)
    {
        $data = Db::table('pk_manager')->where('mg_id', $managerId)->find();
        return $data;
    }
}
