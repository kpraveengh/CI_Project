<?php

namespace Entity;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Products Model
 *
 * @Entity
 * @Table(name="Products") 
 */
class Products implements \JsonSerializable {

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
     * @image
     * @Column(type="text", nullable=true)
     */
    public $image;

    /**
     * @qr_code
     * @Column(type="text", nullable=true)
     */
    public $qr_code;

    /**
     * @nfc_code
     * @Column(type="text", nullable=true)
     */
    public $nfc_code;

    /**
     * @upc_code
     * @Column(type="text", nullable=true)
     */
    public $upc_code;

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
     * @ManyToOne(targetEntity="Owner")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    public $user_id;

    /**
     * @ManyToOne(targetEntity="Chef")
     * @JoinColumn(name="chef_id", referencedColumnName="id")
     */
    public $chef_id;

    /**
     * @qr_image
     * @Column(type="text", nullable=true)
     */
    public $qr_image;
    
    function getQr_image() {
        return $this->qr_image;
    }

    function setQr_image($qr_image) {
        $this->qr_image = $qr_image;
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

    function getUpc_code() {
        return $this->upc_code;
    }

    function setUpc_code($upc_code) {
        $this->upc_code = $upc_code;
    }

    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getImage() {
        return $this->image;
    }

    function getQr_code() {
        return $this->qr_code;
    }

    function getNfc_code() {
        return $this->nfc_code;
    }

    function getUser_id() {
        return $this->user_id;
    }

    function getChef_id() {
        return $this->chef_id;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setImage($image) {
        $this->image = $image;
    }

    function setQr_code($qr_code) {
        $this->qr_code = $qr_code;
    }

    function setNfc_code($nfc_code) {
        $this->nfc_code = $nfc_code;
    }

    function setUser_id($owner_id) {
        $this->user_id = $owner_id;
    }

    function setChef_id($chef_id) {
        $this->chef_id = $chef_id;
    }

    public function jsonSerialize() {
        $vars = get_object_vars($this);
        return $vars;
    }

}
