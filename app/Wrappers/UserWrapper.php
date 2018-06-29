<?php
/**
 * Created by PhpStorm.
 * User: foreverzjz
 * Date: 2018/6/29
 * Time: 下午1:40
 */

namespace Wrappers;

use Core\Base\Wrapper;
use DataMeta\ExamUser;

class UserWrapper extends Wrapper
{
    use ExamUser;

    public function __construct(array $data=NULL)
    {
        if(is_array($data)){
            parent::setWrapperProperties($data);
        }
    }
}