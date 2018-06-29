<?php

namespace Models;

use DataMeta\ExamUser;
use Enumerations\CacheConst;
use Enumerations\UserConst;
use Phalcon\Di;

class UserModel extends ExamModelBase
{
    use ExamUser;
    CONST CACHE_CONNECTION = 'redis';

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return parent::TABLE_PRE.'user';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ExamUser[]|ExamUser
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ExamUser
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public function getInfoByUser($username)
    {
        if(empty($username)){
            return [];
        }
        $info = parent::findFirst("(`username`={$username} or `mp`={$username}) and delete_at is null");
        if(empty($info)){
            return [];
        }
        return $info->toArray();
    }

    public function preventCache($userId)
    {
        if(empty($userId)){
            return false;
        }
        $cache = Di::getDefault()->get(self::CACHE_CONNECTION);
        $cacheKey = sprintf(CacheConst::LOGIN_PASS_ERROR, $userId);
        $errTime = $cache->setIncr($cacheKey, 0);
        if($errTime > UserConst::LOGIN_MAX_ERROR_TIME){
            $cacheKeyBan = sprintf(CacheConst::LOGIN_BAN, $userId);
            $cache->set($cacheKeyBan, 1, 3600);
            $cache->delete($cacheKey);
            return false;
        }
        return true;
    }

    public function clearLoginCache($userId)
    {
        if(empty($userId)){
            return true;
        }
        $cache = Di::getDefault()->get(self::CACHE_CONNECTION);
        $cacheKey = sprintf(CacheConst::LOGIN_PASS_ERROR, $userId);
        $cache->delete($cacheKey);
        return true;
    }

    public function existBanKey($userId)
    {
        if(empty($userId)){
            return false;
        }
        $cache = Di::getDefault()->get(self::CACHE_CONNECTION);
        $cacheKey = sprintf(CacheConst::LOGIN_BAN, $userId);
        return $cache->existKey($cacheKey);
    }
}
