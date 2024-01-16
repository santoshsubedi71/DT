<?php

namespace namche;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use namche\Bootstrap;
use namche\lib\PDODatabase;
use namche\lib\Session;
use namche\lib\Cart;

$db = new PDOdatabase(Bootstrap::DB_HOST, Bootstrap::DB_NAME, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_TYPE);
$ses = new Session($db);
$cart = new Cart($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);



$ses->checkSession();
$customer_no = $_SESSION['customer_no'];
if ($_SESSION['customer_no'] === ''){
    exit();
}




$result = $db->getLoginUserProfile();

$dataArr = $cart->getCartData($customer_no);
list($sumNum, $sumPrice) = $cart->getItemAndSumPrice($customer_no);



if(isset($_POST['submitBtn'])){
    $username = isset($_POST['username']) ? $_POST['username'] :  '';
    $email = isset($_POST['email']) ? $_POST['email'] :  '';
    $zip1 = isset($_POST['zip1']) ? $_POST['zip1'] :  '';
    $zip2 = isset($_POST['zip2']) ? $_POST['zip2'] :  '';
    $shipping_address = isset($_POST['address']) ? $_POST['address'] :  '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] :  '';
    $shipping_amount = isset($_POST['shipping_amount']) ? $_POST['shipping_amount'] :  '';
    
    $dataArr = $cart->getCartData($customer_no);
    $jsonDataproducts = json_encode($dataArr);

    $db->insert(
        ' orders ',
        [
            'fullname' => $username,
            'email' => $email,
            'zip1' => $zip1,
            'zip2' => $zip2,
            'shipping_address' => $shipping_address,
            'phone_no' => $phone   ,
            'products' => $jsonDataproducts,
            'shipping_amount' => $shipping_amount,
            'sub_total' => $sumPrice,
            'total' => $sumPrice + $shipping_amount,
            'signup_id' => $result['id']
        ]
    );

    $cart->delAllCartData();

    
    header('Location: list.php');

    exit;

}

$context = [
    'userdata' => $result,
];
$context['sumNum'] = $sumNum;
$context['sumPrice'] = $sumPrice;
$context['dataArr'] = $dataArr;

$template = $twig->loadTemplate('purchase.html.twig');
$template->display($context);
