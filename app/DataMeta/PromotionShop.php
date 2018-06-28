<?php
namespace DataMeta;
trait PromotionShop
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
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $promotion_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $promotion_name;

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
    public $promotion_type;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $promotion_begin_time;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $promotion_end_time;

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
    public $promotion_status;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $shop_id;

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
    public $promotion_city_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
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
    public $commercial_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    public $confirm_status;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $confirm_time;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
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
