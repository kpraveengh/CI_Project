<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once('factual/Factual.php');
require_once('factual/FactualTest.php');

class Barcode extends CI_Controller {
    public function scanupc() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        $this->load->library('curl');
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            $userid = $args['user_id'];
            $code_type = $args['code_type'];
            $upc = $args['upc'];
            $productname = $args['product_name'];
            $historyType = "";
            // $userid = 2;
            $id = null;
            $userpreventives = $em->getRepository("Entity\Preventives")->findBy(array('user_id' => $userid, 'member_id' => $id));
            //getting user name
            $usernames = $em->getRepository("Entity\User")->findOneBy(array('id' => $userid));
            $username = $usernames->getName();
            if ($code_type == "barcode") {
                $historyType = "barcode";
                $factual = new Factual("QPGlQD5uElzu0toEklOWmcOUysxO5Hpa1Fy7pvaX", "Ran91UTDN7FGyRwRrlBurakog8ccoAxiOlnSPgu5");
                $tableName = "products-cpg-nutrition";
                $query = new FactualQuery;
                $query->field("upc")->equal($upc);
                $res = $factual->fetch($tableName, $query);
                $result = $res->getData();
                $image = $result[0]['image_urls'][0];
                $ingredients = $result[0]['ingredients'];
                $product_name = $result[0]['product_name'];
                if ($product_name == "" || empty($ingredients)) {
                    $ingredients = array();
                    $members_result = array();
                    $product_name = "";
                    $responsemessage = "No result found";
                    $user_result = array();
                    echo json_encode(array(
                        'image' => '',
                        'code_type' => $code_type,
                        'ingredients' => $ingredients,
                        'members_result' => $members_result,
                        'product_name' => $product_name,
                        'response_code' => '404',
                        'response_message' => $responsemessage,
                        'user_result' => $usernames,
                    ));
                    exit;
                }
            }
            if ($code_type == "qrcode") {
                $product = $em->getRepository("Entity\Products")->findOneBy(array('upc_code' => $upc));
                if (!empty($product)) {
                    $product_name = $product->getName();
                    $historyType = "upc";
                    //Getting product ingredients
                    $alling = $em->getRepository("Entity\Product_ingredients")->findBy(array('product_id' => $product->getId()));
                    $ingredients = array();
                    foreach ($alling as $ingredient) {
                        $ing = $ingredient->getIngredient_id()->getIngredient_name();
                        array_push($ingredients, $ing);
                    }
                } else {
                    $ingredients = array();
                    $members_result = array();
                    $product_name = "";
                    $responsemessage = "No result found";
                    $user_result = $usernames;
                    echo json_encode(array(
                        'image' => '',
                        'code_type' => $code_type,
                        'ingredients' => $ingredients,
                        'members_result' => $members_result,
                        'product_name' => $product_name,
                        'response_code' => '404',
                        'response_message' => $responsemessage,
                        'user_result' => $user_result,
                    ));
                    exit;
                }
            }
            if ($code_type == "product_search") {
                $upc = $args['upc'];
                $historyType = "product search by name";
                $factual = new Factual("QPGlQD5uElzu0toEklOWmcOUysxO5Hpa1Fy7pvaX", "Ran91UTDN7FGyRwRrlBurakog8ccoAxiOlnSPgu5");
                $tableName = "products-cpg-nutrition";
                $query = new FactualQuery;
                $query->field("upc")->equal($upc);
                $res = $factual->fetch($tableName, $query);
                $result = $res->getData();
                $image = $result[0]['image_urls'][0];
                $ingredients = $result[0]['ingredients'];
                $product_name = $result[0]['product_name'];
                if ($product_name == "" || empty($ingredients)) {
                    $ingredients = array();
                    $members_result = array();
                    $product_name = "";
                    $responsemessage = "No result found";
                    $user_result = array();
                    echo json_encode(array(
                        'image' => '',
                        'code_type' => $code_type,
                        'ingredients' => $ingredients,
                        'members_result' => $members_result,
                        'product_name' => $product_name,
                        'response_code' => '404',
                        'response_message' => $responsemessage,
                        'user_result' => $usernames,
                    ));
                    exit;
                }

//                $product = $em->getRepository("Entity\Products")->findOneBy(array('name' => $productname));
//                if (!empty($product)) {
//                    $product_name = $product->getName();
//                    $historyType = "product search by name";
//                    $alling = $em->getRepository("Entity\Product_ingredients")->findBy(array('product_id' => $product->getId()));
//                    $ingredients = array();
//                    foreach ($alling as $ingredient) {
//                        $ing = $ingredient->getIngredient_id()->getIngredient_name();
//                        array_push($ingredients, $ing);
//                    }
//                } else {
//                    $ingredients = array();
//                    $members_result = array();
//                    $product_name = "";
//                    $responsemessage = "No result found";
//                    $user_result = array();
//                    echo json_encode(array(
//                        'image' => '',
//                        'code_type' => $code_type,
//                        'ingredients' => $ingredients,
//                        'members_result' => $members_result,
//                        'product_name' => $product_name,
//                        'response_code' => '404',
//                        'response_message' => $responsemessage,
//                        'user_result' => $usernames,
//                    ));
//                    exit;
//                }
            }

