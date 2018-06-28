<?php
namespace DataMeta;
trait Promotion
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
     * @Column(type="string", length=255, nullable=false)
     */
    public $promotion_name;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=false)
     */
    public $promotion_code;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $business_code;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    public $promotion_species;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    public $type;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $intro;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=true)
     */
    public $partner;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    public $scope;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $operator;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $operator_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $promotion_city_id;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $revocation;

    /**
     *
     * @var string
     * @Column(type="string", length=10, nullable=false)
     */
    public $platform;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $begin_time;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $end_time;

    /**
     *
     * @var integer
     * @Column(type="double", length=10, nullable=true)
     */
    public $limit_price;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $num_per_order;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $order_num_per_day;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=true)
     */
    public $promotion_status;
    

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=true)
     */
    public $is_actived;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=true)
     */
    public $is_deleted;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $created_at;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $updated_at;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $deleted_at;
}
