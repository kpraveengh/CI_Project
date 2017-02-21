<?php

namespace Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Admin Model
 *
 * @Entity
 * @Table(name="Admin") 
 */
class Admin implements \JsonSerializable {

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
     * @name
     * @Column(type="text", nullable=false)
     */
    public $email;

    /**
     * @password
     * @Column(type="text", nullable=false)
     */
    public $password;

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

    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getEmail() {
        return $this->email;
    }

    function getCreated_at() {
        return $this->created_at;
    }

    function getUpdated_at() {
        return $this->updated_at;
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

    function setCreated_at($created_at) {
        $this->created_at = $created_at;
    }

    function setUpdated_at($updated_at) {
        $this->updated_at = $updated_at;
    }
    
    function getPassword() {
        return $this->password;
    }

    function setPassword($password) {
        $this->password = $password;
    }

    
    public function jsonSerialize() {
        $vars = get_object_vars($this);
        return $vars;
    }

}
