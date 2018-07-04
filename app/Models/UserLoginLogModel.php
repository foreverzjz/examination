<?php

namespace Models;

use DataMeta\ExamUserLoginLog;
use Enumerations\CacheConst;
use Phalcon\Di;

class UserLoginLogModel extends ExamModelBase
{
    use ExamUserLoginLog;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("db_examination");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'exam_user_login_log';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ExamUserLoginLog[]|ExamUserLoginLog
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ExamUserLoginLog
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public function logout($uid)
    {
        if(empty($uid)){
            return true;
        }
        $db = $this->getWriteConnection();
        $result = $db->update($this->getSource(), ['sign_out'], [1], "uid={$uid}");
        return $result;
    }

    public function clearLoginCache($uid)
    {
        if(empty($uid)){
            return false;
        }
        $cache = Di::getDefault()->get(CacheConst::CACHE_CONNECTION);
        $loginUidKey = sprintf(CacheConst::LOGIN_UID, $uid);
        $prevLoginToken = $cache->get($loginUidKey);
        if($prevLoginToken !== false){
            $prevLoginTokenKey = sprintf(CacheConst::LOGIN_TOKEN, $prevLoginToken);
            $cache->delete($prevLoginTokenKey);
        }
        $cache->delete($loginUidKey);
        return true;
    }

    public function recordLoginLog($data)
    {
        $db = $this->getWriteConnection();
        return $db->insert($this->getSource(), array_values($data), array_keys($data));
    }
}
