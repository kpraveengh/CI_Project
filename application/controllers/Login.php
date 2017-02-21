
<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    public function index() {

        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->view("login");
    }

    public function checklogin() {

        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        $version = $this->config->item('user_app_version');
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            $email = $args['email'];
            $password = $args['password'];
            $user = $em->getRepository("Entity\User")->findOneBy(array('email' => $email));
            if (empty($user)) {
                echo json_encode(array(
                    'response_code' => '500',
                    'response_message' => 'This email doesnot exist'));
            } else {
                $check_password = $user->getPassword();
                if (hash_equals($check_password, crypt($password, $check_password))) {
                    $status = $user->getStatus();
                    if ($status == TRUE) {
                        echo json_encode(array(
                            'response_code' => '200',
                            'current_version' => $version,
                            'response_message' => 'You are successfully Loggedin',
                            'user' => $user));
                    } else {
                        echo json_encode(array(
                            'response_code' => '300',
                            'response_message' => 'Please verify your email',
                            'user' => $user));
                    }
                } else {
                    echo json_encode(array(
                        'response_code' => '500',
                        'response_message' => 'Password doesnot match'));
                }
            }
        } else {
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            $user = $em->getRepository("Entity\User")->findOneBy(array('email' => $email));
            $check_password = $user->getPassword();

            if (hash_equals($check_password, crypt($password, $check_password))) {
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'You are successfully Loggedin',
                    'user' => $user));
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
