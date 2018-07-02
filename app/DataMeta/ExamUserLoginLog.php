<?php

namespace DataMeta;

trait ExamUserLoginLog
{

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $uid;

    /**
     *
     * @var string
     * @Column(type="string", length=30, nullable=false)
     */
    public $token;

    /**
     *
     * @var integer
     * @Column(type="integer", length=4, nullable=false)
     */
    public $type;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=true)
     */
    public $description;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    public $sign_out;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $login_time;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $update_time;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=true)
     */
    public $login_ip;

    /**
     *
     * @var string
     * @Column(type="string", length=128, nullable=true)
     */
    public $device_id;
}
