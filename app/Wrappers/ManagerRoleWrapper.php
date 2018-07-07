<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 2018/4/11
 * Time: 下午1:58
 */

namespace Wrappers;

use Core\Base\Wrapper;
use DataMeta\ManagerRoleMeta;

class ManagerRoleWrapper extends Wrapper
{
    use ManagerRoleMeta;

    public function __construct(array $data = NULL)
    {
        if (is_array($data)) {
            parent::setWrapperProperties($data);
        }
    }
}