            // $ingredients = array('Sugar', 'egg', 'milk');
            if ($ingredients) {
                $responsemessage = 'Result is successfully fetched';
            } else {
                $responsemessage = 'No Result';
            }

            $user_not_eat_ingredients = array();
            $userdonteat = "";
            $user_result = array();
            foreach ($userpreventives as $preventive) {
                $ingredient = $preventive->getName();
                if (in_array($ingredient, $ingredients)) {
                    $user_not_eat_ingredients[] = $ingredient;
                    $userdonteat = "Do not eat.";
                }
            }
            $safe = "safe";
            $unsafe_users = array();
            if (count($user_not_eat_ingredients) > 0) {
                $safe = "unsafe";
                $user_result ['harmful_ingredients'] = $user_not_eat_ingredients;
                $user_result ['name'] = $username;
                $user_result ['state'] = $userdonteat;
                array_push($unsafe_users, $username);
            }
            else {
                $user_result ['harmful_ingredients'] = $user_not_eat_ingredients;
                $user_result ['name'] = $username;
                $user_result ['state'] = "can eat.";
            }
            //members data
            $null = null;
            $members_result = array();
            $members = $em->createQuery("SELECT u FROM \Entity\Preventives u where u.user_id ='$userid' and u.member_id != '$null' GROUP BY u.member_id");
            $allmembers = $members->getResult();
            // $ingredients = array('Sugar', 'egg', 'milk');
            foreach ($allmembers as $allmember) {
                $memberpreventives = $em->getRepository("Entity\Preventives")->findBy(array('member_id' => $allmember->getMember_id()));
                $member_result = array();
                $member_not_eat_ingredients = array();
                foreach ($memberpreventives as $memberpreventive) {
                    $member_id = $memberpreventive->getMember_id()->getId();
                    $member_name = $memberpreventive->getMember_id()->getName();
                    $ingredient = $memberpreventive->getName();
                    if (in_array($ingredient, $ingredients)) {
                        $member_not_eat_ingredients[] = $ingredient;
                        $memberdonteat = "Do not eat";
                    }
                }
                if (count($member_not_eat_ingredients) > 0) {
                    $safe = "unsafe";
                    $member_result ['harmful_ingredients'] = $member_not_eat_ingredients;
                    $member_result ['id'] = $member_id;
                    $member_result ['name'] = $member_name;
                    $member_result ['state'] = $memberdonteat;
                    array_push($unsafe_users, $member_name);
                } else {
                    $member_result ['harmful_ingredients'] = $member_not_eat_ingredients;
                    $member_result ['id'] = $member_id;
                    $member_result ['name'] = $member_name;
                    $member_result ['state'] = 'can eat.';
                }
                array_push($members_result, $member_result);
            }

