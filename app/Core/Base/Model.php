<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 16/8/22
 * Time: 22:08
 */

namespace Core\Base;

use Core\Tools\ErrorHandler;
use \Phalcon\Di;
use Phalcon\Exception;
use Phalcon\Mvc\Model as PhalconModel;
use Phalcon\Exception as PhalconException;

/**
 * Class BaseModel
 * @property \Core\Tools\MyRedis $redis;
 * @package Models
 */
class Model extends PhalconModel
{
    const TABLE_PRE = '';
    const PRIMARY_CACHE = 'dbc:%s';

    protected static $tableName;
    protected static $safeDelete = FALSE;

    protected $Di;
    protected $config;
    public $errorMessage;

    public function onConstruct()
    {
        $this->Di = Di::getDefault();
        $this->config = $this->Di->get('config');
    }

    public function initialize()
    {
        $this->setReadConnectionService('dbslave');
        $this->setWriteConnectionService('db');

        $this->skipAttributes(
            [
                "create_at",
                "update_at",
            ]
        );
    }

//    public function getSource()
//    {
//        return self::TABLE_PRE . static::$tableName;
//    }

    public static function getCacheDetailKey()
    {
        return sprintf(self::PRIMARY_CACHE, get_called_class()::TABLE_NAME);
    }
    /*
        protected static function getConnection(string $connection = 'db')
        {
            return self::reconnect($connection);
        }

        protected static function reconnect(string $connection = 'db')
        {
            $connection = Di::getDefault()->get($connection);
            if (is_null($connection->getInternalHandler())) {
                $connection->connect();
            }
            return $connection;
        }
    */

    /**
     *
     */
    public function create($data = NULL, $whiteList = NULL)
    {
        try {
            $result = parent::create();
            if (!$result) {
                $messages = '';
                foreach ($this->getMessages() as $message) {
                    $messages .= $message;
                }
                ErrorHandler::setErrorInfo($messages, -80700);
                $backTrace = debug_backtrace();
                ErrorHandler::saveDebugLog($messages, "{$backTrace[0]['class']}::{$backTrace[0]['function']}()", "{$backTrace[0]['file']}:{$backTrace[0]['line']}");
                return FALSE;
            }
            return $result;
        } catch (PhalconException $e) {
            $param = [
                'self' => $this->toArray(),
                'data' => $data,
                'whiteList' => $whiteList,
            ];
            ErrorHandler::exception($e, $param);
            return FALSE;
        }
    }

