<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Owner extends CI_Controller {

    public function index() {
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->view("owners/createowner");
    }

    function ownersave() {
        $this->load->library('doctrine');
        $this->load->library('encrypt');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            $password = $args['password'];
            $password_confirmation = $args['password_confirmation'];
            $email = $args['email'];
            $this->checkmailexistnace($email, 'owner');
            if ($password == $password_confirmation) {
                $owner = new Entity\Owner;
                $owner->setName($args['name']);
                $owner->setRestaurant_name($args['restaurant_name']);
                $owner->setPhone_no($args['phone_no']);
                $owner->setEmail($args['email']);
                $owner->setStatus(FALSE);
                $verify_code = mt_rand(100000, 999999);
                $owner->setVerifycode($verify_code);
                $owner->setUser_type('owner');
                $hashed_password = crypt($args['password']);
                $owner->setPassword($hashed_password);
                $em->persist($owner);
                $em->flush();

                $email = $owner->getEmail();
                $name = $owner->getName();
                $verifycode = $owner->getVerifycode();
                $success = $this->sendemail($email, $name, $verifycode);
                if ($success) {
                    echo json_encode(array(
                        'response_code' => '200',
                        'response_message' => 'Please check your ' . $email . '  for verification code',
                        'user' => $owner));
                } else {
                    echo json_encode(array(
                        'response_code' => '500',
                        'response_message' => 'Email sending failed'));
                    exit;
                }
            } else {
                echo json_encode(array(
                    'response_code' => '404',
                    'response_message' => 'Password and confirm password not matched.'));
            }
        } else {
            $password = $this->input->post('password');
            $confirm_password = $this->input->post('password_confirmation');
            $email = $this->input->post('email');
            $this->checkmailexistnace($email);
            if ($password == $confirm_password) {
                $owner = new Entity\Owner;
                $owner->setName($this->input->post('name'));
                $owner->setEmail($this->input->post('email'));
                $owner->setRestaurant_name($this->input->post('restaurant_name'));
                $owner->setPhone_no($this->input->post('phone_no'));
                $owner->setUser_type('owner');
                $hashed_password = crypt($password);
                $owner->setPassword($hashed_password);
                $em = $this->doctrine->em;
                $em->persist($owner);
                $em->flush();
            } else {
                echo "Registration failed";
                $this->load->view("owners/createowner");
            }
            $this->load->helper('url');
            $this->load->helper('form');
        }
    }

    function ownerprofile() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        $version = $this->config->item('app_version');
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            $ownerid = $args['user_id'];
            $type = $args['user_type'];
            if ($type == 'owner') {
                $owner = $em->getRepository("Entity\Owner")->findOneBy(array('id' => $ownerid));
                if (!empty($owner)) {
                    echo json_encode(array(
                        'response_code' => '200',
                        'response_message' => 'Profile fetched',
                        'current_version' => $version,
                        'user' => $owner));
                } else {
                    echo json_encode(array(
                        'response_code' => '404',
                        'response_message' => 'No results found.'));
                }
            } else {
                $chefs = $em->getRepository("Entity\Chef")->findOneBy(array('id' => $ownerid));
                $chef_result = array();
                $chef_result ['id'] = $chefs->getId();
                $chef_result ['name'] = $chefs->getName();
                $chef_result ['email'] = $chefs->getEmail();
                $chef_result ['phone_no'] = $chefs->getPhone_no();
                $chef_result ['image'] = $chefs->getImage();
                if (!empty($chef_result)) {
                    echo json_encode(array(
                        'response_code' => '200',
                        'response_message' => 'Profile fetched',
                        'user' => $chef_result));
                } else {
                    echo json_encode(array(
                        'response_code' => '404',
                        'response_message' => 'No results found.'));
                }
            }
        } else {
            $ownerid = $this->input->post('user_id');
            $owner = $em->getRepository("Entity\Owner")->findOneBy(array('id' => $ownerid));
            if (!empty($owner)) {
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'Profile fetched',
                    'user' => $owner));
            }

            $this->load->helper('url');
            $this->load->helper('form');
        }
    }

    function editownerprofile() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        $ownerid = $this->input->post('id');
        $owner = $em->getRepository("Entity\Owner")->findBy(array('id' => $ownerid));
        echo $owner[0]->getName();
        $this->load->helper('url');
        $this->load->helper('form');

        $this->load->view('owners/editownerprofile', array(
            'owner' => $owner,
        ));
    }

    function updateownerprofile() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            if (!empty($this->input->post('image'))) {
                $ownerid = $this->input->post('user_id');
                $user_type = $this->input->post('user_type');
                if ($user_type == 'chef') {
                    $owner = $em->getRepository("Entity\Chef")->findOneBy(array('id' => $ownerid));
                } else {
                    $owner = $em->getRepository("Entity\Owner")->findOneBy(array('id' => $ownerid));
                    $owner->setRestaurant_name($this->input->post('restaurant_name'));
                }
                $owner->setName($this->input->post('name'));
                $owner->setPhone_no($this->input->post('phone_no'));
                $base64_str = $this->input->post('image');
                $image = base64_decode($base64_str);
                $image_name = time() . '.png';
                $path = "./uploads/owners/" . $image_name;
                file_put_contents($path, $image);
                $uploadpath = "http://app.myallergyalert.com/uploads/owners/" . $image_name;
                $owner->setImage($uploadpath);
            } else {
                $args = json_decode(file_get_contents("php://input"), true);
                $ownerid = $args['user_id'];
                $user_type = $args['user_type'];
                if ($user_type == 'chef') {
                    $owner = $em->getRepository("Entity\Chef")->findOneBy(array('id' => $ownerid));
                } else {
                    $owner = $em->getRepository("Entity\Owner")->findOneBy(array('id' => $ownerid));
                    $owner->setRestaurant_name($args['restaurant_name']);
                }
                $owner->setName($args['name']);
                $owner->setPhone_no($args['phone_no']);
            }
            $em->persist($owner);
            $em->flush();
            if ($user_type == 'chef') {
                $chef_result = array();
                $chef_result ['id'] = $owner->getId();
                $chef_result ['name'] = $owner->getName();
                $chef_result ['email'] = $owner->getEmail();
                $chef_result ['phone_no'] = $owner->getPhone_no();
                $chef_result ['image'] = $owner->getImage();
                $result = $chef_result;
            } else {
                $result = $chef_result;
            }
            echo json_encode(array(
                'response_code' => '200',
                'response_message' => 'Profile has been successfully updated',
                'user' => $result));
        } else {
            $ownerid = $this->input->post('id');
            $owner = $em->getRepository("Entity\Owner")->findOneBy(array('id' => $ownerid));
            $owner->setName($this->input->post('name'));
            $owner->setPhone_no($this->input->post('phone_no'));
            $owner->setRestaurant_name($this->input->post('restaurant_name'));
            $owner->setUser_type('Owner');
            $em = $this->doctrine->em;
            $em->persist($owner);
            $em->flush();
            echo json_encode($owner);
            $this->load->helper('url');
            $this->load->helper('form');
        }
    }

    public function addchef() {
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper(array('form', 'url'));
        $this->load->view("owners/createchef");
    }

    public function createchef() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $chefs = new Entity\Chef;
            if (!empty($this->input->post('image'))) {
                $userid = $this->input->post('user_id');
                $owner = $em->getRepository("Entity\Owner")->findOneBy(array('id' => $userid));
                $check_email = $em->getRepository("Entity\Chef")->findOneBy(array('email' => $this->input->post('email')));
                $check_in_owner = $em->getRepository("Entity\Owner")->findOneBy(array('email' => $this->input->post('email')));
                if (empty($check_email) && empty($check_in_owner)) {
                    $chefs = new Entity\Chef;
                    $chefs->setName($this->input->post('name'));
                    $chefs->setEmail($this->input->post('email'));
                    $chefs->setPhone_no($this->input->post('phone_no'));
                    $verify_code = mt_rand(100000, 999999);
                    $chefs->setVerifycode($this->input->post($verify_code));
                    $password = $this->input->post('password');
                    $hashed_password = crypt($password);
                    $chefs->setPassword($hashed_password);
                    $chefs->setUser_id($owner);
                    $chefs->setUser_type('chef');
                    $base64_str = $this->input->post('image');
                    $image = base64_decode($base64_str);
                    $image_name = time() . '.png';
                    $path = "./uploads/chefs/" . $image_name;
                    file_put_contents($path, $image);
                    $uploadpath = "http://app.myallergyalert.com/uploads/chefs/" . $image_name;
                    $chefs->setImage($uploadpath);
                    $today = new DateTime();
                    $chefs->setCreated_at($today);
                    $em->persist($chefs);
                    $em->flush();
                    echo json_encode(array(
                        'response_code' => '200',
                        'response_message' => 'Chef has been added successfully',
                        'member' => $chefs));
                } else {
                    echo json_encode(array(
                        'response_code' => '500',
                        'response_message' => 'This email already existed'));
                }
            } else {
                $args = json_decode(file_get_contents("php://input"), true);
                $userid = $args['user_id'];
                $owner = $em->getRepository("Entity\Owner")->findOneBy(array('id' => $userid));
                $check_email = $em->getRepository("Entity\Chef")->findOneBy(array('email' => $args['email']));
                $check_in_owner = $em->getRepository("Entity\Owner")->findOneBy(array('email' => $args['email']));
                if (empty($check_email) && empty($check_in_owner)) {
                    $chefs = new Entity\Chef;
                    $chefs->setName($args['name']);
                    $chefs->setEmail($args['email']);
                    $chefs->setPhone_no($args['phone_no']);
                    $verify_code = mt_rand(100000, 999999);
                    $chefs->setVerifycode($verify_code);
                    $chefs->setUser_type('chef');
                    $chefs->setUser_id($owner);
                    $password = $args['password'];
                    $hashed_password = crypt($password);
                    $chefs->setPassword($hashed_password);
                    $today = new DateTime();
                    $chefs->setCreated_at($today);
                    $em->persist($chefs);
                    $em->flush();
                    echo json_encode(array(
                        'response_code' => '200',
                        'response_message' => 'Chef has been added successfully',
                        'member' => $chefs));
                } else {
                    echo json_encode(array(
                        'response_code' => '500',
                        'response_message' => 'This email already existed'));
                }
            }
        } else {
            $chef = new Entity\Chef;
            $chef->setName($this->input->post('name'));
            $chef->setEmail($this->input->post('email'));
            $chef->setPhone_no($this->input->post('phone_no'));
            $chef->setUser_type('chef');
            $password = $this->input->post('password');
            $hashed_password = crypt($password);
            $chef->setPassword($hashed_password);
            $em = $this->doctrine->em;
            $em->persist($chef);
            $em->flush();

            $this->load->helper('url');
            $this->load->helper('form');
        }
    }

    function chefprofile() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        $chefid = $this->input->get('chef_id');
        $chefs = $em->getRepository("Entity\Chef")->findOneBy(array('id' => $chefid));
        if (!empty($chefs)) {
            $chef_result = array();
            $chef_result ['id'] = $chefs->getId();
            $chef_result ['name'] = $chefs->getName();
            $chef_result ['email'] = $chefs->getEmail();
            $chef_result ['phone_no'] = $chefs->getPhone_no();
            $chef_result ['image'] = $chefs->getImage();

            echo json_encode(array(
                'response_code' => '200',
                'response_message' => 'Profile fetched',
                'current_version' => '',
                'chef' => $chef_result));
        } else {
            echo json_encode(array(
                'response_code' => '404',
                'response_message' => 'No result found.'));
        }
    }

    function updatechef() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            if (!empty($this->input->post('image'))) {
                $chefid = $this->input->post('chef_id');
                $chefs = $em->getRepository("Entity\Chef")->find(array('id' => $chefid));
                $chefs->setName($this->input->post('name'));
                $chefs->setPhone_no($this->input->post('phone_no'));
                $base64_str = $this->input->post('image');
                $image = base64_decode($base64_str);
                $image_name = time() . '.png';
                $path = "./uploads/chefs/" . $image_name;
                file_put_contents($path, $image);
                $uploadpath = "http://app.myallergyalert.com/uploads/chefs/" . $image_name;
                $chefs->setImage($uploadpath);
                $today = new DateTime();
                $chefs->setUpdated_at($today);
            } else {
                $args = json_decode(file_get_contents("php://input"), true);
                $chefid = $args['chef_id'];
                $chefs = $em->getRepository("Entity\Chef")->findOneBy(array('id' => $chefid));
                $chefs->setName($args['name']);
                $chefs->setPhone_no($args['phone_no']);
                $today = new DateTime();
                date_default_timezone_set('Asia/Kolkata');
                $chefs->setUpdated_at($today);
            }
            $em->persist($chefs);
            $em->flush();
            echo json_encode(array(
                'response_code' => '200',
                'response_message' => 'Profile has been successfully updated',
                'user' => $chefs));
        }
    }

    public function deletechef() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            $chefid = $args['id'];
            $ownerid = $args['user_id'];
            $products = $em->getRepository("Entity\Products")->findBy(array('chef_id' => $chefid));
            if (!empty($products)) {
                foreach ($products as $product) {
                    $productid = $product->getId();
                    $pro_ing = $em->getRepository("Entity\Product_ingredients")->findBy(array('product_id' => $productid));
                    foreach ($pro_ing as $pro) {
                        $em->remove($pro);
                        $em->flush();
                    }
                    $em->remove($product);
                    $em->flush();
                }
            }
            $chefs = $em->getRepository("Entity\Scan_history")->findBy(array('chef_id' => $chefid));
            foreach ($chefs as $chef) {
                $em->remove($chef);
                $em->flush();
            }
            $chefs = $em->getRepository("Entity\Chef")->findBy(array('id' => $chefid));
            $owners = $em->getRepository("Entity\Owner")->findBy(array('id' => $ownerid));
            if (!empty($chefs)) {
                foreach ($chefs as $entity) {
                    $em->remove($entity);
                    $em->flush();
                }
            }
            echo json_encode(array(
                'response_code' => '200',
                'response_message' => 'Chef has been deleted successfully'));
        } else {
            $chefs = $em->getRepository("Entity\Chef")->findBy(array('id' => 15));
            $owners = $em->getRepository("Entity\Owner")->findBy(array('id' => 1));
            echo json_encode($chefs);
            if (!empty($chefs)) {
                foreach ($chefs as $entity) {
                    $em->remove($entity);
                    $em->flush();
                }
            }
        }
    }

    function allchefs() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $ownerid = $this->input->get('user_id');
            $chefs = $em->getRepository("Entity\Chef")->findBy(array('user_id' => $ownerid));
            $count = count($chefs);
            $cheffs = array();
            foreach ($chefs as $chef) {
                $chef_result = array();
                $chef_result ['id'] = $chef->getId();
                $chef_result ['name'] = $chef->getName();
                $chef_result ['image'] = $chef->getImage();
                array_push($cheffs, $chef_result);
            }

            $pagination = array('max_page_size' => '1',
                'page_no' => '1',
                'per_page' => '5',
                'total_no_records' => $count);

            if (!empty($chefs)) {
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'chef Lists',
                    'chefs' => $cheffs,
                    'pagination' => $pagination));
            } else {
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'No chefs found',
                    'chefs' => $chefs));
            }
        } else {
            $ownerid = $this->input->post('user_id');
            $chefs = $em->getRepository("Entity\Chef")->findAll(array('user_id' => $ownerid));
            if (!empty($chefs)) {
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'Profile fetched',
                    'chefs' => $chefs));
            }

            $this->load->helper('url');
            $this->load->helper('form');
        }
    }

    public function addproduct() {
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper(array('form', 'url'));
        $this->load->view("owners/addproduct");
    }

    public function createproduct() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            if (!empty($this->input->post('image'))) {
                if ($this->input->post('user_type') == 'owner') {
                    $product = new Entity\Products;
                    $product->setName($this->input->post('name'));
                    $owner = $em->getRepository("Entity\Owner")->findOneBy(array('id' => $this->input->post('user_id')));
                    $product->setUser_id($owner);
                    $base64_str = $this->input->post('image');
                    $image = base64_decode($base64_str);
                    $image_name = time() . '.png';
                    $path = "./uploads/products/" . $image_name;
                    file_put_contents($path, $image);
                    $uploadpath = "http://app.myallergyalert.com/uploads/products/" . $image_name;
                    $product->setImage($uploadpath);
                    $today = new DateTime();
                    $product->setCreated_at($today);
                    $product->setUpdated_at($today);
                    $em->persist($product);
                    $em->flush();
                } else {
                    $chef = $em->getRepository("Entity\Chef")->findOneBy(array('id' => $this->input->post('user_id')));
                    $product = new Entity\Products;
                    $product->setName($this->input->post('name'));
                    $product->setUser_id($chef->getUser_id());
                    $product->setChef_id($chef);
                    $base64_str = $this->input->post('image');
                    $image = base64_decode($base64_str);
                    $image_name = time() . '.png';
                    $path = "./uploads/products/" . $image_name;
                    file_put_contents($path, $image);
                    $uploadpath = "http://app.myallergyalert.com/uploads/products/" . $image_name;
                    $product->setImage($uploadpath);
                    $today = new DateTime();
                    $product->setCreated_at($today);
                    $product->setUpdated_at($today);
                    $em->persist($product);
                    $em->flush();
                }
                $ingredients = $this->input->post('product_ingredients_attributes');
                foreach ($ingredients as $ingredient) {
                    $ingredientss = $em->getRepository("Entity\Ingredients")->findOneBy(array('id' => $ingredient));
                    $productid = $product->getId();
                    $product_ingredient = new Entity\Product_ingredients;
                    $chek_ing = $em->getRepository("Entity\Product_ingredients")->findBy(array('ingredient_id' => $ingredient, 'product_id' => $productid));
                    if (empty($chek_ing)) {
                        $product_ingredient->setIngredient_id($ingredientss);
                        $product_ingredient->setProduct_id($product);
                        $today = new DateTime();
                        $product_ingredient->setCreated_at($today);
                        $product_ingredient->setUpdated_at($today);
                        $em->persist($product_ingredient);
                        $em->flush();
                    }
                }
            } else {
                $args = json_decode(file_get_contents("php://input"), true);
                if ($args['user_type'] == 'owner') {
                    $product = new Entity\Products;
                    $product->setName($args['name']);
                    $today = new DateTime();
                    $product->setCreated_at($today);
                    $product->setUpdated_at($today);
                    $owner = $em->getRepository("Entity\Owner")->findOneBy(array('id' => $args['user_id']));
                    $product->setUser_id($owner);
                    $em->persist($product);
                    $em->flush();
                } else {
                    $chef = $em->getRepository("Entity\Chef")->findOneBy(array('id' => $args['user_id']));
                    $product = new Entity\Products;
                    $product->setName($args['name']);
                    $today = new DateTime();
                    $product->setCreated_at($today);
                    $product->setUpdated_at($today);
                    $product->setUser_id($chef->getUser_id());
                    $product->setChef_id($chef);
                    $em->persist($product);
                    $em->flush();
                }
                $ingredients = $args['product_ingredients_attributes'];

                foreach ($ingredients as $ingredient) {
                    $ingredientss = $em->getRepository("Entity\Ingredients")->findOneBy(array('id' => $ingredient));
                    $productid = $product->getId();
                    $product_ingredient = new Entity\Product_ingredients;
                    $chek_ing = $em->getRepository("Entity\Product_ingredients")->findBy(array('ingredient_id' => $ingredient, 'product_id' => $productid));
                    if (empty($chek_ing)) {
                        $product_ingredient->setIngredient_id($ingredientss);
                        $product_ingredient->setProduct_id($product);
                        $today = new DateTime();
                        $product_ingredient->setCreated_at($today);
                        $product_ingredient->setUpdated_at($today);
                        $em->persist($product_ingredient);
                        $em->flush();
                    }
                }
            }

            //Saving data into scan history
            $scan = new Entity\Scan_history;
            $scan->setProduct_name($product->getName());
            $today = new DateTime();
            $scan->setCreated_at($today);
            $scan->setUpdated_at($today);
            $scan->setResult("");
            $scan->setHistory_type("Created a product");
            $scan->setCode_type("");
            $scan->setUpc_code("");
            $scan->setImage($product->getImage());
            $scan->setUnsafe_users("");
            if ($args['user_type'] == 'owner' || $this->input->post('user_type') == 'owner') {
                $scan->setOwner_id($owner);
            } else {
                $scan->setChef_id($chef);
                $scan->setOwner_id($chef->getUser_id());
            }
            $em->persist($scan);
            $em->flush();

            echo json_encode(array(
                'response_code' => '200',
                'response_message' => 'Product has been added successfully',
                'products' => $product));
        }
    }

    public function product() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $allproductss = array();
            $product = $em->getRepository("Entity\Products")->findOneBy(array('id' => $this->input->get('product_id')));
            if (!empty($product)) {
                $pro = array();
                $pro['id'] = $product->getId();
                $pro['name'] = $product->getName();
                $pro['created_at'] = "";
                $pro['updated_at'] = "";
                $pro['image'] = $product->getImage();
                $pro['qr_code'] = "";
                $pro['nfc_code'] = "";
                $pro['ingredients'] = array();
                $ingredients = $em->getRepository("Entity\Product_ingredients")->findBy(array('product_id' => $product->getId()));

                foreach ($ingredients as $ingredient) {
                    $ing = array();
                    $ing['id'] = $ingredient->getIngredient_id()->getId();
                    $ing['ingredient_name'] = $ingredient->getIngredient_id()->getIngredient_name();
                    $ing['product_name'] = $ingredient->getProduct_id()->getName();
                    $ing['user_id'] = "";
                    $ing['member_id'] = "";
                    $ing['created_at'] = "";
                    $ing['updated_at'] = "";
                    array_push($pro['ingredients'], $ing);
                }
                $pro['user'] = array();
                $pro['user']['id'] = $product->getUser_id()->getId();
                $pro['user']['name'] = $product->getUser_id()->getName();
            }

            if (!empty($product)) {
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'Product Deatils Fetched ',
                    'product' => $pro));
            } else {
                echo json_encode(array(
                    'response_code' => '404',
                    'response_message' => 'No Products found',
                    'product' => "",
                    'pagination' => ""));
            }
        }
    }

    public function updateproduct() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            if ($this->input->post('user_type') == 'owner' || $args['user_type'] == "owner") {
                if (!empty($this->input->post('image'))) {
                    $pro_id = $this->input->post('product_id');
                    $user_id = $this->input->post('user_id');
                    $products = $em->getRepository("Entity\Products")->findOneBy(array('id' => $pro_id));
                    if (!empty($products)) {
                        $owner = $em->getRepository("Entity\Owner")->findOneBy(array('id' => $user_id));
                        $products->setUser_id($owner);
                        $products->setName($this->input->post('name'));
                        $base64_str = $this->input->post('image');
                        $image = base64_decode($base64_str);
                        $image_name = time() . '.png';
                        $path = "./uploads/products/" . $image_name;
                        file_put_contents($path, $image);
                        $uploadpath = "http://app.myallergyalert.com/uploads/products/" . $image_name;
                        $products->setImage($uploadpath);
                        $today = new DateTime();
                        $products->setUpdated_at($today);
                        $all_ing = $em->getRepository("Entity\Product_ingredients")->findBy(array('product_id' => $pro_id));
                        if (!empty($all_ing)) {
                            foreach ($all_ing as $ing) {
                                $em->remove($ing);
                                $em->flush();
                            }
                        }
                        $ingredients = $this->input->post('product_ingredients_attributes');
                        foreach ($ingredients as $ingredient) {
                            $ingredient = $em->getRepository("Entity\Ingredients")->findOneBy(array('id' => $ingredient));
                            $check_ing = $em->getRepository("Entity\Product_ingredients")->findOneBy(array('product_id' => $pro_id, 'ingredient_id' => $ingredient));
                            $pro_ing = new Entity\Product_ingredients;
                            if (empty($check_ing)) {
                                $pro_ing->setProduct_id($products);
                                $pro_ing->setIngredient_id($ingredient);
                                $today = new DateTime();
                                $pro_ing->setCreated_at($today);
                                $pro_ing->setUpdated_at($today);
                                $em->persist($pro_ing);
                                $em->flush();
                            }
                        }
                        $em->persist($products);
                        $em->flush();
                        echo json_encode(array(
                            'response_code' => '200',
                            'response_message' => 'Product has been updated successfully'));
                    } else {
                        echo json_encode(array(
                            'response_code' => '404',
                            'response_message' => 'Something went wrong'));
                    }
                } else {
                    $args = json_decode(file_get_contents("php://input"), true);
                    $pro_id = $args['product_id'];
                    $user_id = $args['user_id'];
                    $products = $em->getRepository("Entity\Products")->find(array('id' => $pro_id));
                    if (!empty($products)) {
                        $owner = $em->getRepository("Entity\Owner")->findOneBy(array('id' => $user_id));
                        $products->setUser_id($owner);
                        $products->setName($args['name']);
                        $today = new DateTime();
                        $products->setCreated_at($today);
                        $products->setUpdated_at($today);
                        //getting product ingredients and removing
                        $all_ing = $em->getRepository("Entity\Product_ingredients")->findBy(array('product_id' => $pro_id));
                        if (!empty($all_ing)) {
                            foreach ($all_ing as $ing) {
                                $em->remove($ing);
                                $em->flush();
                            }
                        }
                        $ingredients = $args['product_ingredients_attributes'];
                        foreach ($ingredients as $ingredient) {
                            $ingredient = $em->getRepository("Entity\Ingredients")->findOneBy(array('id' => $ingredient));
                            $check_ing = $em->getRepository("Entity\Product_ingredients")->findOneBy(array('product_id' => $pro_id, 'ingredient_id' => $ingredient));
                            $pro_ing = new Entity\Product_ingredients;
                            if (empty($check_ing)) {
                                $pro_ing->setProduct_id($products);
                                $pro_ing->setIngredient_id($ingredient);
                                $today = new DateTime();
                                $pro_ing->setCreated_at($today);
                                $pro_ing->setUpdated_at($today);
                                $em->persist($pro_ing);
                                $em->flush();
                            }
                        }
                        $em->persist($products);
                        $em->flush();
                        echo json_encode(array(
                            'response_code' => '200',
                            'response_message' => 'Product has been updated successfully'));
                    } else {
                        echo json_encode(array(
                            'response_code' => '404',
                            'response_message' => 'Something went wrong'));
                    }
                }
            } else {
                if (!empty($this->input->post('image'))) {
                    $pro_id = $this->input->post('product_id');
                    $chef_id = $this->input->post('user_id');
                    $products = $em->getRepository("Entity\Products")->findOneBy(array('id' => $pro_id));
                    if (!empty($products)) {
                        $chef = $em->getRepository("Entity\Chef")->findOneBy(array('id' => $chef_id));
                        $products->setUser_id($chef->getUser_id());
                        $products->setChef_id($chef);
                        $products->setName($this->input->post('name'));
                        $base64_str = $this->input->post('image');
                        $image = base64_decode($base64_str);
                        $image_name = time() . '.png';
                        $path = "./uploads/products/" . $image_name;
                        file_put_contents($path, $image);
                        $uploadpath = "http://app.myallergyalert.com/uploads/products/" . $image_name;
                        $products->setImage($uploadpath);
                        $today = new DateTime();
                        $products->setCreated_at($today);
                        $products->setUpdated_at($today);
                        $all_ing = $em->getRepository("Entity\Product_ingredients")->findBy(array('product_id' => $pro_id));
                        if (!empty($all_ing)) {
                            foreach ($all_ing as $ing) {
                                $em->remove($ing);
                                $em->flush();
                            }
                        }
                        $ingredients = $this->input->post('product_ingredients_attributes');
                        foreach ($ingredients as $ingredient) {
                            $ingredient = $em->getRepository("Entity\Ingredients")->findOneBy(array('id' => $ingredient));
                            $check_ing = $em->getRepository("Entity\Product_ingredients")->findOneBy(array('product_id' => $pro_id, 'ingredient_id' => $ingredient));
                            $pro_ing = new Entity\Product_ingredients;
                            if (empty($check_ing)) {
                                $pro_ing->setProduct_id($products);
                                $pro_ing->setIngredient_id($ingredient);
                                $today = new DateTime();
                                $pro_ing->setCreated_at($today);
                                $pro_ing->setUpdated_at($today);
                                $em->persist($pro_ing);
                                $em->flush();
                            }
                        }
                        $em->persist($products);
                        $em->flush();
                        echo json_encode(array(
                            'response_code' => '200',
                            'response_message' => 'Product has been updated successfully'));
                    } else {
                        echo json_encode(array(
                            'response_code' => '404',
                            'response_message' => 'Something went wrong'));
                    }
                } else {
                    $args = json_decode(file_get_contents("php://input"), true);
                    $pro_id = $args['product_id'];
                    $chef_id = $args['user_id'];
                    $products = $em->getRepository("Entity\Products")->find(array('id' => $pro_id));
                    if (!empty($products)) {
                        $chef = $em->getRepository("Entity\Chef")->findOneBy(array('id' => $chef_id));
                        $products->setUser_id($chef->getUser_id());
                        $products->setChef_id($chef);
                        $products->setName($args['name']);
                        $today = new DateTime();
                        $products->setCreated_at($today);
                        $products->setUpdated_at($today);
                        //getting product ingredients and removing
                        $all_ing = $em->getRepository("Entity\Product_ingredients")->findBy(array('product_id' => $pro_id));
                        if (!empty($all_ing)) {
                            foreach ($all_ing as $ing) {
                                $em->remove($ing);
                                $em->flush();
                            }
                        }
                        $ingredients = $args['product_ingredients_attributes'];
                        foreach ($ingredients as $ingredient) {
                            $ingredient = $em->getRepository("Entity\Ingredients")->findOneBy(array('id' => $ingredient));
                            $check_ing = $em->getRepository("Entity\Product_ingredients")->findOneBy(array('product_id' => $pro_id, 'ingredient_id' => $ingredient));
                            $pro_ing = new Entity\Product_ingredients;
                            if (empty($check_ing)) {

                                $pro_ing->setProduct_id($products);
                                $pro_ing->setIngredient_id($ingredient);
                                $today = new DateTime();
                                $pro_ing->setCreated_at($today);
                                $pro_ing->setUpdated_at($today);
                                $em->persist($pro_ing);
                                $em->flush();
                            }
                        }

                        $em->persist($products);
                        $em->flush();
                        echo json_encode(array(
                            'response_code' => '200',
                            'response_message' => 'Product has been updated successfully'));
                    } else {
                        echo json_encode(array(
                            'response_code' => '404',
                            'response_message' => 'Something went wrong'));
                    }
                }
            }
        }
    }

    //we are not using this method 
    public function updatechefproduct() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            if (!empty($this->input->post('image'))) {
                $pro_id = $this->input->post('product_id');
                $chef_id = $this->input->post('chef_id');
                $products = $em->getRepository("Entity\Products")->find(array('id' => $pro_id));
                if (!empty($products)) {
                    $chef = $em->getRepository("Entity\Chef")->findOneBy(array('id' => $chef_id));
                    $products->setUser_id($chef->getUser_id());
                    $products->setChef_id($chef);
                    $products->setName($this->input->post('name'));
                    $base64_str = $this->input->post('image');
                    $image = base64_decode($base64_str);
                    $image_name = time() . '.png';
                    $path = "./uploads/products/" . $image_name;
                    file_put_contents($path, $image);
                    $uploadpath = "http://app.myallergyalert.com/uploads/products/" . $image_name;
                    $products->setImage($uploadpath);
                    $today = new DateTime();
                    $products->setUpdated_at($today);
                    $all_ing = $em->getRepository("Entity\Product_ingredients")->findBy(array('product_id' => $pro_id));
                    if (!empty($all_ing)) {
                        foreach ($all_ing as $ing) {
                            $em->remove($ing);
                            $em->flush();
                        }
                    }
                    $ingredients = $this->input->post('product_ingredients_attributes');
                    foreach ($ingredients as $ingredient) {
                        $ingredient = $em->getRepository("Entity\Ingredients")->findOneBy(array('id' => $ingredient));
                        $pro_ing = new Entity\Product_ingredients;
                        $pro_ing->setProduct_id($products);
                        $pro_ing->setIngredient_id($ingredient);
                        $today = new DateTime();
                        $pro_ing->setUpdated_at($today);
                        $em->persist($pro_ing);
                        $em->flush();
                    }
                    $em->persist($products);
                    $em->flush();
                    echo json_encode(array(
                        'response_code' => '200',
                        'response_message' => 'Product has been updated successfully'));
                } else {
                    echo json_encode(array(
                        'response_code' => '404',
                        'response_message' => 'Something went wrong'));
                }
            } else {
                $args = json_decode(file_get_contents("php://input"), true);
                $pro_id = $args['product_id'];
                $chef_id = $args['chef_id'];
                $products = $em->getRepository("Entity\Products")->find(array('id' => $pro_id));
                if (!empty($products)) {
                    $chef = $em->getRepository("Entity\Chef")->findOneBy(array('id' => $chef_id));
                    $products->setUser_id($chef->getUser_id());
                    $products->setChef_id($chef);
                    $products->setName($args['name']);
                    $today = new DateTime();
                    $products->setUpdated_at($today);
                    //getting product ingredients and removing
                    $all_ing = $em->getRepository("Entity\Product_ingredients")->findBy(array('product_id' => $pro_id));
                    if (!empty($all_ing)) {
                        foreach ($all_ing as $ing) {
                            $em->remove($ing);
                            $em->flush();
                        }
                    }
                    $ingredients = $args['product_ingredients_attributes'];
                    foreach ($ingredients as $ingredient) {
                        $ingredient = $em->getRepository("Entity\Ingredients")->findOneBy(array('id' => $ingredient));
                        $pro_ing = new Entity\Product_ingredients;
                        $pro_ing->setProduct_id($products);
                        $pro_ing->setIngredient_id($ingredient);
                        $today = new DateTime();
                        $pro_ing->setUpdated_at($today);
                        $em->persist($pro_ing);
                        $em->flush();
                    }
                    $em->persist($products);
                    $em->flush();
                    echo json_encode(array(
                        'response_code' => '200',
                        'response_message' => 'Product has been updated successfully'));
                } else {
                    echo json_encode(array(
                        'response_code' => '404',
                        'response_message' => 'Something went wrong'));
                }
            }
        }
    }

    public function productlist() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $id = null;
            if ($this->input->get('user_type') == 'owner') {
                $products = $em->getRepository("Entity\Products")->findBy(array('user_id' => $this->input->get('user_id'), 'chef_id' => $id));
            } else {
                $owner = $this->input->get('user_id');
                $product = $em->createQuery("SELECT u FROM \Entity\Products u where u.user_id ='$owner' and u.chef_id != '$id' ");
                $products = $product->getResult();
            }
            $allproductss = array();
            foreach ($products as $product) {
                $pro = array();
                $pro['id'] = $product->getId();
                $pro['name'] = $product->getName();
                $pro['created_at'] = "";
                $pro['updated_at'] = "";
                $pro['image'] = $product->getImage();
                $pro['qr_code'] = "";
                $pro['nfc_code'] = "";
                $pro['ingredients'] = array();
                $ingredients = $em->getRepository("Entity\Product_ingredients")->findAll(array('product_id' => $product->getId()));
                $raja = array();
                foreach ($ingredients as $ingredient) {
                    $ing = array();
                    $ing['id'] = $ingredient->getIngredient_id()->getId();
                    $ing['ingredient_name'] = $ingredient->getIngredient_id()->getIngredient_name();
                    $ing['product_name'] = $ingredient->getProduct_id()->getName();
                    $ing['user_id'] = "";
                    $ing['member_id'] = "";
                    $ing['created_at'] = "";
                    $ing['updated_at'] = "";
                    array_push($pro['ingredients'], $ing);
                }
                $pro['user'] = array();
                $pro['user']['id'] = $product->getUser_id()->getId();
                $pro['user']['name'] = $product->getUser_id()->getName();
                array_push($allproductss, $pro);
            }


            $count = count($allproductss);
            $pagination = array('max_page_size' => '1',
                'page_no' => '1',
                'per_page' => '2',
                'total_no_records' => $count);
            if (!empty($allproductss)) {
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'Product deatils fetched',
                    'product' => $allproductss,
                    'pagination' => $pagination));
            } else {
                echo json_encode(array(
                    'response_code' => '404',
                    'response_message' => 'No Products found',
                    'product' => "",
                    'pagination' => ""));
            }
        }
    }

    public function chefproductlist() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $owner = $this->input->get('owner_id');
            $chef = $this->input->get('user_id');
            $product = $em->createQuery("SELECT u FROM \Entity\Products u where u.user_id ='$owner' and u.chef_id = '$chef' ");
            $products = $product->getResult();
            $allproductss = array();
            foreach ($products as $product) {
                $pro = array();
                $pro['id'] = $product->getId();
                $pro['name'] = $product->getName();
                $pro['created_at'] = "";
                $pro['updated_at'] = "";
                $pro['image'] = $product->getImage();
                ;
                $pro['qr_code'] = "";
                $pro['nfc_code'] = "";
                $pro['ingredients'] = array();
                $ingredients = $em->getRepository("Entity\Product_ingredients")->findAll(array('product_id' => $product->getId()));
                $raja = array();
                foreach ($ingredients as $ingredient) {
                    $ing = array();
                    $ing['id'] = $ingredient->getIngredient_id()->getId();
                    $ing['ingredient_name'] = $ingredient->getIngredient_id()->getIngredient_name();
                    $ing['product_name'] = $ingredient->getProduct_id()->getName();
                    $ing['user_id'] = "";
                    $ing['member_id'] = "";
                    $ing['created_at'] = "";
                    $ing['updated_at'] = "";
                    array_push($pro['ingredients'], $ing);
                }
                $pro['user'] = array();
                $pro['user']['id'] = $product->getUser_id()->getId();
                $pro['user']['name'] = $product->getUser_id()->getName();
                array_push($allproductss, $pro);
            }
            $count = count($allproductss);
            $pagination = array('max_page_size' => '1',
                'page_no' => '1',
                'per_page' => '2',
                'total_no_records' => $count);
            if (!empty($allproductss)) {
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'Product deatils fetched ',
                    'product' => $allproductss,
                    'pagination' => $pagination));
            } else {
                echo json_encode(array(
                    'response_code' => '404',
                    'response_message' => 'No Products found',
                    'product' => [],
                    'pagination' => $pagination));
            }
        }
    }

    public function myproducts() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $chef = $this->input->get('user_id');
            $type = $this->input->get('user_type');
            if ($type == "owner") {
                $id = NULL;
                $product = $em->createQuery("SELECT u FROM \Entity\Products u where u.user_id = '$chef' and u.chef_id IS NULL ");
                $products = $product->getResult();
            } else {
                $product = $em->createQuery("SELECT u FROM \Entity\Products u where u.chef_id = '$chef' ");
                $products = $product->getResult();
            }
            $allproductss = array();
            foreach ($products as $product) {
                $pro = array();
                $pro['id'] = $product->getId();
                $pro['name'] = $product->getName();
                $pro['created_at'] = "";
                $pro['updated_at'] = "";
                $pro['image'] = $product->getImage();
                $pro['qr_code'] = "";
                $pro['nfc_code'] = "";
                $pro['ingredients'] = array();
                $ingredients = $em->getRepository("Entity\Product_ingredients")->findAll(array('product_id' => $product->getId()));
                $raja = array();
                foreach ($ingredients as $ingredient) {
                    $ing = array();
                    $ing['id'] = $ingredient->getIngredient_id()->getId();
                    $ing['ingredient_name'] = $ingredient->getIngredient_id()->getIngredient_name();
                    $ing['product_name'] = $ingredient->getProduct_id()->getName();
                    $ing['user_id'] = "";
                    $ing['member_id'] = "";
                    $ing['created_at'] = "";
                    $ing['updated_at'] = "";
                    array_push($pro['ingredients'], $ing);
                }
                $pro['user'] = array();
                $pro['user']['id'] = $product->getUser_id()->getId();
                $pro['user']['name'] = $product->getUser_id()->getName();
                array_push($allproductss, $pro);
            }
            $count = count($allproductss);
            $pagination = array('max_page_size' => '1',
                'page_no' => '1',
                'per_page' => '2',
                'total_no_records' => $count);
            if (!empty($allproductss)) {
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'Product deatils fetched ',
                    'product' => $allproductss,
                    'pagination' => $pagination));
            } else {
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'No Products found',
                    'product' => [],
                    'pagination' => $pagination));
            }
        }
    }

    public function qrtoproducts() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $chef = $this->input->get('user_id');
            $type = $this->input->get('user_type');
            if ($type == "owner") {
                $id = NULL;
                $product = $em->createQuery("SELECT u FROM \Entity\Products u where u.user_id = '$chef' and u.chef_id IS NULL and u.qr_code IS NULL");
                $products = $product->getResult();
            } else {
                $product = $em->createQuery("SELECT u FROM \Entity\Products u where u.chef_id = '$chef' and u.qr_code IS NULL ");
                $products = $product->getResult();
            }
            $allproductss = array();
            foreach ($products as $product) {
                $pro = array();
                $pro['id'] = $product->getId();
                $pro['name'] = $product->getName();
                $pro['created_at'] = "";
                $pro['updated_at'] = "";
                $pro['image'] = $product->getImage();
                $pro['qr_code'] = "";
                $pro['nfc_code'] = "";
                $pro['ingredients'] = array();
                $ingredients = $em->getRepository("Entity\Product_ingredients")->findAll(array('product_id' => $product->getId()));
                $raja = array();
                foreach ($ingredients as $ingredient) {
                    $ing = array();
                    $ing['id'] = $ingredient->getIngredient_id()->getId();
                    $ing['ingredient_name'] = $ingredient->getIngredient_id()->getIngredient_name();
                    $ing['product_name'] = $ingredient->getProduct_id()->getName();
                    $ing['user_id'] = "";
                    $ing['member_id'] = "";
                    $ing['created_at'] = "";
                    $ing['updated_at'] = "";
                    array_push($pro['ingredients'], $ing);
                }
                $pro['user'] = array();
                $pro['user']['id'] = $product->getUser_id()->getId();
                $pro['user']['name'] = $product->getUser_id()->getName();
                array_push($allproductss, $pro);
            }
            $count = count($allproductss);
            $pagination = array('max_page_size' => '1',
                'page_no' => '1',
                'per_page' => '2',
                'total_no_records' => $count);
            if (!empty($allproductss)) {
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'Product deatils fetched ',
                    'product' => $allproductss,
                    'pagination' => $pagination));
            } else {
                echo json_encode(array(
                    'response_code' => '404',
                    'response_message' => 'No Products found',
                    'product' => [],
                    'pagination' => $pagination));
            }
        }
    }

    public function allproducts() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $user_type = $this->input->get('user_type');
            if ($user_type == 'chef') {
                $products = $em->getRepository("Entity\Products")->findBy(array('chef_id' => $this->input->get('user_id')));
            } else {
                $products = $em->getRepository("Entity\Products")->findBy(array('user_id' => $this->input->get('user_id')));
            }
            $allproductss = array();
            foreach ($products as $product) {
                $pro = array();
                $pro['id'] = $product->getId();
                $pro['name'] = $product->getName();
                $pro['created_at'] = $product->getCreated_at()->format('Y-m-d H:i:s');
                $pro['updated_at'] = $product->getUpdated_at()->format('Y-m-d H:i:s');
                $pro['image'] = $product->getImage();
                $pro['qr_code'] = "";
                $pro['nfc_code'] = "";
                $pro['ingredients'] = array();
                $ingredients = $em->getRepository("Entity\Product_ingredients")->findBy(array('product_id' => $product->getId()));
                $raja = array();
                foreach ($ingredients as $ingredient) {
                    $ing = array();
                    $ing['id'] = $ingredient->getIngredient_id()->getId();
                    $ing['ingredient_name'] = $ingredient->getIngredient_id()->getIngredient_name();
                    $ing['product_name'] = $ingredient->getProduct_id()->getName();
                    $ing['user_id'] = "";
                    $ing['member_id'] = "";
                    $ing['created_at'] = "";
                    $ing['updated_at'] = "";
                    array_push($pro['ingredients'], $ing);
                }
                $pro['user'] = array();
                if (!empty($product->getChef_id())) {
                    $pro['user']['id'] = $product->getChef_id()->getId();
                    $pro['user']['name'] = $product->getChef_id()->getName();
                } else {
                    $pro['user']['id'] = $product->getUser_id()->getId();
                    $pro['user']['name'] = $product->getUser_id()->getName();
                }

                array_push($allproductss, $pro);
            }

            $count = count($allproductss);
            $pagination = array('max_page_size' => '1',
                'page_no' => '1',
                'per_page' => '2',
                'total_no_records' => $count);

            if (!empty($allproductss)) {
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'Product deatils fetched ',
                    'product' => $allproductss,
                    'pagination' => $pagination));
            } else {
                echo json_encode(array(
                    'response_code' => '404',
                    'response_message' => 'No Products found',
                    'product' => $allproductss));
            }
        }
    }

    public function deleteproduct() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;

        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            $product_id = $args['id'];
            $ingredients = $em->getRepository("Entity\Product_ingredients")->findBy(array('product_id' => $product_id));
            if (!empty($ingredients)) {
                foreach ($ingredients as $entity) {
                    $em->remove($entity);
                }
                $em->flush();
            }
            $product = $em->getRepository("Entity\Products")->findOneBy(array('id' => $product_id));
            $em->remove($product);
            $em->flush();
            echo json_encode(array(
                'response_code' => '200',
                'response_message' => 'Product has been deleted successfully'));
        } else {
            $ingredients = $em->getRepository("Entity\Product_ingredients")->findBy(array('product_id' => 4));
            if (!empty($ingredients)) {
                foreach ($ingredients as $entity) {
                    $em->remove($entity);
                    $em->flush();
                }
            }
            $product = $em->getRepository("Entity\Products")->find(array('id' => 4));
            $em->remove($product);
            $em->flush();
        }
    }

    public function qrcode() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            if (isset($args['qr_image'])) {
                $productid = $args['id'];
                $userid = $args['user_id'];
                $user_type = $args['user_type'];
                $qrcode = $args['qr_code'];
                $this->qrcodeexistnace($qrcode);

                if ($user_type == 'chef') {
                    $product = $em->getRepository("Entity\Products")->findOneBy(array('id' => $productid, 'chef_id' => $userid));
                    $id = $em->getRepository("Entity\Chef")->findOneBy(array('id' => $userid));
                } else {
                    $product = $em->getRepository("Entity\Products")->findOneBy(array('id' => $productid, 'user_id' => $userid));
                    $id = $em->getRepository("Entity\Owner")->findOneBy(array('id' => $userid));
                }
                if (!empty($product)) {
                    $base64_str = $args['qr_image'];
                    $image = base64_decode($base64_str);
                    $image_name = time() . '.png';
                    $path = "./uploads/qr_images/" . $image_name;
                    file_put_contents($path, $image);
                    $uploadpath = "http://app.myallergyalert.com/uploads/qr_images/" . $image_name;
                    $product->setQr_image($uploadpath);
                    $product->setUpc_code($qrcode);
                    $product->setQr_code($qrcode);
                    $em->persist($product);
                    $em->flush();
                } else {
                    echo json_encode(array(
                        "response_code" => '500',
                        "response_message" => 'No product found for this code'));
                }
            } else {
                $args = json_decode(file_get_contents("php://input"), true);
                $productid = $args['id'];
                $userid = $args['user_id'];
                $user_type = $args['user_type'];
                $qrcode = $args['qr_code'];
                $this->qrcodeexistnace($qrcode);
                if ($user_type == 'chef') {
                    $product = $em->getRepository("Entity\Products")->findOneBy(array('id' => $productid, 'chef_id' => $userid));
                    $id = $em->getRepository("Entity\Chef")->findOneBy(array('id' => $userid));
                } else {
                    $product = $em->getRepository("Entity\Products")->findOneBy(array('id' => $productid, 'user_id' => $userid));
                    $id = $em->getRepository("Entity\Owner")->findOneBy(array('id' => $userid));
                }
                if (!empty($product)) {
                    $product->setUpc_code($args['qr_code']);
                    $product->setQr_code($args['qr_code']);
                    $em->persist($product);
                    $em->flush();
                }
            }

