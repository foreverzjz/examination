<?php

namespace DataMeta;

trait ExamUser
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    public $id;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=false)
     */
    public $username;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $password;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=false)
     */
    public $salt;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=false)
     */
    public $realname;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    public $gender;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=false)
     */
    public $mp;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=true)
     */
    public $roles;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    public $status;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $create_at;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $update_at;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $delete_at;
}
