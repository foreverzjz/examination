<?php
namespace DataMeta;
trait PromotionShopGoods
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
     * @Column(type="integer", length=1, nullable=false)
     */
    public $shop_confirm_status;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $shop_confirm_time;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=false)
     */
    public $goods_id;

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
     * @var double
     * @Column(type="double", length=10, nullable=true)
     */
    public $costing;

    /**
     *
     * @var double
     * @Column(type="double", length=10, nullable=true)
     */
    public $selling_price;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $discount;

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
    public $stock;

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
