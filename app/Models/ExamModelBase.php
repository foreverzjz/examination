<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 2017/9/12
 * Time: 下午4:02
 */

namespace Models;

use Core\Base\Model;
use Phalcon\Db;

class ExamModelBase extends Model
{
    const TABLE_PRE = 'exam_';

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("db_examination");
    }

    public function findRecords($fields, $con = [], $tableName)
    {
        $whereArr = [];
        $record = [];
        if (empty($fields)) {
            $fields = '*';
        }
        if (is_array($fields)) {
            $fields = trim(join(',', $fields), ',');
        } else {
            $fields = trim($fields, ',');
        }
        if (!empty($con)) {
            foreach ($con as $key => $value) {
                if ($value !== '') {
                    $whereArr[] = "`{$key}`='{$value}'";
                }
            }
        }
        $strWhere = "";
        if (!empty($whereArr)) {
            $strWhere = " where " . join(" and ", $whereArr);
        }
        $sql = "select {$fields} from " . $tableName . $strWhere;
        $db = $this->getReadConnection();
        $record = $db->fetchAll($sql, \PDO::FETCH_ASSOC);
        return $record;
    }

    public function findOneRecord($fields, $con = [],$tableName = NULL)
    {
        if(!$tableName)$tableName = $this->getSource();
        $whereArr = [];
        $record = [];
        if (empty($fields)) {
            $fields = '*';
        }
        if (is_array($fields)) {
            $fields = trim(join(',', $fields), ',');
        } else {
            $fields = trim($fields, ',');
        }
        if (!empty($con)) {
            foreach ($con as $key => $value) {
                if ($value !=='') {
                    $whereArr[] = "`{$key}`='{$value}'";
                }
            }
        }
        $strWhere = "";
        if (!empty($whereArr)) {
            $strWhere = " where " . join(" and ", $whereArr);
        }
        $sql = "select {$fields} from " . $tableName . $strWhere;
        $db = $this->getReadConnection();
        $record = $db->fetchAll($sql, \PDO::FETCH_ASSOC);
        return $record['0']?$record['0']:[];
    }

    public static function insertRecordOnDuplicate($tableName, $record, $duplicateFields = [])
    {
        $values = [];
        $fields = [];
        $bindParams = [];
        $updateFields = [];
        foreach ($record as $field => $value) {
            $fields[] = "`{$field}`";
            $values[] = ":{$field}";
            $bindParams[":{$field}"] = $value;
        }
        $values = '(' . implode(', ', $values) . ')';
        $fields = '(' . implode(', ', $fields) . ')';
        foreach ($duplicateFields as $field) {
            if (in_array($field, array_keys($record))) {
                $updateFields[] = "`{$field}`=:update{$field}";
                $bindParams[":update{$field}"] = $record[$field];
            }
        }
        $updateFields = implode(', ', $updateFields);
        $sql = "INSERT INTO `{$tableName}` {$fields} VALUES {$values} ON DUPLICATE KEY UPDATE {$updateFields}";
        try {
            $result = self::getConnection()->execute($sql, $bindParams);
        } catch (\PDOException $e) {
            return FALSE;
        }
        if ($result) {
            $record['id'] = (int)self::getConnection()->lastInsertId();
            return $record;
        }
        return FALSE;
    }

    /**
     * @param $data
     * @param $condition
     * @param $tableName
     * @return bool
     */
    public function updateCon($data,$condition,$tableName)
    {
        if(empty($data) || empty($condition)) {
            return false;
        }
        $fields = array_keys($data);
        $values = array_values($data);
        $db = $this->getWriteConnection();
        $result = $db->update($tableName,$fields,$values,$condition);
        
        return $result;
    }

    /**
     * @param $field
     * @param $type
     * @return string
     */
    public static function transformField($field, $type)
    {
        switch (strtoupper($type)) {
            case "SELECT":
                if (empty($field)) {
                    return '*';
                }
                if (is_array($field)) {
                    return trim(join(',', $field), ',');
                } else {
                    return trim($field, ',');
                }
                break;
            case "UPDATE":
                if (empty($field)) {
                    return '1=1';
                }
                if (is_array($field)) {
                    $conditionsArr = [];
                    foreach ($field as $key => $val) {
                        if (is_numeric($key)) {
                            $conditionsArr[] = "{$val}";
                        } else {
                            $conditionsArr[] = "{$key} = ?";
                        }
                    }
                    $ret['set'] = trim(implode(',', $conditionsArr), ',');
                    $ret['val'] = array_values($field);
                    return $ret;
                } else {
                    return trim($field, ',');
                }
                break;
        }
    }

    public function fetchOneRecord($con="", $fields="*")
    {
        if(empty($con)){
            return [];
        }
        $db = $this->getReadConnection();
        $sql = "select {$fields} from ".$this->getSource()." where {$con}";
        return $db->fetchOne($sql, \PDO::FETCH_ASSOC);
    }

    public function fetchRecords($con="", $fields="*")
    {
        if(empty($con)){
            return [];
        }
        $db = $this->getReadConnection();
        $sql = "select {$fields} from ".$this->getSource()." where {$con}";
        return $db->fetchAll($sql, \PDO::FETCH_ASSOC);
    }
}