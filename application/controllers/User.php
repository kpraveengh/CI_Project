<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

    public function index() {
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->view("users/createuser");
    }

    function usersave() {
        $this->load->library('doctrine');
        $this->load->library('encrypt');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            $email = $args['email'];
            $user = $em->getRepository("Entity\User")->findOneBy(array('email' => $email));
            if (!empty($user)) {
                echo json_encode(array(
                    'response_code' => '500',
                    'response_message' => $email . ' already exist'));
            } else {
                $password = $args['password'];
                $password_confirmation = $args['password_confirmation'];
                if ($password == $password_confirmation) {
                    $user = new Entity\User;
                    $user->setName($args['name']);
                    $user->setEmail($args['email']);
                    $user->setPhone_no($args['phone_no']);
                    $user->setUser_type('normal');
                    $verify_code = mt_rand(100000, 999999);
                    $user->setVerifycode($verify_code);
                    $user->setStatus(FALSE);
                    $hashed_password = crypt($args['password']);
                    $user->setPassword($hashed_password);
                    $em->persist($user);
                    $em->flush();
                    $name = $user->getName();
                    $verifycode = $user->getVerifycode();

                    $success = $this->sendemail($email, $name, $verifycode);
                    if ($success) {
                        echo json_encode(array(
                            'response_code' => '200',
                            'response_message' => 'Please check your ' . $email . '  for verification code',
                            'user' => $user));
                        exit;
                    } else {
                        echo json_encode(array(
                            'response_code' => '500',
                            'response_message' => 'Email sending failed'));
                        exit;
                    }
                } else {
                    echo "Registration failed";
                }
            }
        } else {
            $this->load->helper('url');
            $this->load->helper('form');
            $password = $this->input->post('password');
            $confirm_password = $this->input->post('password_confirmation');
            if ($password == $confirm_password) {
                $user = new Entity\User;
                $user->setName($this->input->post('name'));
                $user->setEmail($this->input->post('email'));
                $user->setUser_type('normal');
                $hashed_password = crypt($password);
                $user->setPassword($hashed_password);
                $em = $this->doctrine->em;
                $em->persist($user);
                $em->flush();
                //echo json_encode($user);
                $this->load->view('users/userprofile', array(
                    'user' => $user,
                ));
            } else {
                echo "Registration failed";
            }
        }
    }

    function userprofile() {
        $this->load->library('doctrine');
        $this->load->helper('url');
        $this->load->helper('form');
        $version = $this->config->item('user_app_version');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            $userid = $args['user_id'];
            $user = $em->getRepository("Entity\User")->findOneBy(array('id' => $userid));
            $id = null;
            $preventive = $em->getRepository("Entity\Preventives")->findBy(array('user_id' => $userid, 'member_id' => $id));

            if (!empty($preventive)) {
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'No Preventive found',
                    'current_version' => $version,
                    'user' => $user,
                    'preventive_list' => $preventive));
            } else {
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'Profile fetched',
                    'current_version' => $version,
                    'user' => $user,
                    'preventive_list' => $preventive));
            }
        } else {
            $userid = $this->input->post('user_id');
            $user = $em->getRepository("Entity\User")->findOneBy(array('id' => $userid));
            if (!empty($user)) {
                $this->load->view('users/userprofile', array(
                    'user' => $user,
                ));
            }
        }
    }

    function editprofile() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        $userid = $this->input->post('id');
        $user = $em->getRepository("Entity\User")->findBy(array('id' => $userid));
