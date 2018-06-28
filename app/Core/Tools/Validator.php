<?php
/**
 * Created by PhpStorm.
 * User: Peter.pan
 * Date: 2017/5/14
 * Time: 0:28
 */

namespace Core\Tools;

use Phalcon\Di;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\InclusionIn;
use Phalcon\Validation\Validator\Regex;
use Phalcon\Validation\Validator\Between;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Url;
use Core\Tools\Validation\ArrayLengthBetween;

class Validator
{

    CONST RULE_SEPARATOR = '|';
    CONST RULE_KEY_PARAM_SEPARATOR = ':';
    CONST RULE_PARAMS_SEPARATOR = ',';

    protected $_inst = NULL;
    protected $_messages = [];

    public function __construct()
    {
        $this->_inst = new Validation();
    }

    public function validateAll($params)
    {
        $messages = $this->_inst->validate($params);
        if (count($messages)) {
            foreach ($messages as $message) {
                $flag = $message->getCode();
                $flag = empty($flag) ? ErrorHandler::ERROR_CODE_PARAM_VALIDATE : $flag;
                ErrorHandler::setErrorInfo($message->getMessage(), $flag);
                return FALSE;
            }
        }
        return TRUE;
    }

    public function reset()
    {
        $this->_inst = new Validation();
    }

    public function validateRequired($key, $field = NULL)
    {
        $itemName = is_null($field) ? "字段{{$key}}" : $field;
        $this->_inst->add($key, new PresenceOf(['message' => "{$itemName}不能为空"]));
        return $this;
    }

    public function validateInclusionIn($key, $range, $field = NULL)
    {
        $itemName = is_null($field) ? "字段{{$key}}" : $field;
        $rangeStr = implode(', ', $range);
        $this->_inst->add($key, new InclusionIn([
            'message' => "{$itemName}必须为{$rangeStr}",
            'domain' => $range,
        ]));
    }

    public function validateBetween($key, $min, $max, $field = NULL)
    {
        $itemName = is_null($field) ? "字段{{$key}}" : $field;
        if (is_numeric($min) && is_numeric($max)) {
            $this->_inst->add($key, new Between([
                'minimum' => $min,
                'maximum' => $max,
                'message' => "{$itemName}的值必须在[{$min}, {$max}]之间",
                'allowEmpty' => TRUE,
            ]));
        }
    }

    public function validateInteger($key, $field = NULL)
    {
        $itemName = is_null($field) ? "字段{{$key}}" : $field;
        $this->_inst->add($key, new Regex([
            'message' => "{$itemName}必须是整数",
            'pattern' => '/^[0-9]+$/',
            'allowEmpty' => TRUE,
        ]));
        return $this;
    }

    public function validateName($key, $field = NULL)
    {
        $itemName = is_null($field) ? "字段{{$key}}" : $field;
        $this->_inst->add($key, new Regex([
            'message' => "{$itemName}必须由汉字,字母或者数字组成",
            'pattern' => '/^[A-Za-z0-9\x{4e00}-\x{9fa5}·]+$/u',
        ]));
    }

    public function validateAccount($key, $field = NULL)
    {
        $itemName = is_null($field) ? "字段{{$key}}" : $field;
        $this->_inst->add($key, new Regex([
            'message' => "{$itemName}必须由汉字,字母或者数字组成",
            'pattern' => '/^[A-Za-z0-9\x{4e00}-\x{9fa5}]+$/u',
        ]));
    }

    public function validateStringLengthMax($key, $max, $field = NULL)
    {
        $itemName = is_null($field) ? "字段{{$key}}" : $field;
        if (intval($max)) {
            $this->_inst->add($key, new StringLength([
                'max' => (int)$max,
                'messageMaximum' => "{$itemName}长度不能超过{$max}个字符",
            ]));
        }
    }

    public function validateStringLengthMin($key, $min, $field = NULL)
    {
        $itemName = is_null($field) ? "字段{{$key}}" : $field;
        if (intval($min)) {
            $this->_inst->add($key, new StringLength([
                'min' => (int)$min,
                'messageMinimum' => "{$itemName}长度不能小于{$min}个字符",
            ]));
        }
    }

    public function validateStringLengthBetween($key, $min, $max, $field = NULL)
    {
        $itemName = is_null($field) ? "字段{{$key}}" : $field;
        if ($min > 0 && $max > 0 && (int)$min < (int)$max) {
            $this->_inst->add($key, new StringLength([
                'max' => (int)$max,
                'min' => (int)$min,
                'messageMaximum' => "{$itemName}长度不能超过{$max}个字符",
                'messageMinimum' => "{$itemName}长度不能小于{$min}个字符",
            ]));
        }
    }

    public function validateArrayLengthBetween($key, $min, $max, $field = NULL)
    {
        $itemName = is_null($field) ? "字段{{$key}}" : $field;
        $this->_inst->add($key, new ArrayLengthBetween([
            'max' => (int)$max,
            'min' => (int)$min,
        ]));
    }

    public function validateUrl($key, $field = NULL)
    {
        $itemName = is_null($field) ? "字段{{$key}}" : $field;
        $this->_inst->add($key, new Url([
            'message' => "{$itemName}必须是合法Url",
            'allowEmpty' => TRUE,
        ]));
    }

    public function validateRegex($key, $pattern, $field = NULL)
    {
        $itemName = is_null($field) ? "字段{{$key}}" : $field;
        $this->_inst->add($key, new Regex([
            'message' => "{$itemName}不合法",
            'pattern' => $pattern,
        ]));
    }
}