            if ($code_type == "barcode" || $code_type == "product_search") {
                $image = $image;
            } else {
                $image = $product->getImage();
            }
            //Saving data into scan history
            $scan_history = $em->getRepository("Entity\Scan_history")->findOneBy(array('user_id' => $userid, 'product_name' => $product_name));
            if (empty($scan_history)) {
                $unsafe_users_array = serialize($unsafe_users);
                $scan = new Entity\Scan_history;
                $scan->setProduct_name($product_name);
                $today = new DateTime();
                $scan->setCreated_at($today);
                $scan->setUpdated_at($today);
                $scan->setResult($safe);
                $scan->setHistory_type($historyType);
                $scan->setCode_type($code_type);
                $scan->setUpc_code($upc);
                $scan->setImage($image);
                $scan->setUnsafe_users($unsafe_users_array);
                $scan->setUser_id($usernames);
                $em->persist($scan);
                $em->flush();
            } else {
                $unsafe_users_array = serialize($unsafe_users);
                $today = new DateTime();
                $scan_history->setUpdated_at($today);
                $scan_history->setCreated_at($today);
                $scan_history->setUnsafe_users($unsafe_users_array);
                $scan_history->setResult($safe);
                $em->persist($scan_history);
                $em->flush();
            }

            echo json_encode(array(
                'image' => $image,
                'ingredients' => $ingredients,
                'members_result' => $members_result,
                'product_name' => $product_name,
                'response_code' => '200',
                'response_message' => $responsemessage,
                'code_type' => $code_type,
                'user_result' => $user_result,
            ));
        }
    }

    function recent_searched_product() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            $userid = $args['user_id'];
            $type = $args['recent_search'];
            $oneweek = date("Y-m-d", strtotime("-1 week"));
            if ($type == "product_search") {
                $scan_historys = $em->createQuery("SELECT u FROM \Entity\Scan_history u where u.user_id ='$userid' and u.code_type ='$type' and u.updated_at > '$oneweek' ORDER BY u.updated_at DESC");
                $scan_historys->setMaxResults(10);
                $scan_history = $scan_historys->getResult();
            } else {
                $scan_historys = $em->createQuery("SELECT u FROM \Entity\Scan_history u where u.user_id ='$userid' and u.updated_at > '$oneweek' ORDER BY u.updated_at DESC");
                $scan_historys->setMaxResults(10);
                $scan_history = $scan_historys->getResult();
            }
            $scan_historyy = array();
            foreach ($scan_history as $scan_histor) {
                $scan_res = array();
                $scan_res ['code_type'] = $scan_histor->getCode_type();
                $scan_res ['image'] = $scan_histor->getImage();
                $scan_res ['product_name'] = $scan_histor->getProduct_name();
                $scan_res ['upc_code'] = $scan_histor->getUpc_code();
                array_push($scan_historyy, $scan_res);
            }
            if (!empty($scan_historyy)) {
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'Recently searched product fetched successfully',
                    'scan_histroy' => $scan_historyy));
            } else {
                echo json_encode(array(
                    'response_code' => '404',
                    'response_message' => 'history not found'));
            }
        }
    }

    function searched_history() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            $userid = $args['user_id'];
            $codetype = $args['code_type'];
            $oneweek = date("Y-m-d", strtotime("-1 week"));
            //$userid = 2;
            if ($codetype == 'barcode' || $codetype == 'qrcode') {
                $scan_historys = $em->createQuery("SELECT u FROM \Entity\Scan_history u where u.user_id ='$userid' and u.code_type in ('barcode','qrcode') and u.updated_at > '$oneweek' ORDER BY u.updated_at DESC");
         
                $scan_history = $scan_historys->getResult();
                $scan_historyy = array();
                foreach ($scan_history as $scan_histor) {
                    $scan_res = array();
                    $scan_res ['created_at'] = $scan_histor->getCreated_at()->format('Y-m-d H:i:s');
                    $scan_res ['product_name'] = $scan_histor->getProduct_name();
                    $scan_res ['result'] = $scan_histor->getResult();
                    $scan_res ['user_id'] = $scan_histor->getUser_id()->getId();
                    $scan_res ['unsafe_users'] = unserialize($scan_histor->getUnsafe_users());
                    array_push($scan_historyy, $scan_res);
                }
            } else {
                $scan_historys = $em->createQuery("SELECT u FROM \Entity\Scan_history u where u.user_id ='$userid' and u.code_type ='product_search' and u.updated_at > '$oneweek' ORDER BY u.updated_at DESC" );
          
                $scan_history = $scan_historys->getResult();
                $scan_historyy = array();
                foreach ($scan_history as $scan_histor) {
                    $scan_res = array();
                    $scan_res ['created_at'] = $scan_histor->getCreated_at()->format('Y-m-d H:i:s');
                    $scan_res ['product_name'] = $scan_histor->getProduct_name();
                    $scan_res ['result'] = $scan_histor->getResult();
                    $scan_res ['user_id'] = $scan_histor->getUser_id()->getId();
                    $scan_res ['unsafe_users'] = unserialize($scan_histor->getUnsafe_users());
                    array_push($scan_historyy, $scan_res);
                }
            }

            echo json_encode(array(
                'response_code' => '200',
                'response_message' => 'Recently searched product fetched successfully',
                'scan_histroy' => $scan_historyy));
        }
    }

    //search product by name with factual database  

    function search_product() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            $name = $args['name'];
            $factual = new Factual("QPGlQD5uElzu0toEklOWmcOUysxO5Hpa1Fy7pvaX", "Ran91UTDN7FGyRwRrlBurakog8ccoAxiOlnSPgu5");
            $tableName = "products-cpg-nutrition";
            $query = new FactualQuery;
            $query->field("product_name")->beginsWith($name);
            $query->only("product_name,upc,category,image_urls");
            $res = $factual->fetch($tableName, $query);
            $products = $res->getData();
            $pro = array();
            foreach ($products as $product) {
                $pro_res = array();
                $pro_res ['image'] = $product[image_urls][0];
                $pro_res ['category'] = $product[category];
                $pro_res ['product_name'] = $product[product_name];
                $pro_res ['upc'] = $product[upc];
                array_push($pro, $pro_res);
            }
            if (!empty($pro)) {
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'Result is successfully fetched',
                    'products' => $pro));
            } else {
                echo json_encode(array(
                    'response_code' => '404',
                    'response_message' => 'No product is found',
                    'products' => $products));
            }
        }
    }

    function owner_history() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $userid = $this->input->get('user_id');
            $type = $this->input->get('user_type');
            if ($type == 'owner') {
                //$userid = 2;
                $scan_historys = $em->createQuery("SELECT u FROM \Entity\Scan_history u where u.owner_id ='$userid' ORDER BY u.updated_at DESC");
                $scan_historys->setMaxResults(10);
                $scan_history = $scan_historys->getResult();
                $scan_historyy = array();
                // $user = array();
                foreach ($scan_history as $scan_histor) {
                    $scan_res = array();
                    $scan_res ['created_at'] = $scan_histor->getCreated_at()->format('Y-m-d H:i:s');
                    $scan_res ['product_name'] = $scan_histor->getProduct_name();
                    $scan_res ['history_type'] = $scan_histor->getHistory_type();
                    $scan_res['user'] = array();                    
                    $chefId = $scan_histor->getChef_id();
                    if(!empty($chefId)){
                        $scan_res['user']['name'] = $scan_histor->getChef_id()->getName();
                    }else{
                        $scan_res['user']['name'] = $scan_histor->getOwner_id()->getName();
                    }
                    array_push($scan_historyy, $scan_res);
                }
                if (!empty($scan_historyy)) {
                    echo json_encode(array(
                        'response_code' => '200',
                        'response_message' => 'Recently searched product fetched successfully',
                        'product' => $scan_historyy));
                } else {
                    echo json_encode(array(
                        'response_code' => '404',
                        'response_message' => 'No History Found',
                        'product' => $scan_historyy));
                }
            } else {

                $scan_historys = $em->createQuery("SELECT u FROM \Entity\Scan_history u where u.chef_id ='$userid' ORDER BY u.updated_at DESC");
                $scan_historys->setMaxResults(10);
                $scan_history = $scan_historys->getResult();
                $scan_historyy = array();
                // $user = array();
                foreach ($scan_history as $scan_histor) {
                    $scan_res = array();
                    $scan_res ['created_at'] = $scan_histor->getCreated_at()->format('Y-m-d H:i:s');
                    $scan_res ['product_name'] = $scan_histor->getProduct_name();
                    $scan_res ['history_type'] = $scan_histor->getHistory_type();
                    $scan_res['user'] = array();
                    $scan_res['user']['name'] = $scan_histor->getChef_id()->getName();
                    array_push($scan_historyy, $scan_res);
                }

                if (!empty($scan_historyy)) {
                    echo json_encode(array(
                        'response_code' => '200',
                        'response_message' => 'Recently searched product fetched successfully',
                        'product' => $scan_historyy));
                } else {
                    echo json_encode(array(
                        'response_code' => '404',
                        'response_message' => 'No History Found',
                        'product' => $scan_historyy));
                }
            }
        }
    }

    function chef_history() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $userid = $this->input->get('user_id');
            //$userid = 2;
            $scan_historys = $em->createQuery("SELECT u FROM \Entity\Scan_history u where u.chef_id ='$userid' ORDER BY u.updated_at DESC");
            $scan_historys->setMaxResults(10);
            $scan_history = $scan_historys->getResult();
            $scan_historyy = array();
            // $user = array();
            foreach ($scan_history as $scan_histor) {
                $scan_res = array();
                $scan_res ['created_at'] = $scan_histor->getCreated_at()->format('Y-m-d H:i:s');
                $scan_res ['product_name'] = $scan_histor->getProduct_name();
                $scan_res ['history_type'] = $scan_histor->getHistory_type();
                $scan_res['user'] = array();
                $scan_res['user']['name'] = $scan_histor->getChef_id()->getName();
                array_push($scan_historyy, $scan_res);
            }

            if (!empty($scan_historyy)) {
                echo json_encode(array(
                    'response_code' => '200',
                    'response_message' => 'Recently searched product fetched successfully',
                    'product' => $scan_historyy));
            } else {
                echo json_encode(array(
                    'response_code' => '404',
                    'response_message' => 'No History found',
                    'product' => $scan_historyy));
            }
        }
    }

    function user_search_product() {
        $this->load->library('doctrine');
        $em = $this->doctrine->em;
        if (ENVIRONMENTTYPE == 'mobile') {
            $args = json_decode(file_get_contents("php://input"), true);
            $name = $args['product_name'];
            $userid = $args['user_id'];
            // $name = 'egg rice';//$args['name'];
            // $userid = 2;//$args['user_id'];
            $today = new DateTime();
            $product = $em->getRepository("Entity\Products")->findOneBy(array('name' => $name));
            if (!empty($product)) {
                $userid = $args['user_id'];
                // $userid = 2;
                $id = null;
                $userpreventives = $em->getRepository("Entity\Preventives")->findBy(array('user_id' => $userid, 'member_id' => $id));
                //getting user name
                $usernames = $em->getRepository("Entity\User")->findOneBy(array('id' => $userid));
                $username = $usernames->getName();

                //Getting product ingredients
                $ingredients = $em->getRepository("Entity\Product_ingredients")->findBy(array('product_id' => $product->getId()));
                $alling = array();
                foreach ($ingredients as $ingredient) {
                    $ing = $ingredient->getIngredient_id()->getIngredient_name();
                    array_push($alling, $ing);
                }
                // $ingredients = array('Sugar', 'egg', 'milk');
                if ($alling) {
                    $responsemessage = 'Result is successfully fetched';
                } else {
                    $responsemessage = 'No Result';
                }

                $user_not_eat_ingredients = array();
                $userdonteat = "";
                $user_result = array();
                foreach ($userpreventives as $preventive) {
                    $ingredient = $preventive->getName();
                    if (in_array($ingredient, $alling)) {
                        $user_not_eat_ingredients[] = $ingredient;
                        $userdonteat = "do not eat.";
                    }
                }
                $unsafe_users = array();
                if (count($user_not_eat_ingredients) > 0) {
                    $user_result ['harmful_ingredients'] = $user_not_eat_ingredients;
                    $user_result ['name'] = $username;
                    $user_result ['state'] = $userdonteat;
                    array_push($unsafe_users, $username);
                }
                if (count($user_not_eat_ingredients) == 0) {
                    $user_result ['harmful_ingredients'] = $user_not_eat_ingredients;
                    $user_result ['name'] = $username;
                    $user_result ['state'] = "You can eat.";
                }
                //members data
                $null = null;
                $members_result = array();
                $members = $em->createQuery("SELECT u FROM \Entity\Preventives u where u.user_id ='$userid' and u.member_id != '$null' GROUP BY u.member_id");
                $allmembers = $members->getResult();
                // $ingredients = array('Sugar', 'egg', 'milk');

                foreach ($allmembers as $allmember) {
                    $memberpreventives = $em->getRepository("Entity\Preventives")->findBy(array('member_id' => $allmember->getMember_id()));
                    $member_result = array();
                    $member_not_eat_ingredients = array();
                    foreach ($memberpreventives as $memberpreventive) {
                        $member_id = $memberpreventive->getMember_id()->getId();
                        $member_name = $memberpreventive->getMember_id()->getName();
                        $ingredient = $memberpreventive->getName();
                        if (in_array($ingredient, $alling)) {
                            $member_not_eat_ingredients[] = $ingredient;
                            $memberdonteat = "do not eat";
                        }
                    }
                    if (count($member_not_eat_ingredients) > 0) {
                        $member_result ['harmful_ingredients'] = $member_not_eat_ingredients;
                        $member_result ['id'] = $member_id;
                        $member_result ['name'] = $member_name;
                        $member_result ['state'] = $memberdonteat;
                        array_push($unsafe_users, $member_name);
                    } else {
                        $member_result ['harmful_ingredients'] = $member_not_eat_ingredients;
                        $member_result ['id'] = $member_id;
                        $member_result ['name'] = $member_name;
                        $member_result ['state'] = 'do not eat.';
                    }
                    array_push($members_result, $member_result);
                }

                $unsafe_users_array = serialize($unsafe_users);

                //Saving data into scan history
                $user = $em->getRepository("Entity\User")->findOneBy(array('id' => $userid));
                $scan = new Entity\Scan_history;
                $scan->setProduct_name($name);
                $today = new DateTime();
                $scan->setCreated_at($today);
                $scan->setUpdated_at($today);
                $scan->setResult('Safe');
                $scan->setHistory_type('Search');
                $scan->setUpc_code($product->getUpc_code());
                $scan->setImage($product->getImage());
                $scan->setUnsafe_users($unsafe_users_array);
                $scan->setUser_id($user);
                $em->persist($scan);
                $em->flush();


                echo json_encode(array(
                    'image' => '',
                    'ingredients' => $alling,
                    'members_result' => $members_result,
                    'product_name' => $name,
                    'response_code' => '200',
                    'response_message' => $responsemessage,
                    'user_result' => $user_result,
                ));
            } else {
                echo json_encode(array(
                    'image' => '',
                    'ingredients' => "",
                    'members_result' => "",
                    'product_name' => $name,
                    'response_code' => '404',
                    'response_message' => 'No products found',
                    'user_result' => '',
                ));
            }
        }
    }

}

?>