//         echo $user[0]->getName();
        echo $user[0]->getDob();
        $this->load->helper('url');
        $this->load->helper('form');

        $this->load->view('users/editprofile', array(
            'user' => $user,
        ));
    }

    function updateuser() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            if (!empty($this->input->post('image'))) {
                $userid = $this->input->post('user_id');
                $user = $em->getRepository("Entity\User")->findOneBy(array('id' => $userid));
                $user->setName($this->input->post('name'));
                $user->setDob($this->input->post('dob'));
                $base64_str = $this->input->post('image');
                $image = base64_decode($base64_str);
                $image_name = time() . '.png';
                $path = "./uploads/users/" . $image_name;
                file_put_contents($path, $image);
                $uploadpath = "http://app.myallergyalert.com/uploads/users/" . $image_name;
                $user->setImage($uploadpath);
            } else {
                $args = json_decode(file_get_contents("php://input"), true);
                $userid = $args['user_id'];
                $user = $em->getRepository("Entity\User")->findOneBy(array('id' => $userid));
                $user->setName($args['name']);
                $user->setDob($args['dob']);
            }
            $em->persist($user);
            $em->flush();
            echo json_encode(array(
                'response_code' => '200',
                'response_message' => 'Profile has been successfully updated',
                'user' => $user));
        } else {
            $userid = $this->input->post('id');
            $user = $em->getRepository("Entity\User")->findBy(array('id' => $userid));
            $user->setName($this->input->post('name'));
            $user->setEmail($this->input->post('email'));
            $user->setUser_type('normal');
            $em = $this->doctrine->em;
            $em->persist($user);
            $em->flush();
            echo json_encode($user);

            $this->load->helper('url');
            $this->load->helper('form');
        }
    }

    public function addmember() {
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper(array('form', 'url'));
        $this->load->view("users/createmember");
    }

    public function createmember() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $members = new Entity\Family_Members;
            if (!empty($this->input->post('image'))) {
                $members->setName($this->input->post('name'));
                $members->setDob($this->input->post('dob'));
                $id = $this->input->post('user_id');
                $user = $em->getRepository("Entity\User")->findOneBy(array('id' => $id));
                $members->setUser_id($user);
                $base64_str = $this->input->post('image');
                $image = base64_decode($base64_str);
                $image_name = time() . '.png';
                $path = "./uploads/members/" . $image_name;
                file_put_contents($path, $image);
                $uploadpath = "http://app.myallergyalert.com/uploads/members/" . $image_name;
                $members->setImage($uploadpath);
            } else {
                $args = json_decode(file_get_contents("php://input"), true);
                $members->setName($args['name']);
                $members->setDob($args['dob']);
                $userid = $args['user_id'];
                $user = $em->getRepository("Entity\User")->findOneBy(array('id' => $userid));
                $members->setUser_id($user);
            }
            $em->persist($members);
            $em->flush();
            echo json_encode(array(
                'response_code' => '200',
                'response_message' => 'Profile fetched',
                'member' => $members));
        } else {
            $members = new Entity\Family_Members;
            $members->setName($this->input->post('name'));
            $members->setDob($this->input->post('dob'));
            $user = $em->getRepository("Entity\User")->findBy(array('id' => $this->input->post('user_id')));
            $members->setUser_id($user);
            if (!empty($_FILES['image']['name'])) {
                $config['upload_path'] = './uploads/members/';
                $config['allowed_types'] = 'gif|jpg|png';
                $config['max_size'] = '';
                $config['max_width'] = '';
                $config['max_height'] = '';
                $this->load->library('upload', $config);
                if (!$this->upload->do_upload('image')) {
                    $error = array('error' => $this->upload->display_errors());
                    echo "image uploding failed";
                } else {
                    $data = array('upload_data' => $this->upload->data());
                    $uploadpath = $config['upload_path'] . $data['upload_data']['file_name'];
                    $members->setImage($uploadpath);
                }
            }
        }
        $em->persist($members);
        $em->flush();
        echo json_encode(array(
            'response_code' => '200',
            'response_message' => 'Profile fetched',
            'member' => $members));
        $this->load->helper('url');
        $this->load->helper('form');
    }

    function memberprofile() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            $userid = $args['member_id'];
            $member = $em->getRepository("Entity\Family_Members")->findOneBy(array('id' => $userid));
            $preventive = $em->getRepository("Entity\Preventives")->findBy(array('member_id' => $userid));
            if (empty($preventive)) {
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'No preventive found',
                    'user' => $member,
                    'preventive_list' => $preventive));
            } else {
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'Profile fetched',
                    'user' => $member,
                    'preventive_list' => $preventive));
            }
        } else {
            $userid = $this->input->post('member_id');
            $member = $em->getRepository("Entity\Family_Members")->findOneBy(array('id' => $userid));
            if (!empty($member)) {
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'Profile fetched',
                    'user' => $member));
            }
            $this->load->helper('url');
            $this->load->helper('form');
        }
    }

