<?php

namespace Entity;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * User Model
 *
 * @Entity
 * @Table(name="User") 
 */
class User implements \JsonSerializable {

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
     * @dob
     * @Column(type="string", nullable=true)
     */
    public $dob;

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
     * @password
     * @Column(type="text", nullable=true)
     */
    public $password;

    /**
     * @user_type
     * @Column(type="text", nullable=true)
     */
    public $user_type;

    /**
     * @status
     * @Column(type="boolean", nullable=true)
     */
    public $status;

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
     * @verifycode
     * @Column(type="text", nullable=false)
     */
    public $verifycode;

    function getVerifycode() {
        return $this->verifycode;
    }

    function setVerifycode($verifycode) {
        $this->verifycode = $verifycode;
    }

    
    /**
     * @is_exist
     * @Column(type="text", nullable=true)
     */
    public $is_exist;

    function getIs_exist() {
        return $this->is_exist;
    }

    function setIs_exist($is_exist) {
        $this->is_exist = $is_exist;
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

    function setProvider_id($provider_id) {
        $this->provider_id = $provider_id;
    }

    function setProvider_name($provider_name) {
        $this->provider_name = $provider_name;
    }

    function setSignup_type($signup_type) {
        $this->signup_type = $signup_type;
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

    function getDob() {
        return $this->dob;
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

    function getOwner_id() {
        return $this->owner_id;
    }

    function getConfirmation_token() {
        return $this->confirmation_token;
    }

    function getConfirmation_sent_at() {
        return $this->confirmation_sent_at;
    }

    function getConfirmed_at() {
        return $this->confirmed_at;
    }

    function getPassword_reset_token() {
        return $this->password_reset_token;
    }

    function getPassword_reset_token_sent_at() {
        return $this->password_reset_token_sent_at;
    }

    function getPassword_confirmed_at() {
        return $this->password_confirmed_at;
    }

    function getStatus() {
        return $this->status;
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

    function setDob($dob) {
        $this->dob = $dob;
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

    function setOwner_id($owner_id) {
        $this->owner_id = $owner_id;
    }

    function setConfirmation_token($confirmation_token) {
        $this->confirmation_token = $confirmation_token;
    }

    function setConfirmation_sent_at($confirmation_sent_at) {
        $this->confirmation_sent_at = $confirmation_sent_at;
    }

    function setConfirmed_at($confirmed_at) {
        $this->confirmed_at = $confirmed_at;
    }

    function setPassword_reset_token($password_reset_token) {
        $this->password_reset_token = $password_reset_token;
    }

    function setPassword_reset_token_sent_at($password_reset_token_sent_at) {
        $this->password_reset_token_sent_at = $password_reset_token_sent_at;
    }

    function setPassword_confirmed_at($password_confirmed_at) {
        $this->password_confirmed_at = $password_confirmed_at;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    public function jsonSerialize() {
        $vars = get_object_vars($this);
        return $vars;
    }

}
