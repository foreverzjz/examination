<?php
/**
 * Created by PhpStorm.
 * User: nixuan
 * Date: 16/4/14
 * Time: 涓嬪崍5:19
 */

namespace Core\Tools;

class Pagination
{

    const ACTION_PULL = 0;
    const ACTION_PAGE = 1;

    const DESC = 'DESC';
    const ASC = 'ASC';

    public $action = self::ACTION_PULL;
    public $pageSize = 20;

    public $nextToken = '';
    public $prevToken = '';

    public $page = 0;
    public $total = 0;
    public $pageCount = 0;

    public $orderBy = self::DESC;
    public $orderByField = NULL;

    public function getOffset()
    {
        return $this->pageSize * $this->page;
    }

    public function clear()
    {
        $this->page = 0;
    }

}