//        function editmemberprofile() {
//    }

    function updatemember() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            if (!empty($this->input->post('image'))) {
                $memberid = $this->input->post('member_id');
                $member = $em->getRepository("Entity\Family_Members")->findOneBy(array('id' => $memberid));
                $member->setName($this->input->post('name'));
                $member->setDob($this->input->post('dob'));
                $base64_str = $this->input->post('image');
                $image = base64_decode($base64_str);
                $image_name = time() . '.png';
                $path = "./uploads/members/" . $image_name;
                file_put_contents($path, $image);
                $uploadpath = "http://app.myallergyalert.com/uploads/members/" . $image_name;
                $member->setImage($uploadpath);
            } else {
                $args = json_decode(file_get_contents("php://input"), true);
                $member = $em->getRepository("Entity\Family_Members")->findOneBy(array('id' => $args['member_id']));
                $member->setName($args['name']);
                $member->setDob($args['dob']);
            }
            $em->persist($member);
            $em->flush();
            echo json_encode(array(
                'response_code' => '200',
                'response_message' => 'Profile has been successfully updated',
                'member' => $member));
        } else {
            $memberid = $this->input->post('id');
            echo $memberid;
            $member = $em->getRepository("Entity\Family_Members")->findOneBy(array('id' => $memberid));
            $member->setName($this->input->post('name'));
            $member->setDob($this->input->post('dob'));
            $em->persist($member);
            $em->flush();
            echo json_encode($member);

            $this->load->helper('url');
            $this->load->helper('form');
        }
    }

    public function deletemember() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;

        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            $member_id = $args['member_id'];
            $member = $em->getRepository("Entity\Family_Members")->find(array('id' => $member_id));
            $preventives = $em->getRepository("Entity\Preventives")->findBy(array('member_id' => $member_id));
            if (!empty($preventives)) {
                foreach ($preventives as $entity) {
                    $em->remove($entity);
                    $em->flush();
                }
            }
            $em->remove($member);
            $em->flush();
            echo json_encode(array(
                'response_code' => '200',
                'response_message' => 'Member has been deleted successfully',
                'member' => $member));
        }
    }

    function allmembers() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            $userid = $args['user_id'];
            $members = $em->getRepository("Entity\Family_Members")->findBy(array('user_id' => $userid));
            if (!empty($members)) {
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'Member Lists',
                    'member' => $members));
            } else {
                echo json_encode(array(
                    'response_code' => '404',
                    'response_message' => 'No Members Found',
                    'member' => $members));
            }
        } else {
            $userid = $this->input->post('user_id');
            $members = $em->getRepository("Entity\Family_Members")->findAll(array('user_id' => $userid));
            if (!empty($members)) {
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'Profile fetched',
                    'member' => $members));
            }

            $this->load->helper('url');
            $this->load->helper('form');
        }
    }

    public function addprevent() {
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper(array('form', 'url'));
        $this->load->view("users/addprevent");
    }

    public function createpreventive() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            if ($args['user_type'] == 'user') {
                $user = $em->getRepository("Entity\User")->findOneBy(array('id' => $args['user_id']));
                $count = count($args['name']);
                $failed_preventive = '';
                for ($i = 0; $i < $count; $i++) {
                    $prevent = new Entity\Preventives;
                    $prevent->setUser_id($user);
                    $member_id = NULL;
                    $getpreventive = $em->getRepository("Entity\Preventives")->findOneBy(array('name' => $args['name'][$i], 'user_id' => $args['user_id'], 'member_id' => $member_id));
                    if (empty($getpreventive)) {
                        $prevent->setName($args['name'][$i]);
                    } else {
                        $failed_preventive .= $args['name'][$i] . ', ';
                        continue;
                    }
                    $em->persist($prevent);
                    $em->flush();
                }
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'Preventives saved',
                    'user' => $prevent));
            } else {
                $count = count($args['name']);
                $failed_preventive = '';
                for ($i = 0; $i < $count; $i++) {
                    $member_id = $args['member_id'];
                    $member = $em->getRepository("Entity\Family_Members")->findOneBy(array('id' => $member_id));
                    $prevent = new Entity\Preventives;
                    $prevent->setMember_id($member);
                    $prevent->setUser_id($member->getUser_id());
                    $getpreventive = $em->getRepository("Entity\Preventives")->findOneBy(array('name' => $args['name'][$i], 'member_id' => $args['member_id']));
                    if (empty($getpreventive)) {
                        $prevent->setName($args['name'][$i]);
                    } else {
                        $failed_preventive .= $args['name'][$i] . ', ';
                        continue;
                    }
                    $em->persist($prevent);
                    $em->flush();
                }
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'Preventive has been added successfully',
                    'member' => $prevent));
            }
        } else {
            $prevent = new Entity\Preventives;
            $prevent->setName($this->input->post('name'));
            $prevent->setMember_id($this->input->post('member_id'));
            $prevent->setUser_id($this->input->post('user_id'));
        }
        $em->persist($prevent);
        $em->flush();
        echo json_encode(array(
            'response_code' => '200',
            'response_message' => 'Preventives saved',
            'prevent' => $prevent));
        $this->load->helper('url');
        $this->load->helper('form');
    }

    public function deletepreventive() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            $preventive_id = $args['preventive_id'];
            $preventive = $em->getRepository("Entity\Preventives")->find($preventive_id);
            $em->remove($preventive);
            $em->flush();
            echo json_encode(array(
                'response_code' => '200',
                'response_message' => 'Preventive has been deleted successfully',
                'preventive' => $preventive));
        }
    }

    function scanhistory() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
