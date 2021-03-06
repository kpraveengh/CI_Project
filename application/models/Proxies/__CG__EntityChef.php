<?php

namespace DoctrineProxies\__CG__\Entity;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class Chef extends \Entity\Chef implements \Doctrine\ORM\Proxy\Proxy
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

    public function getUser_type()
    {
        $this->__load();
        return parent::getUser_type();
    }

    public function setUser_type($user_type)
    {
        $this->__load();
        return parent::setUser_type($user_type);
    }

    public function getUser_id()
    {
        $this->__load();
        return parent::getUser_id();
    }

    public function setUser_id($user_id)
    {
        $this->__load();
        return parent::setUser_id($user_id);
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

    public function getPassword()
    {
        $this->__load();
        return parent::getPassword();
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

    public function setPassword($password)
    {
        $this->__load();
        return parent::setPassword($password);
    }

    public function jsonSerialize()
    {
        $this->__load();
        return parent::jsonSerialize();
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'name', 'email', 'image', 'phone_no', 'password', 'user_type', 'created_at', 'updated_at', 'user_id');
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