<?php

namespace DoctrineProxies\__CG__\Entity;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class User extends \Entity\User implements \Doctrine\ORM\Proxy\Proxy
{
    private $_entityPersister;
    private $_identifier;
    public $__isInitialized__ = false;
    public function __construct($entityPersister, $identifier)
    {
        $this->_entityPersister = $entityPersister;
        $this->_identifier = $identifier;
    }
    /** @private */
    public function __load()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;

            if (method_exists($this, "__wakeup")) {
                // call this after __isInitialized__to avoid infinite recursion
                // but before loading to emulate what ClassMetadata::newInstance()
                // provides.
                $this->__wakeup();
            }

            if ($this->_entityPersister->load($this->_identifier, $this) === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            unset($this->_entityPersister, $this->_identifier);
        }
    }

    /** @private */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    
    public function getIs_exist()
    {
        $this->__load();
        return parent::getIs_exist();
    }

    public function setIs_exist($is_exist)
    {
        $this->__load();
        return parent::setIs_exist($is_exist);
    }

    public function getProvider_id()
    {
        $this->__load();
        return parent::getProvider_id();
    }

    public function getProvider_name()
    {
        $this->__load();
        return parent::getProvider_name();
    }

    public function getSignup_type()
    {
        $this->__load();
        return parent::getSignup_type();
    }

    public function setProvider_id($provider_id)
    {
        $this->__load();
        return parent::setProvider_id($provider_id);
    }

    public function setProvider_name($provider_name)
    {
        $this->__load();
        return parent::setProvider_name($provider_name);
    }

    public function setSignup_type($signup_type)
    {
        $this->__load();
        return parent::setSignup_type($signup_type);
    }

    public function getCreated_at()
    {
        $this->__load();
        return parent::getCreated_at();
    }

    public function getUpdated_at()
    {
        $this->__load();
        return parent::getUpdated_at();
    }

    public function setCreated_at($created_at)
    {
        $this->__load();
        return parent::setCreated_at($created_at);
    }

    public function setUpdated_at($updated_at)
    {
        $this->__load();
        return parent::setUpdated_at($updated_at);
    }

    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return (int) $this->_identifier["id"];
        }
        $this->__load();
        return parent::getId();
    }

    public function getName()
    {
        $this->__load();
        return parent::getName();
    }

    public function getEmail()
    {
        $this->__load();
        return parent::getEmail();
    }

    public function getDob()
    {
        $this->__load();
        return parent::getDob();
    }

    public function getImage()
    {
        $this->__load();
        return parent::getImage();
    }

    public function getPhone_no()
    {
        $this->__load();
        return parent::getPhone_no();
    }

    public function getRestaurant_name()
    {
        $this->__load();
        return parent::getRestaurant_name();
    }

    public function getPassword()
    {
        $this->__load();
        return parent::getPassword();
    }

    public function getUser_type()
    {
        $this->__load();
        return parent::getUser_type();
    }

    public function getOwner_id()
    {
        $this->__load();
        return parent::getOwner_id();
    }

    public function getConfirmation_token()
    {
        $this->__load();
        return parent::getConfirmation_token();
    }

    public function getConfirmation_sent_at()
    {
        $this->__load();
        return parent::getConfirmation_sent_at();
    }

    public function getConfirmed_at()
    {
        $this->__load();
        return parent::getConfirmed_at();
    }

    public function getPassword_reset_token()
    {
        $this->__load();
        return parent::getPassword_reset_token();
    }

    public function getPassword_reset_token_sent_at()
    {
        $this->__load();
        return parent::getPassword_reset_token_sent_at();
    }

    public function getPassword_confirmed_at()
    {
        $this->__load();
        return parent::getPassword_confirmed_at();
    }

    public function getStatus()
    {
        $this->__load();
        return parent::getStatus();
    }

    public function setId($id)
    {
        $this->__load();
        return parent::setId($id);
    }

    public function setName($name)
    {
        $this->__load();
        return parent::setName($name);
    }

    public function setEmail($email)
    {
        $this->__load();
        return parent::setEmail($email);
    }

    public function setDob($dob)
    {
        $this->__load();
        return parent::setDob($dob);
    }

    public function setImage($image)
    {
        $this->__load();
        return parent::setImage($image);
    }

    public function setPhone_no($phone_no)
    {
        $this->__load();
        return parent::setPhone_no($phone_no);
    }

    public function setRestaurant_name($restaurant_name)
    {
        $this->__load();
        return parent::setRestaurant_name($restaurant_name);
    }

    public function setPassword($password)
    {
        $this->__load();
        return parent::setPassword($password);
    }

    public function setUser_type($user_type)
    {
        $this->__load();
        return parent::setUser_type($user_type);
    }

    public function setOwner_id($owner_id)
    {
        $this->__load();
        return parent::setOwner_id($owner_id);
    }

    public function setConfirmation_token($confirmation_token)
    {
        $this->__load();
        return parent::setConfirmation_token($confirmation_token);
    }

    public function setConfirmation_sent_at($confirmation_sent_at)
    {
        $this->__load();
        return parent::setConfirmation_sent_at($confirmation_sent_at);
    }

    public function setConfirmed_at($confirmed_at)
    {
        $this->__load();
        return parent::setConfirmed_at($confirmed_at);
    }

    public function setPassword_reset_token($password_reset_token)
    {
        $this->__load();
        return parent::setPassword_reset_token($password_reset_token);
    }

    public function setPassword_reset_token_sent_at($password_reset_token_sent_at)
    {
        $this->__load();
        return parent::setPassword_reset_token_sent_at($password_reset_token_sent_at);
    }

    public function setPassword_confirmed_at($password_confirmed_at)
    {
        $this->__load();
        return parent::setPassword_confirmed_at($password_confirmed_at);
    }

    public function setStatus($status)
    {
        $this->__load();
        return parent::setStatus($status);
    }

    public function jsonSerialize()
    {
        $this->__load();
        return parent::jsonSerialize();
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'name', 'email', 'dob', 'image', 'phone_no', 'password', 'user_type', 'status', 'created_at', 'updated_at', 'provider_id', 'provider_name', 'signup_type', 'is_exist');
    }

    public function __clone()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            $class = $this->_entityPersister->getClassMetadata();
            $original = $this->_entityPersister->load($this->_identifier);
            if ($original === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            foreach ($class->reflFields as $field => $reflProperty) {
                $reflProperty->setValue($this, $reflProperty->getValue($original));
            }
            unset($this->_entityPersister, $this->_identifier);
        }
        
    }
}