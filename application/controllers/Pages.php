
<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Pages extends CI_Controller {

    public function subscription() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $id = 1;
            $user_type = $this->input->get("user_type");
            if ($user_type == 'normal') {
                $result = $em->getRepository("Entity\Pages")->find($id);
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'Subscription',
                    'description' => $result->getDescription()));
            } else {
                $result = $em->getRepository("Entity\Owner_pages")->find($id);
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'Subscription',
                    'description' => $result->getDescription()));
            }
        }
    }

    public function terms() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $id = 2;
            $user_type = $this->input->get("user_type");
            if ($user_type == 'normal') {
                $result = $em->getRepository("Entity\Pages")->find($id);
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'Terms and Conditions',
                    'description' => $result->getDescription()));
            } else {
                $result = $em->getRepository("Entity\Owner_pages")->find($id);
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'Terms and Conditions',
                    'description' => $result->getDescription()));
            }
        }
    }

    public function howitworks() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $id = 3;
            $user_type = $this->input->get("user_type");
            if ($user_type == 'normal') {
                $result = $em->getRepository("Entity\Pages")->find($id);
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'How it Works',
                    'description' => $result->getDescription()));
            } else {
                $result = $em->getRepository("Entity\Owner_pages")->find($id);
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'How it Works',
                    'description' => $result->getDescription()));
            }
        }
    }

    function contact() {
        $this->load->library('doctrine');
        $this->load->library('encrypt');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            $contact = new Entity\Contactus;
            $contact->setName($args['name']);
            $contact->setEmail($args['email']);
            $contact->setPhone_no($args['phone_no']);
            $contact->setSubject($args['subject']);
            $contact->setDescription($args['description']);
//            $contact->setUser_type($args['user_type']);
            $today = new DateTime();
            $contact->setCreated_at($today);
            $em->persist($contact);
            $em->flush();

            $sender_name = $contact->getName();
            $sender_email = $contact->getEmail();
            $sender_phone = $contact->getPhone_no();
            $sender_subject = $contact->getSubject();
            $sender_description = $contact->getDescription();
//            $sender_type = $contact->getUser_type();
            //SENDING NEW GENERATED PASSWORD TO EMAIL
            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

            $this->load->library('email');
            $this->email->from('info@myallergyalert.com', 'My Allergy Alert');
            $this->email->to('rajamohan.technodrive@gmail.com');
            $this->email->subject($sender_subject);
            $message = '<html><body style="font-family: arial;">';
            $message .= '<b>Name :- </b> ' . $sender_name . '.<br>';
            $message .= '<b>Email :- </b> ' . $sender_email . '.<br>';
            $message .= '<b>Phone no :- </b> ' . $sender_phone . '.<br>';
//            $message .= 'Message from :-<b>' . $sender_type . '</b>.<br>';
            $message .= ' <b>Description :-</b></br>' . $sender_description . '.<br><br><br><br>';
            $message .= 'Thanks and Regards<br>' . $sender_name . '.';
            $message .= '</body></html>';

            $this->email->message($message);
            $msg = $this->email->send();
            if ($msg) {
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'Query Successfully Submitted'));
            } else {
                echo json_encode(array(
                    'response_code' => '500',
                    'response_message' => 'Something went wrong'));
            }
        } else {
            echo "Something went wrong";
        }
    }

}