//        $scan = new Entity\Scan_history;
//        $scan->setProduct_name('Scan-history');
//        $today = new DateTime();
//        $scan->setCreated_at($today);
//        $scan->setResult('Safe');
////        $unsafeusers = array("name1", "name2", "name3");
//        $scan->setUnsafe_users('["name1","name2","name3"]');
//        $scan->setUser_id('2');
//        $em->persist($scan);
//        $em->flush();

        $id = 4;
        $scanresult = $em->getRepository("Entity\Scan_history")->find($id);
        $raja = array();

        echo json_encode(array(
            'created_at' => $scanresult->getCreated_at(),
            'product_name' => $scanresult->getProduct_name(),
            'result' => "safe",
            'unsafe_users' => $scanresult->getUnsafe_users(),
            'user_id' => $scanresult->getUser_id(),
        ));
    }

    function forgotpassword() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            $email = $args['email'];
            $user = $em->getRepository("Entity\User")->findOneBy(array('email' => $args['email']));
            if (!empty($user)) {
                $name = $user->getName();
                $verify_code = mt_rand(100000, 999999);
                $user->setVerifycode($verify_code);
                $em->persist($user);
                $em->flush();
                $name = $user->getName();
                $verifycode = $user->getVerifycode();
                $success = $this->sendemail($email, $name, $verifycode);
                if ($success) {
                    echo json_encode(array(
                        'response_code' => '200',
                        'response_message' => 'Please check your ' . $email . '  for verification code',
                        'user' => $user));
                    exit;
                } else {
                    echo json_encode(array(
                        'response_code' => '500',
                        'response_message' => 'Email sending failed'));
                    exit;
                }
            } else {
                echo json_encode(array(
                    'response_code' => '404',
                    'response_message' => $email . '  is not registered'));
            }
        }
    }

    function setnewpassword() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            $email = $args['email'];
            $verifycode = $args['verifycode'];
            $user = $em->getRepository("Entity\User")->findOneBy(array('email' => $email, 'verifycode' => $verifycode));
            if (!empty($user)) {
                $password = $args['password'];
                $password_confirmation = $args['password_confirmation'];
                if ($password == $password_confirmation) {
                    $hashed_password = crypt($args['password']);
                    $user->setPassword($hashed_password);
                    $em->persist($user);
                    $em->flush();
                    echo json_encode(array(
                        'response_code' => '200',
                        'response_message' => 'Your password changed successfully',
                        'user' => $user));
                    exit;
                }
            } else {
                echo json_encode(array(
                    'response_code' => '404',
                    'response_message' => 'Please enter correct verify code'));
                exit;
            }
        }
    }

    public function checkmailexistnace($email) {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        $user = $em->getRepository("Entity\User")->findOneBy(array('email' => $email));
        if (empty($user)) {
            return true;
        } else {
            $obj = (object) array();
            echo json_encode(array(
                'response_code' => '404',
                'response_message' => $email . ' is  not registered'));
            exit;
        }
    }

    public function sendemail($email, $name, $verifycode) {
        // To send HTML mail, the Content-type header must be set

        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

        $this->load->library('email');
        $this->email->from('info@myallergyalert.com', 'My Allergy Alert');
        $this->email->to($email);
        $this->email->subject('My Allergy Alert Email Verification code');
        $message = '<html><body>';
        $message .= 'Hi <b>' . $name . '</b>,Please verify your email address so we know that its really you!.';
        $message .= ' <br>Enter this Verification code to Login into My Allergy Alert App.<br> ';
        $message .= '<h1>' . $verifycode . '</h1>';
        $message .= '</body></html>';

        $this->email->message($message);
        return $this->email->send();
    }

    public function verifyemail() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        $args = json_decode(file_get_contents("php://input"), true);
        $email = $args['email'];
        $verifycode = $args['verifycode'];
        $user = $em->getRepository("Entity\User")->findOneBy(array('email' => $email, 'verifycode' => $verifycode));
        if (!empty($user)) {
            $user->setStatus('1');
            $em->persist($user);
            $em->flush();
            echo json_encode(array(
                'response_code' => '200',
                'response_message' => 'Successfully verification completed ',
                'user' => $user));
        } else {
            echo json_encode(array(
                'response_code' => '404',
                'response_message' => 'Please enter valid verification code'));
            exit;
        }
    }

    public function sendpassword() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            $email = $args['email'];
            $user = $em->getRepository("Entity\User")->findOneBy(array('email' => $email));
            if (!empty($user)) {
                $name = $user->getName();
                $email = $user->getEmail();
                $seed = str_split($name . $email . '0123456789'); // and any other characters
                shuffle($seed); // probably optional since array_is randomized; this may be redundant
                $changedpassword = '';
                foreach (array_rand($seed, 6) as $k)
                    $changedpassword .= $seed[$k];

                $changepassword = $user->setPassword($changedpassword);
                $em->persist($user);
                $em->flush();
                $newpassword = $user->getPassword();

                //SENDING NEW GENERATED PASSWORD TO EMAIL
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

                $this->load->library('email');
                $this->email->from('info@myallergyalert.com', 'My Allergy Alert');
                $this->email->to($email);
                $this->email->subject('My Allergy Alert Account New Password');
                $message = '<html><body>';
                $message .= 'Hi <b>' . $name . '</b>,Please Login into My Allergy Alert App with this New Password.';
                $message .= 'Password <h1>' . $newpassword . '</h1>';
                $message .= '</body></html>';

                $this->email->message($message);
                $newpass = $this->email->send();
                if ($newpass) {
                    echo json_encode(array(
                        'response_code' => '200',
                        'response_message' => 'Please check your email for new password'));
                }
            } else {
                echo json_encode(array(
                    'response_code' => '404',
                    'response_message' => 'this email doesnt exist, please enter regsitered email'));
                exit;
            }
        }
    }

}