    /**
     *
     */
    public function update($data = NULL, $whiteList = NULL)
    {
        try {
            if ($this->getModelsMetaData()->hasAttribute($this, 'update_at')) {
                $this->update_at = date('Y-m-d H:i:s');
            }

            $result = parent::update();
            if (!$result) {
                $messages = '';
                foreach ($this->getMessages() as $message) {
                    $messages .= $message;
                }
                $backTrace = debug_backtrace();
                ErrorHandler::saveDebugLog($messages, "{$backTrace[0]['class']}::{$backTrace[0]['function']}()", "{$backTrace[0]['file']}:{$backTrace[0]['line']}");
                return FALSE;
            }
            return $result;
        } catch (PhalconException $e) {
            $param = [
                'self' => $this->toArray(),
                'data' => $data,
                'whiteList' => $whiteList,
            ];
            ErrorHandler::exception($e, $param);
            return FALSE;
        }
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Model
     */
    public static function findFirst($parameters = NULL)
    {
        try {
            return parent::findFirst($parameters);
        } catch (PhalconException $e) {
            ErrorHandler::exception($e, $parameters);
            return FALSE;
        }
    }

    public static function findByPrimaryWithCache($primaryValue, $primaryKey = 'id', bool $forceReadDB = FALSE)
    {
        $redis = Di::getDefault()->get('redis');
        $result = $redis->hashGet(self::getCacheDetailKey(), $primaryValue, TRUE);
        if (!$result) {
            $record = parent::findFirst("{$primaryKey} = '{$primaryValue}'");
            if (!$record) {
                $result = [];
            } else {
                $result = $record->toArray();
                $redis->hashSet(self::getCacheDetailKey(), $primaryValue, $result);
            }
        }
        return $result;
    }

    /**
     * 检查是否重复
     * @param string $field 字段名
     * @param string $value 值
     * @param string $banConditions 检查时需排除的条件
     * @return bool
     */
    public static function checkUnique(string $field, string $value, string $banConditions = '')
    {
        $conditions = "{$field} = '{$value}'";
        if (!empty($banConditions)) {
            $conditions .= " AND {$banConditions}";
        }
        $result = parent::findFirst($conditions);
        if (!$result) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public static function findByUnique($uniqueValue, bool $forceReadDB = FALSE)
    {
        $uniqueFields = static::$uniqueFields;
        if (empty($uniqueFields)) {
            throw new \Exception('This table has no unique fields');
        }

        if ($forceReadDB) {
            $result = FALSE;
        } else {
            $result = self::getCache()->hashGet(self::getCacheDetailKey(), $uniqueValue, TRUE);
        }

        if ($result) {
            return $result;
        } else {
            $uniqueValues = explode('_', $uniqueValue);
            $conditions = [];
            foreach ($uniqueFields as $key => $field) {
                $conditions[$field] = $uniqueValues[$key];
            }
            if (static::$softDelete) {
                $conditions['deleted_at'] = NULL;
            }
            $record = self::findOneRecord($conditions);
            if ($record) {
                self::getCache()->hashSet(self::getCacheDetailKey(), $uniqueValue, $record);
            }
            return $record;
        }
    }

    public function updateRecord($record, $where)
    {
        $tableName =$this->getSource();
        $conditions = [];
        foreach ($record as $key => $value) {
            if (!$this->getModelsMetaData()->hasAttribute($this, $key)) {
                unset($record[$key]);
            }
        }
        foreach ($where as $field => $value) {
            $conditions[] = "{$field}={$value}";
        }
        $conditions = implode(' AND ', $conditions);
        return $this->getWriteConnection()->updateAsDict($tableName, $record, $conditions);
    }

    public function countRecords($conditions = NULL)
    {
        $tableName = $this->getSource();
        if (count($conditions)) {
            list($conditions, $bindParams) = self::_parseConditions($conditions);
        } else {
            $conditions = '1=1';
            $bindParams = NULL;
        }
        $sql = "SELECT count(`id`) FROM `{$tableName}` WHERE {$conditions}";
        $result = $this->getReadConnection()->fetchColumn($sql, $bindParams);
        return (int)$result;
    }

    public function updateByCondition($updateData, $whereCondition, $dataTypes = NULL)
    {

        $fields = [];
        $values = [];
        foreach ($updateData as $key => $value) {
            if ($this->getModelsMetaData()->hasAttribute($this, $key)) {
                $fields[] = $key;
                $values[] = $value;
            }
        }
        $db = $this->getWriteConnection();
        try {
            $updateResult = $db->update($this->getSource(), $fields, $values, $whereCondition, $dataTypes);
            if(!$updateResult){
                return FALSE;
            }
        } catch (\PDOException $e) {
            ErrorHandler::exception($e, $updateData);
        }
        return $db->affectedRows();
    }

    /**
     * 执行Phql查询
     * @param $PHQL
     * @param $param
     * @return mixed
     */
    public static function PhqlQuery($PHQL, $param)
    {
        $modelsManager = Di::getDefault()->get('modelsManager');
        $query = $modelsManager->createQuery($PHQL);
        try {
            $result = $query->execute($param);
        } catch (PhalconException $e) {
            ErrorHandler::exception($e, func_get_args());
            return FALSE;
        }
        return $result;
    }

    private static function _parseConditions($conditions = [])
    {
        $conditionsArr = [];
        $bindParams = [];
        if (is_array($conditions)) {
            foreach ($conditions as $field => $val) {
                $fieldOperation = explode('__', $field);
                if (count($fieldOperation) == 2) {
                    $field = $fieldOperation[0];
                    $operation = $fieldOperation[1];

                    if (is_array($val)) {
                        if ($operation === 'between') {
                            $conditionsArr[] = "`{$field}`>=:{$field}_min AND `{$field}`<=:{$field}_max";
                            $bindParams[":{$field}_min"] = $val[0];
                            $bindParams[":{$field}_max"] = $val[1];
                        }
                    } else {
                        if ($operation === 'gt') {
                            $conditionsArr[] = "`{$field}`>:{$field}";
                            $bindParams[":{$field}"] = $val;
                        } else if ($operation === 'ge') {
                            $conditionsArr[] = "`{$field}`>=:{$field}";
                            $bindParams[":{$field}"] = $val;
                        } else if ($operation === 'lt') {
                            $conditionsArr[] = "`{$field}`<:{$field}";
                            $bindParams[":{$field}"] = $val;
                        } else if ($operation === 'le') {
                            $conditionsArr[] = "`{$field}`<=:{$field}";
                            $bindParams[":{$field}"] = $val;
                        } else if ($operation === 'ne') {
                            if (is_null($val)) {
                                $conditionsArr[] = "`{$field}` <> NULL";
                            } else {
                                $conditionsArr[] = "`{$field}`<>:{$field}";
                                $bindParams[":{$field}"] = $val;
                            }
                        }
                    }
                } else {
                    if (is_numeric($field)) {
                        $conditionsArr[] = "{$val}";
                    } else if (is_null($val)) {
                        $conditionsArr[] = "`{$field}` IS NULL";
                    }
                    else if (is_array($val) && count($val)) {
                        $val = implode(",", $val);
                        $conditionsArr[] = "FIND_IN_SET(`{$field}` , :{$field})";
                        $bindParams[":{$field}"] = $val;
                    }
                    else {
                        $conditionsArr[] = "`{$field}`=:{$field}";
                        $bindParams[":{$field}"] = $val;
                    }
                }
            }

            $conditions = implode(' AND ', $conditionsArr);
        } else {
            $conditions = '1=1';
            $bindParams = NULL;
        }
        return [$conditions, $bindParams];
    }
}