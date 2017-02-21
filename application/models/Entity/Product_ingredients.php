<?php

namespace Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Product_ingredients Model
 *
 * @Entity
 * @Table(name="Product_ingredients") 
 */
class Product_ingredients implements \JsonSerializable {

    /**
     * @Id
     * @Column(type="integer", nullable=false)
     * @GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ManyToOne(targetEntity="Ingredients")
     * @JoinColumn(name="ingredient_id", referencedColumnName="id")
     */
    public $ingredient_id;

    /**
     * @ManyToOne(targetEntity="Products")
     * @JoinColumn(name="product_id", referencedColumnName="id")
     */
    public $product_id;

    /**
     * @created_at
     * @Column(type="datetime", nullable=true)
     */
    public $created_at;

    /**
     * @updated_at
     * @Column(type="datetime", nullable=true)
     */
    public $updated_at;
    
    
    function getCreated_at() {
        return $this->created_at;
    }

    function getUpdated_at() {
        return $this->updated_at;
    }

    function setCreated_at($created_at) {
        $this->created_at = $created_at;
    }

    function setUpdated_at($updated_at) {
        $this->updated_at = $updated_at;
    }

    
    function getId() {
        return $this->id;
    }

    function getIngredient_id() {
        return $this->ingredient_id;
    }

    function getProduct_id() {
        return $this->product_id;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setIngredient_id($ingredient_id) {
        $this->ingredient_id = $ingredient_id;
    }

    function setProduct_id($product_id) {
        $this->product_id = $product_id;
    }

    public function jsonSerialize() {
        $vars = get_object_vars($this);
        return $vars;
    }

}