//                sending email confirmation for qr code and image
            if ($user_type == 'chef') {
                $name = $product->getChef_id()->getName();
                $email = $product->getChef_id()->getEmail();
                $product_name = $product->getName();
                $qr_code = $product->getQr_code();
                $qr_image = $product->getQr_image();
                $this->sendqr_image($email, $name, $qr_code, $qr_image, $product_name);
            } else {
                $name = $product->getUser_id()->getName();
                $email = $product->getUser_id()->getEmail();
                $product_name = $product->getName();
                $qr_code = $product->getQr_code();
                $qr_image = $product->getQr_image();
                $this->sendqr_image($email, $name, $qr_code, $qr_image, $product_name);
            }


            //Saving data into scan history
            $scan = new Entity\Scan_history;
            $scan->setProduct_name($product->getName());
            $today = new DateTime();
            $scan->setCreated_at($today);
            $scan->setUpdated_at($today);
            $scan->setResult("");
            $scan->setHistory_type("assigned to QR");
            $scan->setCode_type("upc");
            $scan->setUpc_code($args['qr_code']);
            $scan->setImage("");
            $scan->setUnsafe_users("");
            if ($user_type == 'chef') {
                $scan->setChef_id($id);
                $scan->setOwner_id($id->getUser_id());
            } else {
                $scan->setOwner_id($id);
            }
            $em->persist($scan);
            $em->flush();

            $pro_res = array();
            $pro_res ['id'] = $product->getId();
            $pro_res ['name'] = $product->getName();
            $pro_res ['user_id'] = $product->getUser_id()->getId();
            $pro_res ['qr_code'] = $product->getQr_code();
            $pro_res ['nfc_code'] = $product->getNfc_code();
            $pro_res ['created_at'] = $product->getCreated_at()->format('Y-m-d H:i:s');
            $pro_res ['updated_at'] = $product->getUpdated_at()->format('Y-m-d H:i:s');
            $pro_res['image'] = $product->getImage();


            echo json_encode(array(
                'response_code' => '200',
                'response_message' => 'Qr code assigned to your product',
                'product' => $pro_res));
        } else {
            echo json_encode(array(
                "response_code" => '500',
                "response_message" => 'No product found for this code'));
        }
    }

    public function modify_qrcode() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            $productid = $args['id'];
            $userid = $args['user_id'];
            $qrcode = $args['qr_code'];
            $user_type = $args['user_type'];
            if ($user_type == 'chef') {
                $product = $em->getRepository("Entity\Products")->findOneBy(array('qr_code' => $qrcode, 'chef_id' => $userid));
            } else {
                $product = $em->getRepository("Entity\Products")->findOneBy(array('qr_code' => $qrcode, 'user_id' => $userid));
            }
            if (!empty($product)) {
                $product->setQr_code(NULL);
                $em->persist($product);
                $em->flush();
            }
            if ($user_type == 'chef') {
                $product = $em->getRepository("Entity\Products")->findOneBy(array('id' => $productid, 'chef_id' => $userid));
            } else {
                $product = $em->getRepository("Entity\Products")->findOneBy(array('id' => $productid, 'user_id' => $userid));
            }
            $product->setUpc_code($args['qr_code']);
            $product->setQr_code($args['qr_code']);
            $em->persist($product);
            $em->flush();
            echo json_encode(array(
                'response_code' => '200',
                'response_message' => 'Successfully Qr code modified'));
        }
    }

    public function qrcodesearch() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $userid = $this->input->get('user_id');
            $qrcode = $this->input->get('code');
            $user_type = $this->input->get('user_type');
            if ($user_type == 'chef') {
                $product = $em->getRepository("Entity\Products")->findOneBy(array('chef_id' => $userid, 'qr_code' => $qrcode));
                $id = $em->getRepository("Entity\Chef")->findOneBy(array('id' => $userid));
            } else {
                $product = $em->getRepository("Entity\Products")->findOneBy(array('user_id' => $userid, 'qr_code' => $qrcode));
                $id = $em->getRepository("Entity\Owner")->findOneBy(array('id' => $userid));
            }
            if (!empty($product)) {
                $pro_res = array();
                $pro_res ['id'] = $product->getId();
                $pro_res ['name'] = $product->getName();
                $pro_res ['user_id'] = $product->getUser_id()->getId();
                $pro_res ['qr_code'] = $product->getQr_code();
                $pro_res ['nfc_code'] = $product->getNfc_code();
                $pro_res ['created_at'] = $product->getCreated_at()->format('Y-m-d H:i:s');
                $pro_res ['updated_at'] = $product->getUpdated_at()->format('Y-m-d H:i:s');
                $pro_res['image'] = $product->getImage();

                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => "Code match successfully",
                    'product' => $pro_res));
            } else {
                echo json_encode(array(
                    'response_code' => '500',
                    'response_message' => "No product found for this code",
                ));
            }
        }
    }

    public function delete_qrcode() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        $this->load->helper("url");
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            $productid = $args['id'];
            $userid = $args['user_id'];
            $user_type = $args['user_type'];
            if ($user_type == 'chef') {
                $product = $em->getRepository("Entity\Products")->findOneBy(array('id' => $productid, 'chef_id' => $userid));
                $id = $em->getRepository("Entity\Chef")->findOneBy(array('id' => $userid));
            } else {
                $product = $em->getRepository("Entity\Products")->findOneBy(array('id' => $productid, 'user_id' => $userid));
                $id = $em->getRepository("Entity\Owner")->findOneBy(array('id' => $userid));
            }
            $qr_image = $product->getQr_image();
            $image_name = substr($qr_image, -14);
            $dlt = FCPATH . '/uploads/qr_images/' . $image_name;
            unlink($dlt);
            $product->setUpc_code(NULL);
            $product->setQr_code(NULL);
            $product->setQr_image(NULL);
            $em->persist($product);
            $em->flush();

            //Saving data into scan history
            $scan = new Entity\Scan_history;
            $scan->setProduct_name($product->getName());
            $today = new DateTime();
            $scan->setCreated_at($today);
            $scan->setUpdated_at($today);
            $scan->setResult("");
            $scan->setHistory_type("QR Code removed for product ");
            $scan->setCode_type("upc");
            $scan->setUpc_code("");
            $scan->setImage("");
            $scan->setUnsafe_users("");
            if ($user_type == 'chef') {
                $scan->setChef_id($id);
                $scan->setOwner_id($id->getUser_id());
            } else {
                $scan->setOwner_id($id);
            }
            $em->persist($scan);
            $em->flush();

            echo json_encode(array(
                'response_code' => '200',
                'response_message' => 'Qr Code successfully deleted to the product'));
        }
    }

    public function checkmailexistnace($email, $type) {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        $owner = $em->getRepository("Entity\Owner")->findOneBy(array('email' => $email));
        $chef = $em->getRepository("Entity\Chef")->findOneBy(array('email' => $email));
        if ($type == 'owner') {
            if (empty($owner) && empty($chef)) {
                return true;
            } else {
                $obj = (object) array();
                echo json_encode(array(
                    'response_code' => '404',
                    'response_message' => $email . '  already  existed',
                    'user' => $obj));
                exit;
            }
        }
    }

