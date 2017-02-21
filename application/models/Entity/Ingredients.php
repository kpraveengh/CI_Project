<?php

namespace Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Ingredients Model
 *
 * @Entity
 * @Table(name="Ingredients") 
 */
class Ingredients implements \JsonSerializable {

    /**
     * @Id
     * @Column(type="integer", nullable=false)
     * @GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ingredient_name
     * @Column(type="text", nullable=false)
     */
    public $ingredient_name;
    
    function getId() {
        return $this->id;
    }

    function getIngredient_name() {
        return $this->ingredient_name;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setIngredient_name($ingredient_name) {
        $this->ingredient_name = $ingredient_name;
    }
    
    public function jsonSerialize() {
        $vars = get_object_vars($this);
        return $vars;
    }

}
