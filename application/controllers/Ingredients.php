
<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ingredients extends CI_Controller {

    public function index() {

        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->view("login");
    }

    public function search() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            $ingredients = $args['ingredient'];
//            $result = $em->getRepository("Entity\Ingredients")->findBy(array('ingredient_name' => $ingredients));
            $product = $em->createQuery("SELECT u FROM \Entity\Ingredients u where u.ingredient_name LIKE '%$ingredients%'")->setMaxResults(10);
            $result = $product->getResult();
            echo json_encode(array(
                'response_code' => '200',
                'response_message' => 'Ingredients lists',
                'ingredient_list' => $result));
        } else {
            $ingredients = $this->input->post('ingredient');
            $product = $em->createQuery("SELECT u FROM \Entity\Ingredients u where u.ingredient_name LIKE '%$ingredients%'")->setMaxResults(10);
            $result = $product->getResult();
            echo json_encode(array(
                'response_code' => '200',
                'response_message' => 'Ingredients lists',
                'ingredient_list' => $result));
        }
    }

}
