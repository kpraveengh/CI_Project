<?php

namespace Entity;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Scan_history Model
 *
 * @Entity
 * @Table(name="Scan_history") 
 */
class Scan_history implements \JsonSerializable {

    /**
     * @Id
     * @Column(type="integer", nullable=false)
     * @GeneratedValue(strategy="AUTO")
     */
    public $id;

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

    /**
     * @product_name
     * @Column(type="text", nullable=true)
     */
    public $product_name;

    /**
     * @result
     * @Column(type="text", nullable=true)
     */
    public $result;

    /**
     * @unsafe_users
     * @Column(type="text", nullable=true)
     */
    public $unsafe_users;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    public $user_id;
       
    /**
     * @image
     * @Column(type="text", nullable=true)
     */
    public $image;
    
     /**
     * @history_type
     * @Column(type="text", nullable=true)
     */
    public $history_type;
    
     /**
     * @upc_code
     * @Column(type="text", nullable=true)
     */
    public $upc_code;
    
   /**
     * @code_type
     * @Column(type="text", nullable=true)
     */
    public $code_type;
    
    
    /**
     * @ManyToOne(targetEntity="Owner")
     * @JoinColumn(name="owner_id", referencedColumnName="id")
     */
    public $owner_id;
    
     /**
     * @ManyToOne(targetEntity="Chef")
     * @JoinColumn(name="chef_id", referencedColumnName="id")
     */
    public $chef_id;
    
    function getOwner_id() {
        return $this->owner_id;
    }

    function getChef_id() {
        return $this->chef_id;
    }

    function setOwner_id($owner_id) {
        $this->owner_id = $owner_id;
    }

    function setChef_id($chef_id) {
        $this->chef_id = $chef_id;
    }

    
    function getId() {
        return $this->id;
    }

    function getCreated_at() {
        return $this->created_at;
    }

    function getProduct_name() {
        return $this->product_name;
    }

    function getResult() {
        return $this->result;
    }

    function getUnsafe_users() {
        return $this->unsafe_users;
    }

    function getUser_id() {
        return $this->user_id;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setCreated_at($created_at) {
        $this->created_at = $created_at;
    }

    function setProduct_name($product_name) {
        $this->product_name = $product_name;
    }

    function setResult($result) {
        $this->result = $result;
    }

    function setUnsafe_users($unsafe_users) {
        $this->unsafe_users = $unsafe_users;
    }

    function setUser_id($user_id) {
        $this->user_id = $user_id;
    }
    function getUpdated_at() {
        return $this->updated_at;
    }

    function getImage() {
        return $this->image;
    }

    function getHistory_type() {
        return $this->history_type;
    }

    function getUpc_code() {
        return $this->upc_code;
    }

    function getCode_type() {
        return $this->code_type;
    }

    function setUpdated_at($updated_at) {
        $this->updated_at = $updated_at;
    }

    function setImage($image) {
        $this->image = $image;
    }

    function setHistory_type($history_type) {
        $this->history_type = $history_type;
    }

    function setUpc_code($upc_code) {
        $this->upc_code = $upc_code;
    }

    function setCode_type($code_type) {
        $this->code_type = $code_type;
    }

            
    public function jsonSerialize() {
        $vars = get_object_vars($this);
        return $vars;
    }

}
