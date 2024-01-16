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
$twig = new \Twig_Environment($loader,[
    'cache' => Bootstrap::CACHE_DIR]);

//here is set the session id
$ses->checkSession();
$customer_no = $_SESSION['customer_no'];



//itm_id which id is come from another pages
$item_id = (isset($_GET['item_id']) === true && preg_match('/^\d+$/', $_GET
['item_id']) === 1) ? $_GET['item_id'] : '';

$crt_id = (isset($_GET['crt_id'])===true && preg_match('/^\d+$/', $_GET
['crt_id']) === 1) ? $_GET['crt_id'] : '';

// here is quentity for to confirm quantity on cart 
$qty = (isset($_GET['qty'])===true && preg_match('/^\d+$/', $_GET
['qty']) === 1) ? $_GET['qty'] : '1';


$qtyChange = (isset($_GET['qtyChange'])===true && preg_match('/^\d+$/', $_GET
['qtyChange']) === 1) ? $_GET['qtyChange'] : '';

    if($qtyChange && $crt_id && $qty){
        $cart->updateCartData($crt_id, $qty);
        echo true;
        exit();
    }
    //here is insert item in cart with item quamtity

    if ($item_id !== '') {
            $res = $cart->insCartData($customer_no, $item_id, $qty);
        if ($res === false) {
            echo "商品購入に失敗しました。";
            exit();
            }
        }

    //here is delete cart item using cart id
    if ($crt_id !== '') {
        $res = $cart->delCartData($crt_id);
        }
        // here is getting info of cart becouse of how many item are there
        $dataArr = $cart->getCartData($customer_no);


        //アイテム数と合計額を取得する、listは配列をそれぞれの変数に分ける
        // here is geting item quantity and total price iof item
    
        list($sumNum, $sumPrice) = $cart->getItemAndSumPrice($customer_no);

        if(!isset($customer_no) || empty($customer_no)){
            $customer_no = $_SESSION['customer_no'];
        }
    
  

    
$context = [
    'username' =>  isset($_SESSION) &&  isset($_SESSION['username']) ? $_SESSION['username'] : '',
    'userIsLoggedIn' => $ses->isUserLogin()
];
$context['sumNum'] = $sumNum;
$context['sumPrice'] = $sumPrice;
$context['dataArr'] = $dataArr;
$template = $twig->loadTemplate('cart.html.twig');
$template->display($context);



