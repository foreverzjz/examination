<?php

namespace Models;

use DataMeta\ExamUserLoginLog;

class UserLoginLog extends ExamModelBase
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

}
