<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    function _construct() {
        $this->load->library('session');
    }

    public function index() {
        $this->load->library('doctrine');
        $this->load->library('session');
        $em = $this->doctrine->em;
        $this->load->helper('url');
        $this->load->helper('form');
        if ($this->session->userdata('email')) {
            $users = $em->getRepository("Entity\User")->findBy(array(), array('id' => 'ASC'));
            $this->load->view("partials/header");
            $this->load->view("admin/index", array('users' => $users));
            $this->load->view("partials/footer");
        } else {
            redirect('/login');
        }
    }

    public function checklogin() {
        $this->load->library('doctrine');
        $this->load->library('session');
        $this->load->helper('url');
        $em = $this->doctrine->em;
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $admins = $em->getRepository("Entity\Admin")->findOneBy(array('email' => $email));
        $check_password = $admins->getPassword();
        if (hash_equals($check_password, crypt($password, $check_password))) {

            $this->session->set_userdata('email', $email);
            redirect('/admin/users');
        } else {
            redirect('/login');
        }
    }

    public function logout() {
        $this->load->library('doctrine');
        $this->load->library('session');
        $em = $this->doctrine->em;
        $this->load->helper('url');
        $newdata = array(
            'email' => '');
        $this->session->unset_userdata($newdata);
        $this->session->sess_destroy();

        redirect('/login');
    }

    public function users() {
        $this->load->library('doctrine');
        $this->load->library('session');
        $em = $this->doctrine->em;
        $this->load->helper('url');
        $this->load->helper('form');
        if ($this->session->userdata('email')) {
            $users = $em->getRepository("Entity\User")->findBy(array(), array('id' => 'ASC'));
            $this->load->view("partials/header");
            $this->load->view("admin/index", array('users' => $users));
            $this->load->view("partials/footer");
        } else {
            redirect('/login');
        }
    }

    public function owners() {
        $this->load->library('doctrine');
        $this->load->library('session');
        $em = $this->doctrine->em;
        $this->load->helper('url');
        $this->load->helper('form');
        if ($this->session->userdata('email')) {
            $owners = $em->getRepository("Entity\Owner")->findBy(array(), array('id' => 'ASC'));
            $this->load->view("partials/header");
            $this->load->view("admin/owners", array('owners' => $owners));
            $this->load->view("partials/footer");
        } else {
            redirect('/login');
        }
    }

    public function admins() {
        $this->load->library('doctrine');
        $this->load->library('session');
        $em = $this->doctrine->em;
        $this->load->helper('url');
        $this->load->helper('form');
        if ($this->session->userdata('email')) {
            $admins = $em->getRepository("Entity\Admin")->findBy(array(), array('id' => 'ASC'));
            $this->load->view("partials/header");
            $this->load->view("admin/admins", array('admins' => $admins));
            $this->load->view("partials/footer");
        } else {
            redirect('/login');
        }
    }

    function createnew() {
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('session');
        if ($this->session->userdata('email')) {
            $this->load->view("partials/header");
            $this->load->view("admin/createadmin");
            $this->load->view("partials/footer");
        } else {
            redirect('/login');
        }
    }

    function create() {
        $this->load->library('doctrine');
        $this->load->library('encrypt');
        $this->load->helper('url');
        $this->load->helper('form');
        $em = $this->doctrine->em;
        $this->load->library('session');
        if ($this->session->userdata('email')) {
            $admin = new Entity\Admin;
            $admin->setName($this->input->post('name'));
            $admin->setEmail($this->input->post('email'));
            $password = $this->input->post('password');
            $hashed_password = crypt($password);
            $admin->setPassword($hashed_password);
            $em->persist($admin);
            $em->flush();
            $this->load->view("admins/index");
            redirect('/admin/index');
        } else {
            redirect('/login');
        }
    }

    function adminprofile($adminid) {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('session');
        if ($this->session->userdata('email')) {
            $admins = $em->getRepository("Entity\Admin")->findOneBy(array('id' => $adminid));
            $this->load->view("partials/header");
            $this->load->view("admin/adminview", array('admins' => $admins));
            $this->load->view("partials/footer");
        } else {
            redirect('/login');
        }
    }

    function editadmin($adminid) {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('session');
        if ($this->session->userdata('email')) {
            $admin = $em->getRepository("Entity\Admin")->findOneBy(array('id' => $adminid));
            $this->load->view("partials/header");
            $this->load->view('admin/editadmin', array('admin' => $admin));
            $this->load->view("partials/footer");
        } else {
            redirect('/login');
        }
    }

    function updateadmin() {
        $this->load->library('doctrine');
        $this->load->helper('url');
        $this->load->helper('form');
        $em = $this->doctrine->em;
        $this->load->library('session');
        if ($this->session->userdata('email')) {
            $adminid = $this->input->post('id');
            $admin = $em->getRepository("Entity\Admin")->findOneBy(array('id' => $adminid));
            $admin->setName($this->input->post('name'));
            $admin->setEmail($this->input->post('email'));
            $em->persist($admin);
            $em->flush();
            redirect('admin/admins');
        } else {
            redirect('/login');
        }
    }

    public function deleteadmin($adminid) {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        $this->load->library('session');
        if ($this->session->userdata('email')) {
            $admins = $em->getRepository("Entity\Admin")->findOneBy(array('id' => $adminid));
            $em->remove($admins);
            $em->flush();
            redirect('admin/admins');
        } else {
            redirect('/login');
        }
    }

    function adduser() {
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('session');
        if ($this->session->userdata('email')) {
            $this->load->view("partials/header");
            $this->load->view("users/createuser");
            $this->load->view("partials/footer");
        } else {
            redirect('/login');
        }
    }

    function createuser() {
        $this->load->library('doctrine');
        $this->load->library('encrypt');
        $this->load->helper('url');
        $this->load->helper('form');
        $em = $this->doctrine->em;
        $this->load->library('session');
        if ($this->session->userdata('email')) {
            $user = new Entity\User;
            $user->setName($this->input->post('name'));
            $user->setEmail($this->input->post('email'));
            $password = $this->input->post('password');
            $hashed_password = crypt($password);
            $user->setPassword($hashed_password);
            $user->setVerifycode(1);           
            $type=$this->input->post('user_type');
             $user->setUser_type($type);
            $em->persist($user);
            $em->flush();
            redirect('/admin/users');
            die;
        } else {
            redirect('/login');
        }
    }

    function userprofile($userid) {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('session');
        if ($this->session->userdata('email')) {
            $users = $em->getRepository("Entity\User")->findOneBy(array('id' => $userid));
            $members = $em->getRepository("Entity\Family_Members")->findBy(array('user_id' => $userid));

            $preventives = $em->getRepository("Entity\Preventives")->findBy(array('user_id' => $userid, 'member_id' => NULL));
            $allPreventives = array();
            foreach ($members as $member) {
                $name = $member->getName();
                $id = $member->getId();
                $member_preventives = $em->getRepository("Entity\Preventives")->findBy(array('member_id' => $member->getId()));

                array_push($allPreventives, array('name' => $name, 'id' => $id, 'preventives' => $member_preventives));
            }
            $this->load->view("partials/header");
            $this->load->view("admin/userview", array('users' => $users, 'members' => $allPreventives, 'preventives' => $preventives));
            $this->load->view("partials/footer");
        } else {
            redirect('/login');
        }
    }

    function edituser($userid) {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('session');
        if ($this->session->userdata('email')) {
            $user = $em->getRepository("Entity\User")->findOneBy(array('id' => $userid));
            $this->load->view("partials/header");
            $this->load->view('admin/edituser', array('user' => $user));
            $this->load->view("partials/footer");
        } else {
            redirect('/login');
        }
    }

    function updateuser() {
        $this->load->library('doctrine');
        $this->load->helper('url');
        $this->load->helper('form');
        $em = $this->doctrine->em;
        $this->load->library('session');
        if ($this->session->userdata('email')) {
            $userid = $this->input->post('id');
            $user = $em->getRepository("Entity\User")->findOneBy(array('id' => $userid));
            $user->setName($this->input->post('name'));
            $user->setEmail($this->input->post('email'));
            $em->persist($user);
            $em->flush();
            redirect('admin/users');
        } else {
            redirect('/login');
        }
    }

    public function deleteuser($userid) {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        $this->load->library('session');
        if ($this->session->userdata('email')) {
            $user = $em->getRepository("Entity\User")->findOneBy(array('id' => $userid));
            $em->remove($user);
            $em->flush();
            redirect('admin/users');
        } else {
            redirect('/login');
        }
    }

    function ownerprofile($ownerid) {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('session');
        if ($this->session->userdata('email')) {
            $owner = $em->getRepository("Entity\Owner")->findOneBy(array('id' => $ownerid));
            $chefs = $em->getRepository("Entity\Chef")->findBy(array('user_id' => $ownerid));
            $products = $em->getRepository("Entity\Products")->findBy(array('user_id' => $ownerid));
            if (!empty($products)) {
                $productIngredients = $em->getRepository("Entity\Product_ingredients")->findBy(array('product_id' => $products));
            }

//            var_dump($productIngredients);exit;
//            foreach ($productIngredients as $productIngredient){
//                var_dump($productIngredient->getIngredient_id()->getIngredient_name());exit;
//            }
//            $owner_products = array();
//            foreach ($products as $product) {
//                $name = $product->getName();
//                $id = $product->getId();
//                $owner_pro_ing = $em->getRepository("Entity\Product_ingredients")->findBy(array('product_id' => $id));
//                array_push($owner_products, array('name' => $name, 'id' => $id, 'ingredients' => $owner_pro_ing));
//            }
            $this->load->view("partials/header");
            $this->load->view("admin/ownerview", array('owner' => $owner, 'chefs' => $chefs, 'products' => $products, 'productIngredients' => $productIngredients));
//            $this->load->view("admin/ownerview", array('owners' => $owners, 'chefs' => $chefs, 'products' => $products, 'owner_products' => $owner_products));
            $this->load->view("partials/footer");
        } else {
            redirect('/login');
        }
    }

    function editowner($ownerid) {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('session');
        if ($this->session->userdata('email')) {
            $owner = $em->getRepository("Entity\Owner")->findOneBy(array('id' => $ownerid));
            $this->load->view("partials/header");
            $this->load->view('admin/editowner', array('owner' => $owner));
            $this->load->view("partials/footer");
        } else {
            redirect('/login');
        }
    }

    function updateowner() {
        $this->load->library('doctrine');
        $this->load->helper('url');
        $this->load->helper('form');
        $em = $this->doctrine->em;
        $this->load->library('session');
        if ($this->session->userdata('email')) {
            $ownerid = $this->input->post('id');
            $owner = $em->getRepository("Entity\Owner")->findOneBy(array('id' => $ownerid));
            $owner->setName($this->input->post('name'));
            $owner->setEmail($this->input->post('email'));
            $em->persist($owner);
            $em->flush();
            redirect('admin/owners');
        } else {
            redirect('/login');
        }
    }

    public function deleteowner($ownerid) {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        $this->load->library('session');
        if ($this->session->userdata('email')) {
            $owner = $em->getRepository("Entity\Owner")->findOneBy(array('id' => $ownerid));
            $em->remove($owner);
            $em->flush();
            redirect('admin/owners');
        } else {
            redirect('/login');
        }
    }

}
