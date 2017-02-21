<?php

namespace Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Family_Members Model
 *
 * @Entity
 * @Table(name="Family_Members") 
 */
class Family_Members implements \JsonSerializable {

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
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    public $user_id;

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

    function getName() {
        return $this->name;
    }

    function getDob() {
        return $this->dob;
    }

    function getImage() {
        return $this->image;
    }

    function getUser_id() {
        return $this->user_id;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setDob($dob) {
        $this->dob = $dob;
    }

    function setImage($image) {
        $this->image = $image;
    }

    function setUser_id($user_id) {
        $this->user_id = $user_id;
    }

    public function jsonSerialize() {
        $vars = get_object_vars($this);
        return $vars;
    }

}
