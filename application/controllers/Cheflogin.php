
<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cheflogin extends CI_Controller {

    public function index() {

        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->view("owners/login");
    }

    public function checklogin() {

        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            $email = $args['email'];
            $password = $args['password'];
            $chefs = $em->getRepository("Entity\Chef")->findOneBy(array('email' => $email));
            $check_password = $chefs->getPassword();

            if (hash_equals($check_password, crypt($password, $check_password))) {
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'You are successfully Loggedin',
                    'chef' => $chefs));
            } else {
                echo json_encode(array(
                    'response_code' => '404',
                    'response_message' => 'Login Failed'));
            }
        } else {
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            $chefs = $em->getRepository("Entity\Chef")->findOneBy(array('email' => $email));
            $check_password = $owners->getPassword();

            if (hash_equals($check_password, crypt($password, $check_password))) {
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'You are successfully Loggedin',
                    'chef' => $chefs));
            } else {
                echo "login failed";
            }
        }
    }

    public function logout() {

        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            echo json_encode(array(
                'response_code' => '200',
                'response_message' => 'Successfully logged out.',
            ));
        } else {
            echo json_encode(array(
                'response_code' => '200',
                'response_message' => 'Successfully logged out.',
            ));
        }
    }

}
