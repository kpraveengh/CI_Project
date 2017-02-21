<?php

namespace Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Pages Model
 *
 * @Entity
 * @Table(name="Pages") 
 */
class Pages implements \JsonSerializable {

    /**
     * @Id
     * @Column(type="integer", nullable=false)
     * @GeneratedValue(strategy="AUTO")
     */
    public $id;
    
    /**
     * @type
     * @Column(type="text", nullable=true)
     */
       public $type;    
   /**
     * @description
     * @Column(type="text", nullable=true)
     */
       public $description;  
 
       function getId() {
           return $this->id;
       }

       function getType() {
           return $this->type;
       }

       function getDescription() {
           return $this->description;
       }

       function setId($id) {
           $this->id = $id;
       }

       function setType($type) {
           $this->type = $type;
       }

       function setDescription($description) {
           $this->description = $description;
       }

       

          public function jsonSerialize() {
        $vars = get_object_vars($this);
        return $vars;
    }

}
