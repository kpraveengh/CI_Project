<?php

namespace Entity;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Owner Model
 *
 * @Entity
 * @Table(name="Owner") 
 */
class Owner implements \JsonSerializable {

    /**
     * @Id
     * @Column(type="integer", nullable=false)
     * @GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @name
     * @Column(type="text", nullable=false)
     */
    public $name;

    /**
     * @email
     * @Column(type="string", nullable=false, unique=true)
     */
    public $email;

    /**
     * @image
     * @Column(type="text", nullable=true)
     */
    public $image;

    /**
     * @phone_no
     * @Column(type="text", nullable=true)
     */
    public $phone_no;

    /**
     * @restaurant_name
     * @Column(type="text", nullable=true)
     */
    public $restaurant_name;

    /**
     * @password
     * @Column(type="text", nullable=false)
     */
    public $password;

    /**
     * @user_type
     * @Column(type="text", nullable=true)
     */
    public $user_type;

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
     * @provider_id
     * @Column(type="text", nullable=true)
     */
    public $provider_id;

    /**
     * @provider_name
     * @Column(type="text", nullable=true)
     */
    public $provider_name;

    /**
     * @signup_type
     * @Column(type="text", nullable=true)
     */
    public $signup_type;

    /**
     * @is_exist
     * @Column(type="text", nullable=true)
     */
    public $is_exist;

    /**
     * @verifycode
     * @Column(type="text", nullable=false)
     */
    public $verifycode;

    /**
     * @status
     * @Column(type="boolean", nullable=true)
     */
    public $status;
    
    
    function getVerifycode() {
        return $this->verifycode;
    }

    function getStatus() {
        return $this->status;
    }

    function setVerifycode($verifycode) {
        $this->verifycode = $verifycode;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    
    function getProvider_id() {
        return $this->provider_id;
    }

    function getProvider_name() {
        return $this->provider_name;
    }

    function getSignup_type() {
        return $this->signup_type;
    }

    function getIs_exist() {
        return $this->is_exist;
    }

    function setProvider_id($provider_id) {
        $this->provider_id = $provider_id;
    }

    function setProvider_name($provider_name) {
        $this->provider_name = $provider_name;
    }

    function setSignup_type($signup_type) {
        $this->signup_type = $signup_type;
    }

    function setIs_exist($is_exist) {
        $this->is_exist = $is_exist;
    }

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

    function getName() {
        return $this->name;
    }

    function getEmail() {
        return $this->email;
    }

    function getImage() {
        return $this->image;
    }

    function getPhone_no() {
        return $this->phone_no;
    }

    function getRestaurant_name() {
        return $this->restaurant_name;
    }

    function getPassword() {
        return $this->password;
    }

    function getUser_type() {
        return $this->user_type;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setImage($image) {
        $this->image = $image;
    }

    function setPhone_no($phone_no) {
        $this->phone_no = $phone_no;
    }

    function setRestaurant_name($restaurant_name) {
        $this->restaurant_name = $restaurant_name;
    }

    function setPassword($password) {
        $this->password = $password;
    }

    function setUser_type($user_type) {
        $this->user_type = $user_type;
    }

    public function jsonSerialize() {
        $vars = get_object_vars($this);
        return $vars;
    }

}
