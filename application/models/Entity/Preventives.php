<?php

namespace Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Preventives Model
 *
 * @Entity
 * @Table(name="Preventives") 
 */
class Preventives implements \JsonSerializable {

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
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    public $user_id;

        /**
     * @ManyToOne(targetEntity="Family_Members")
     * @JoinColumn(name="member_id", referencedColumnName="id", nullable=true)
     */
    public $member_id;
    
    /**
     *@created_at
     * @Column(type="datetime", nullable=true)
     */
    public $created_date;

    /**
     *@updated_at
     * @Column(type="datetime", nullable=true)
     */
    public $updated_date;
    
    
    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getUser_id() {
        return $this->user_id;
    }

    function getMember_id() {
        return $this->member_id;
    }

    function getCreated_date() {
        return $this->created_date;
    }

    function getUpdated_date() {
        return $this->updated_date;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setUser_id($user_id) {
        $this->user_id = $user_id;
    }

    function setMember_id($member_id) {
        $this->member_id = $member_id;
    }

    function setCreated_date($created_date) {
        $this->created_date = $created_date;
    }

    function setUpdated_date($updated_date) {
        $this->updated_date = $updated_date;
    }

    
    public function jsonSerialize() {
        $vars = get_object_vars($this);
        return $vars;
    }

}
