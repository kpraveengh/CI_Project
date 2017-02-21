<?php

class Doctrine_Tools extends CI_Controller {

   public $em;
   public $platform;

    public function __construct() {
        parent::__construct();
        $this->load->library('doctrine');
        $this->em = $this->doctrine->em;
        /** @var $em \Doctrine\ORM\EntityManager */
        $platform = $this->em->getConnection()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');
        $platform->registerDoctrineTypeMapping('set', 'string');
    }
    

    function create() {
        $this->load->library('doctrine');
        echo 'Reminder: Make sure the tables do not exist already.<br />
		<form action="" method="POST">
		<input type="submit" name="action" value="Create Tables"><br /><br />';

        if ($this->input->post('action')) {
            $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
            $classes = $this->em->getMetadataFactory()->getAllMetadata();
            $schemaTool->createSchema($classes);
        }
    }
    function update() {
        $this->load->library('doctrine');
        echo '
		<form action="" method="POST">
		<input type="submit" name="action" value="Update Tables"><br /><br />';

        if ($this->input->post('action')) {
            $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
            $classes = $this->em->getMetadataFactory()->getAllMetadata();
            $schemaTool->updateSchema($classes, true);
        }
    }
    function drop() {
        $this->load->library('doctrine');
        echo 'Reminder: Make sure the tables do not exist already.<br />
		<form action="" method="POST">
		<input type="submit" name="action" value="Drop Tables"><br /><br />';

        if ($this->input->post('action')) {
            $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
            $classes = $this->em->getMetadataFactory()->getAllMetadata();
            $schemaTool->dropSchema($classes);
        }
    }
    

}
