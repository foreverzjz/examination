<?php
/**
 * Created by PhpStorm.
 * User: foreverzjz
 * Date: 2018/6/29
 * Time: 下午1:10
 */

namespace Controllers;

use Core\Base\Controller;
use Phalcon\Di;

class BaseController extends Controller
{
    public function initialize()
    {
        parent::initialize();
        parent::setJsonResponse();
    }

    public function getRequest()
    {
        return Di::getDefault()->get('input');
    }
}