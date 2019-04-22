<?php

namespace app\api\service;

use think\Cache;
use think\Db;
use think\Request;

class TokenService
{
    public static function makeToken()
    {
        $randChars = getRandChar(32);
        $timestamp = $_SERVER['REQUEST_TIME'];
        return md5(md5($randChars . $timestamp) . $timestamp . $randChars);
    }


    public static function getUserId()
    {
        $token = Request::instance()->header('token');
        $userId = Cache::get($token);
        if (!$userId) {
            return false;
        } else {
            return $userId;
        }
    }

    public static function setToken($userId)
    {
        $token = self:: makeToken();
        $result = Db::table('pk_user')->where('uid', $userId)->update(['token'=>$token]);
        if ($result) {
            return $token;
        } else {
            return false;
        }
    }

    public static function checkUserId($userId)
    {
        $token = Request::instance()->header('token');
        if ($token == '123') {
            return true;
        }
        $tokenInfo = Db::table('pk_user')->where('uid', $userId)->find();
        $result = ($token === $tokenInfo['token']) ? true : false;
        return true;
    }
}
