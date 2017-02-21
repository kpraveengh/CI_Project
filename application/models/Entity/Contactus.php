<?php

namespace Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Contactus Model
 *
 * @Entity
 * @Table(name="Contactus") 
 */
class Contactus implements \JsonSerializable {

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
       public $name; 
       
    /**
     * @email
     * @Column(type="text", nullable=true)
     */
       public $email; 
    /**
     * @phone_no
     * @Column(type="integer", nullable=true)
     */
       public $phone_no; 
       
    /**
     * @subject
     * @Column(type="text", nullable=true)
     */
       public $subject;  
       
   /**
     * @description
     * @Column(type="text", nullable=true)
     */
       public $description;  
       
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

       function getCreated_at() {
           return $this->created_at;
       }

       function setCreated_at($created_at) {
           $this->created_at = $created_at;
       }

              
       function getUser_type() {
           return $this->user_type;
       }

       function setUser_type($user_type) {
           $this->user_type = $user_type;
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

       function getPhone_no() {
           return $this->phone_no;
       }

       function getSubject() {
           return $this->subject;
       }

       function getDescription() {
           return $this->description;
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

       function setPhone_no($phone_no) {
           $this->phone_no = $phone_no;
       }

       function setSubject($subject) {
           $this->subject = $subject;
       }

       function setDescription($description) {
           $this->description = $description;
       }

       
          public function jsonSerialize() {
        $vars = get_object_vars($this);
        return $vars;
    }

}
