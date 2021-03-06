<?php
/**
 * Created by PhpStorm.
 * User: nixuan
 * Date: 16/3/22
 * Time: 下午4:56
 */

namespace Core\Tools;

use \Phalcon\Cache\Backend\Redis;

class MyRedis extends Redis
{
    const KEY_NOT_FOUNT = NULL;

    const DEFAULT_TTL = 10800;
    const FOREVER_TTL = -1;
    const TODAY_TTL = 0;
    private $prevDb = 0;
    private $currDb = 0;

    public function getObj(){
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        return $this->_redis;
    }
    
    public function setExpire($key, $ttl = 0)
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        if ($ttl == self::TODAY_TTL) {
            $ttl = strtotime(date('Y-m-d 23:59:59')) - time();
        }
        return $this->_redis->expire($key, $ttl);
    }

    public function existsKey($key)
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        return $this->_redis->exists($key);
    }

    public function get($keyName, $deserialize = FALSE)
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        $result = $this->_redis->get($keyName);
        if($result !== false){
            if ($deserialize) {
                $result = json_decode($result, TRUE);
            }
        }
        return $result;
    }

    public function mget($keys,$column="",$deserialize=false) {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        $cacheInfo = $this->_redis->mget($keys);
        $result = array();
        if($deserialize) {
            foreach($cacheInfo as $item) {
                if($item !== false) {
                    $item = json_decode($item,true);
                    if(!empty($column)) {
                        $result[$item[$column]] = $item;
                    } else {
                        $result[] = $item;
                    }
                }
            }
        } else {
            foreach ($cacheInfo as $item) {
                if($item !== false) {
                    $result[] = $item;
                }
            }
        }
        return $result;
    }

    public function mset($result,$ttl = -1) {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        foreach($result as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $result[$key] = json_encode($value);
            }
        }
        if($ttl < 0) {
            $this->_redis->mset($result);
        } else {
            if ($ttl == self::TODAY_TTL) {
                $ttl = strtotime(date('Y-m-d 23:59:59')) - time();
            }
            if ($ttl > 1) {
                foreach($result as $key => $value) {
                    $this->_redis->setex($key, $ttl, $value);
                }
            }
        }
    }
    
    public function set($key, $value, $ttl = -1)
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        if (strlen($key) && ($value !== NULL)) {
            if (is_array($value) || is_object($value)) {
                $value = json_encode($value);
            }
            if ($ttl < 0) {
                $this->_redis->set($key, $value);
            } else {
                if ($ttl == self::TODAY_TTL) {
                    $ttl = strtotime(date('Y-m-d 23:59:59')) - time();
                }
                if ($ttl > 1) {
                    $this->_redis->setex($key, $ttl, $value);
                }
            }
        }
    }

    public function incr($key)
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        $incrResult = $this->_redis->incr($key);
        return $incrResult;
    }

    public function decr($key)
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        return $this->_redis->decr($key);
    }

    public function incrBy($key, $incr,$ttl = -1)
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        $incrResult = $this->_redis->incrBy($key, $incr);
        if ($incrResult) {
            if ($ttl == 0) {
                $this->_redis->expire($key, strtotime(date('Y-m-d 23:59:59')) - time());
            } elseif ($ttl > 0) {
                $this->_redis->expire($key, $ttl);
            }
        }
        return $incrResult;
    }

    public function decrBy($key, $incr,$ttl = -1)
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        $decrResult = $this->_redis->decrBy($key, $incr);
        if ($decrResult) {
            if ($ttl == 0) {
                $this->_redis->expire($key, strtotime(date('Y-m-d 23:59:59')) - time());
            } elseif ($ttl > 0) {
                $this->_redis->expire($key, $ttl);
            }
        }
        return $decrResult;
    }

    public function setIncr($key, $ttl = -1)
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        $incrResult = $this->_redis->incr($key);
        if ($incrResult) {
            if ($ttl == 0) {
                $this->_redis->expire($key, strtotime(date('Y-m-d 23:59:59')) - time());
            } elseif ($ttl > 0) {
                $this->_redis->expire($key, $ttl);
            }
        }
        return $incrResult;
    }

    public function setDecr($key, $ttl = -1)
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        $decrResult = $this->_redis->decr($key);
        if ($decrResult) {
            if ($ttl == 0) {
                $this->_redis->expire($key, strtotime(date('Y-m-d 23:59:59')) - time());
            } elseif ($ttl > 0) {
                $this->_redis->expire($key, $ttl);
            }
        }
        return $decrResult;
    }

    public function delete($key)
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        return $this->_redis->delete($key);
    }

    public function hashLength($key)
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        return $this->_redis->hLen($key);
    }

    public function hashGet($key, $hashKey, $unSerialize = FALSE)
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        $val = $this->_redis->hGet($key, $hashKey);
        if ($unSerialize) {
            $val = json_decode($val, TRUE);
        }
        return $val;
    }

    public function hashIncrBy($key, $hashKey, $incr = 1)
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        return $this->_redis->hIncrBy($key, $hashKey, $incr);
    }

    public function hashSet($key, $hashKey, $value)
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        return $this->_redis->hSet($key, $hashKey, $value);
    }

    public function hashMultiSet($key, $map, $ttl = -1)
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        $mapVal = [];
        foreach ($map as $hashKey => $value) {
            if (is_array($value) || is_object($value)) {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE);
            }
            $mapVal[$hashKey] = $value;
        }
        $result = $this->_redis->hMSet($key, $mapVal);
        if ($result) {
            if ($ttl == 0) {
                $this->_redis->expire($key, strtotime(date('Y-m-d 23:59:59')) - time());
            } elseif ($ttl > 0) {
                $this->_redis->expire($key, $ttl);
            }
        }
        return $result;
    }


    public function hashMultiGet($key, $fields, $unserialize = FALSE)
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        $resultSet = $this->_redis->hMGet($key, $fields);
        if (!$resultSet) {
            return $resultSet;
        }
        if ($unserialize) {
            $newResultSet = [];
            foreach ($resultSet as $key => $row) {
                $newResultSet[$key] = json_decode($row, JSON_UNESCAPED_UNICODE);
            }
            return $newResultSet;
        }
        return $resultSet;
    }

    public function hashGetAll($key, $unSerialize = FALSE)
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        $resultSet = $this->_redis->hGetAll($key);
        if (!$resultSet) {
            return $resultSet;
        }
        return $resultSet;
    }

    public function hashDestroyField($key, $field)
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }

        if (is_array($field)) {
            array_unshift($field, $key);
            return call_user_func_array(array($this->_redis, 'hDel'), $field);
        } else {
            return $this->_redis->hDel($key, $field);
        }
    }

    public function zAdd($key, $score, $value)
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        return $this->_redis->zAdd($key, $score, $value);
    }

    public function zGetAll($key)
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        return $this->_redis->zRange($key, 0, -1);
    }

    public function zRevRangeByScore($key, $max, $min, $options)
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        return $this->_redis->zRevRangeByScore($key, $max, $min, $options);
    }

    public function zRangeByScore($key, $min, $max, $options)
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        return $this->_redis->zRangeByScore($key, $min, $max, $options);
    }

    public function zRemove($key, $member)
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        return $this->_redis->zDelete($key, $member);
    }

    public function zIncrBy($key, $member, $score)
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        return $this->_redis->zIncrBy($key, $score, $member);
    }

    public function zInter($key, $subKeys)
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        $weights = [1];
        $weights = array_pad($weights, count($subKeys), 0);
        return $this->_redis->zInter($key, $subKeys, $weights);
    }

    public function zUnion($key, $subKeys)
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        return $this->_redis->zUnion($key, $subKeys);
    }

    public function zScore($key, $member)
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        return $this->_redis->zScore($key, $member);
    }

    /**
     * zSize
     * @param $key
     * @return integer
     */
    public function zSize($key)
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        return $this->_redis->zCard($key);
    }

    public function destroy($key)
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        return $this->_redis->del($key);
    }

    public function selectDb($dbIndex)
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }

        if ($this->_redis->select($dbIndex)) {
            $this->prevDb = $this->currDb;
            $this->currDb = $dbIndex;
        }
    }

    public function backPreviousDb()
    {
        if (is_null($this->_redis)) {
            $this->_connect();
        }
        $this->_redis->select($this->prevDb);
    }
}