//        else {
//            if (empty($chef)) {
//                return true;
//            } else {
//                echo json_encode(array(
//                    'response_code' => '404',
//                    'response_message' => $email . ' already  existed'));
//                exit;
//            }
//        }
    public function qrcodeexistnace($qrcode) {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        $products = $em->getRepository("Entity\Products")->findOneBy(array('qr_code' => $qrcode, 'upc_code' => $qrcode));
        if (empty($products)) {
            return true;
        } else {
            $existedUserId = $products->getUser_id();
            if ($existedUserId == $userid) {
                echo json_encode(array(
                    'response_code' => '404',
                    'response_message' => 'This Qr code already existed for some other product'));
                exit;
            } else {
                echo json_encode(array(
                    'response_code' => '404',
                    'response_message' => 'This Qr code already existed to another restaurant'));
                exit;
            }
        }
    }

    public function ingredient($ingredient) {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        $ingredient = $em->getRepository("Entity\Ingredients")->findOneBy(array('id' => $ingredient));
        $check_ing = $em->getRepository("Entity\Product_ingredients")->findOneBy(array('product_id' => $pro_id, 'ingredient_id' => $ingredient));

        if (empty($check_ing)) {
            return true;
        } else {
            echo json_encode(array(
                'response_code' => '404',
                'response_message' => 'ingredient already existed'));
        }
    }

    public function unique_upc() {
        $random_number = mt_rand(0, 9999999999);
        $time = time();
        $unique_upc = $random_number . $time;
        echo json_encode(array(
            'response_code' => '200',
            'response_message' => 'Set Unique upc code for your product',
            'upc' => $unique_upc));
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

    public function sendqr_image($email, $name, $upc_code, $qr_image, $product_name) {
        // To send HTML mail, the Content-type header must be set
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $this->load->library('email');
        $this->email->from('info@myallergyalert.com', 'My Allergy Alert');
        $this->email->to($email);
        $this->email->subject('My Allergy Alert Qr Code details for your Product');
        $message = '<html><body>';
        $message .= 'Hi <b>' . $name . '</b>,<br>';
        $message .= 'This is Unique Qr code for your Product  <b>' . $product_name . '<b>';
        $message .= '<h3>' . $upc_code . '</h3>';
        $message .= '</body></html>';
        $this->email->message($message);
        $this->email->attach($qr_image);
        return $this->email->send();
    }

    public function verifyemail() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        $args = json_decode(file_get_contents("php://input"), true);
        $email = $args['email'];
        $verifycode = $args['verifycode'];
        $owner = $em->getRepository("Entity\Owner")->findOneBy(array('email' => $email, 'verifycode' => $verifycode));
        if (!empty($owner)) {
            $owner->setStatus('1');
            $em->persist($owner);
            $em->flush();
            echo json_encode(array(
                'response_code' => '200',
                'response_message' => 'Successfully verification completed ',
                'user' => $owner));
        } else {
            echo json_encode(array(
                'response_code' => '404',
                'response_message' => 'Please enter valid verification code'));
            exit;
        }
    }

    function forgotpassword() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            $email = $args['email'];
            $type = $args['user_type'];
            if ($type == 'owner') {
                $owner = $em->getRepository("Entity\Owner")->findOneBy(array('email' => $args['email']));
                if (!empty($owner)) {
                    $name = $owner->getName();
                    $verify_code = mt_rand(100000, 999999);
                    $owner->setVerifycode($verify_code);
                    $em->persist($owner);
                    $em->flush();
                    $name = $owner->getName();
                    $verifycode = $owner->getVerifycode();
                    $success = $this->sendemail($email, $name, $verifycode);
                    if ($success) {
                        echo json_encode(array(
                            'response_code' => '200',
                            'response_message' => 'Please check your ' . $email . '  for verification code',
                            'user' => $owner));
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
                        'response_message' => $email . '  is  not registered'));
                }
            } else {
                $chef = $em->getRepository("Entity\Chef")->findOneBy(array('email' => $args['email']));
                if (!empty($chef)) {
                    $name = $chef->getName();
                    $verify_code = mt_rand(100000, 999999);
                    $chef->setVerifycode($verify_code);
                    $em->persist($chef);
                    $em->flush();
                    $name = $chef->getName();
                    $verifycode = $chef->getVerifycode();
                    $success = $this->sendemail($email, $name, $verifycode);
                    if ($success) {
                        echo json_encode(array(
                            'response_code' => '200',
                            'response_message' => 'Please check your ' . $email . '  for verification code',
                            'user' => $chef));
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
                        'response_message' => $email . '  is  not registered'));
                }
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
            $type = $args['user_type'];
            if ($type == 'owner') {
                $owner = $em->getRepository("Entity\Owner")->findOneBy(array('email' => $email, 'verifycode' => $verifycode));
                if (!empty($owner)) {
                    $password = $args['password'];
                    $password_confirmation = $args['password_confirmation'];
                    if ($password == $password_confirmation) {
                        $hashed_password = crypt($args['password']);
                        $owner->setPassword($hashed_password);
                        $em->persist($owner);
                        $em->flush();
                        echo json_encode(array(
                            'response_code' => '200',
                            'response_message' => 'Your password changed successfully',
                            'user' => $owner));
                        exit;
                    }
                } else {
                    echo json_encode(array(
                        'response_code' => '404',
                        'response_message' => 'Please enter correct verify code'));
                    exit;
                }
            } else {
                $chef = $em->getRepository("Entity\Chef")->findOneBy(array('email' => $email, 'verifycode' => $verifycode));
                if (!empty($chef)) {
                    $password = $args['password'];
                    $password_confirmation = $args['password_confirmation'];
                    if ($password == $password_confirmation) {
                        $hashed_password = crypt($args['password']);
                        $chef->setPassword($hashed_password);
                        $em->persist($chef);
                        $em->flush();
                        echo json_encode(array(
                            'response_code' => '200',
                            'response_message' => 'Your password changed successfully',
                            'user' => $chef));
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
    }

    public function sendpassword() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            $email = $args['email'];
            $owner = $em->getRepository("Entity\Owner")->findOneBy(array('email' => $email));
            $name = $owner->getName();
            $email = $owner->getEmail();
            $seed = str_split($name . $email . '0123456789'); // and any other characters
            shuffle($seed); // probably optional since array_is randomized; this may be redundant
            $changedpassword = '';
            foreach (array_rand($seed, 6) as $k)
                $changedpassword .= $seed[$k];
            if (!empty($owner)) {
                $changedpassword = $owner->setPassword($changedpassword);
                $em->persist($owner);
                $em->flush();
                $newpassword = $owner->getPassword();
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
                    'response_message' => 'This email doesnt exist, please enter regsitered email'));
                exit;
            }
        }
    }
      public function sendmail() {
        // To send HTML mail, the Content-type header must be set
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $this->load->library('email');
        $this->email->from('praveen.technodrive@gmail.com', '');
        $this->email->to('praveenkmr841@gmail.com');
        $this->email->subject('My Allergy Alert Email Verification code');
        $message = '<html><body>';
        $message .= 'Hi <b></b>,Please verify your email address so we know that its really you!.';
        $message .= ' <br>Enter this Verification code to Login into My Allergy Alert App.<br> ';
        $message .= '<h1></h1>';
        $message .= '</body></html>';
        $this->email->message($message);
       if ($this->email->send()){
           
           echo 'sucess';
       }else{
          
 echo $this->email->print_debugger();
       }
    }

}
