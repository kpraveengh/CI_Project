
<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Social_login extends CI_Controller {

    public function index() {

        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->view("login");
    }

    public function checksignup() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            $email = $args['email'];
            $name = $args['name'];
            $provider_id = $args['provider_id'];
            $provider_name = $args['provider_name'];
            $signup_type = $args['signup_type'];
            $user_type = $args['user_type'];
            if ($user_type == 'normal') {
                $user = $em->getRepository("Entity\User")->findOneBy(array('email' => $email));
                if (empty($user)) {
                    $user = new Entity\User;
                    $user->setName($args['name']);
                    $user->setEmail($args['email']);
                    $user->setDob($args['dob']);
                    $user->setProvider_id($args['provider_id']);
                    $user->setProvider_name($args['provider_name']);
                    $user->setSignup_type($args['signup_type']);
                    $user->setUser_type('normal');
                    $user->setVerifycode(1);
                    $user->setStatus(1);
                    $user->setIs_exist('true');
                    $today = new DateTime();
                    $user->setCreated_at($today);
                    $user->setUpdated_at($today);
                    $em->persist($user);
                    $em->flush();
                    echo json_encode(array(
                        'response_code' => '200',
                        'response_message' => 'You are successfully Loggdin',
                        'is_exist' => 'true',
                        'user' => $user));
                } else {
                    echo json_encode(array(
                        'response_code' => '200',
                        'response_message' => 'You are successfully Loggdin',
                        'is_exist' => 'true',
                        'user' => $user));
                }
            } else if ($user_type == 'owner') {
                $owner = $em->getRepository("Entity\Owner")->findOneBy(array('email' => $email));
                if (empty($owner)) {
                    $owner = new Entity\Owner;
                    $owner->setName($args['name']);
                    $owner->setEmail($args['email']);
                    $owner->setProvider_id($args['provider_id']);
                    $owner->setProvider_name($args['provider_name']);
                    $owner->setSignup_type($args['signup_type']);
                    $owner->setUser_type('owner');
                    $owner->setVerifycode(1);
                    $owner->setStatus(1);
                    $today = new DateTime();
                    $owner->setCreated_at($today);
                    $owner->setUpdated_at($today);
                    $owner->setIs_exist('true');
                    $em->persist($owner);
                    $em->flush();
                    echo json_encode(array(
                        'response_code' => '200',
                        'response_message' => 'You are successfully Loggdin',
                        'is_exist' => 'true',
                        'user' => $owner));
                } else {
                    echo json_encode(array(
                        'response_code' => '200',
                        'response_message' => 'You are successfully Loggdin',
                        'is_exist' => 'true',
                        'user' => $owner));
                }
            }
        }
    }

    // check login for facebook and googl we are not using this method

    public function checklogin() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            $email = $args['email'];
            $name = $args['name'];
            $provider_id = $args['provider_id'];
            $provider_name = $args['provider_name'];
            $signup_type = $args['signup_type'];
            $user_type = $args['user_type'];
            if ($user_type == 'normal') {
                $user = $em->getRepository("Entity\User")->findOneBy(array('email' => $email));
                if (empty($user)) {
                    $obj = (object) array();
                    echo json_encode(array(
                        'response_code' => '200',
                        'response_message' => 'you are not existing user please signup',
                        'is_exist' => 'true',
                        'user' => $obj));
                } else {
                    echo json_encode(array(
                        'response_code' => '200',
                        'response_message' => 'User loggedin',
                        'is_exist' => 'true',
                        'user' => $user));
                }
            } else if ($user_type == 'owner') {
                $owner = $em->getRepository("Entity\Owner")->findOneBy(array('email' => $email));
                if (empty($owner)) {
                    $obj = (object) array();
                    echo json_encode(array(
                        'response_code' => '200',
                        'response_message' => 'you are not existing user please signup',
                        'is_exist' => 'true',
                        'user' => $obj));
                } else {
                    echo json_encode(array(
                        'response_code' => '200',
                        'response_message' => 'User loggedin',
                        'is_exist' => 'true',
                        'user' => $owner));
                }
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

}
