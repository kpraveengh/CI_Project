<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once('factual/Factual.php');
require_once('factual/FactualTest.php');

class Test extends CI_Controller {

    function scanupc() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            $upc = $args['upc'];
            $factual = new Factual("QPGlQD5uElzu0toEklOWmcOUysxO5Hpa1Fy7pvaX", "Ran91UTDN7FGyRwRrlBurakog8ccoAxiOlnSPgu5");
            $tableName = "products-cpg-nutrition";
            $query = new FactualQuery;
            $query->field("upc")->equal($upc);
            $res = $factual->fetch($tableName, $query);
            $result = $res->getData();
            $image = $result[0]['image_urls'][0];
            $ingredients = $result[0]['ingredients'];
            echo json_encode(array(
                "image" => $image,
                "ingredients" => $ingredients));
            exit;
//Set error level -- best not to change this.
            error_reporting(E_ERROR);
//Run tests	
            $factualTest = new factualTest($key, $secret);
            $factualTest->setLogFile($logFile);
            $factualTest->test();
        }
    }

    function search() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            $name = $args['name'];
            $factual = new Factual("QPGlQD5uElzu0toEklOWmcOUysxO5Hpa1Fy7pvaX", "Ran91UTDN7FGyRwRrlBurakog8ccoAxiOlnSPgu5");
            $tableName = "products-cpg-nutrition";
            $query = new FactualQuery;
            $query->field("product_name")->beginsWith($name);
            $query->only("product_name,upc,category");
            $res = $factual->fetch($tableName, $query);
            $result = $res->getData();
            echo json_encode($result);
            exit;
//Set error level -- best not to change this.
            error_reporting(E_ERROR);
//Run tests	
            $factualTest = new factualTest($key, $secret);
            $factualTest->setLogFile($logFile);
            $factualTest->test();
        }
    }

}

?>
