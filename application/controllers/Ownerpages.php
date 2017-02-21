
<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Owner_pages extends CI_Controller {

    public function subscription() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $id=1;
            $result = $em->getRepository("Entity\Owner_pages")->find($id);
            echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'Subscription',
                 'description' => $result->getDescription()));
        }
}

 public function terms() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
             $id=2;
            $result = $em->getRepository("Entity\Owner_pages")->find($id);
            echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'TermsandConditions',
                    'description' => $result->getDescription()));
       }
}
 public function howitworks() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $id=3;
            $result = $em->getRepository("Entity\Owner_pages")->find($id);
            echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'How it works',
                    'description' => $result->getDescription()));
        }
}
}
