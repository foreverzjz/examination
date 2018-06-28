<?php
namespace DataMeta;
trait PromotionLog{

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
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $promotion_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $user_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $device_id;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=true)
     */
    public $app_version;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=true)
     */
    public $client_type;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=false)
     */
    public $order_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $school_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $shop_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $city_id;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=false)
     */
    public $goods_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $goods_num;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $day;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=true)
     */
    public $is_deleted;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $created_at;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $delete_at;
}
