
<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ownerlogin extends CI_Controller {

    public function login() {

        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->view("login");
    }

    public function checklogin() {
        $this->load->library('doctrine');
        $version = $this->config->item('owner_app_version');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            $email = $args['email'];
            $password = $args['password'];
            //to check email existance
            $this->checkmailexistnace($email);
            $owners = $em->getRepository("Entity\Owner")->findOneBy(array('email' => $email));
            if (!empty($owners)) {
                $owner_password = $owners->getPassword();
                if (hash_equals($owner_password, crypt($password, $owner_password))) {
                    $status = $owners->getStatus();
                    if ($status == TRUE) {
                        echo json_encode(array(
                            'response_code' => '200',
                             'current_version' => '',
                            'response_message' => 'You are successfully Loggedin',
                            'current_version' => $version,
                            'user_type' => 'owner',
                            'user' => $owners));
                    } else {
                        echo json_encode(array(
                            'response_code' => '300',
                            'response_message' => 'Please verify your email',
                             'user' => $owners));
                    }
                } else {
                    echo json_encode(array(
                        'response_code' => '400',
                        'response_message' => 'Username or password doesnot matched.',
                        'user_type' => 'owner'));
                }
            } else {
                $chefs = $em->getRepository("Entity\Chef")->findOneBy(array('email' => $email));
                if (!empty($chefs)) {
                    $chef_password = $chefs->getPassword();
                    if (hash_equals($chef_password, crypt($password, $chef_password))) {
                        $chef = array();
                        $chef ['id'] = $chefs->getId();
                        $chef ['name'] = $chefs->getName();
                        $chef ['phone_no'] = $chefs->getPhone_no();
                        $chef ['email'] = $chefs->getEmail();
                        $chef ['user_type'] = 'chef';

                        echo json_encode(array(
                            'response_code' => '200',
                             'current_version' => '',
                            'response_message' => 'You are successfully Loggedin',
                            'current_version' => $version,
                            'user_type' => 'chef',
                            'user' => $chef));
                    } else {
                        echo json_encode(array(
                            'response_code' => '400',
                            'response_message' => 'Username or password doesnot matched.',
                            'user_type' => 'chef'));
                    }
                }
            }
        } else {
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            $owners = $em->getRepository("Entity\Owner")->findOneBy(array('email' => $email));
            if (!empty($owners)) {
                $owner_password = $owners->getPassword();
                if (hash_equals($owner_password, crypt($password, $owner_password))) {
                    echo json_encode(array(
                        'response_code' => '200',
                        'response_message' => 'You are successfully Loggedin',
                        'user_type' => 'owner',
                        'user' => $owners));
                } else {
                    echo "login failed";
                }
            } else {
                $chefs = $em->getRepository("Entity\Chef")->findOneBy(array('email' => $email));
                $chef_password = $chefs->getPassword();
                $usertype = $chefs->getUser_type();
                if (!empty($chefs)) {
                    if (hash_equals($chef_password, crypt($password, $chef_password))) {
                        echo json_encode(array(
                            'response_code' => '200',
                            'response_message' => 'You are successfully Loggedin',
                            'user_type' => 'chef',
                            'user' => $chefs));
                    } else {
                        echo "login failed";
                    }
                } else {
                    
                }
            }
        }
    }

    public function checkmailexistnace($email) {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        $owners = $em->getRepository("Entity\Owner")->findOneBy(array('email' => $email));
        $chefs = $em->getRepository("Entity\Chef")->findOneBy(array('email' => $email));
        if (!empty($owners) || !empty($chefs)) {
            return true;
        } else {
            echo json_encode(array(
                'response_code' => '404',
                'response_message' => 'Email does not existed'));
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
