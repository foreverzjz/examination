<?php

namespace Wrappers;

use Core\Base\Wrapper;
use DataMeta\ExamUser;

class ManagerWrapper extends Wrapper
{
    use ExamUser;

    private $banToResponse = [
        'password' => '',
        'salt' => '',
    ];

    public function __construct(array $data = NULL)
    {
        if (is_array($data)) {
            parent::setWrapperProperties($data);
        }
    }

    public function toResponseArray()
    {
        $arrAuth = $this->toArray();
        return array_diff_key($arrAuth, $this->banToResponse);
    }
}