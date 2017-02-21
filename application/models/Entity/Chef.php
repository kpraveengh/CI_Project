<?php

namespace Entity;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Chef Model
 *
 * @Entity
 * @Table(name="Chef") 
 */
class Chef implements \JsonSerializable {

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
     * @password
     * @Column(type="text", nullable=false)
     */
    public $password;

    /**
     * @ManyToOne(targetEntity="Owner")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    public $user_id;

    /**
     * @user_type
     * @Column(type="text", nullable=false)
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

    function getUser_type() {
        return $this->user_type;
    }

    function setUser_type($user_type) {
        $this->user_type = $user_type;
    }

    function getUser_id() {
        return $this->user_id;
    }

    function setUser_id($user_id) {
        $this->user_id = $user_id;
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

    function getPassword() {
        return $this->password;
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

    function setPassword($password) {
        $this->password = $password;
    }

    public function jsonSerialize() {
        $vars = get_object_vars($this);
        return $vars;
    }

}
