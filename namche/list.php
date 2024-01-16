<?php

namespace namche;

require_once dirname(__FILE__) . '/bootstrap.class.php';

use namche\Bootstrap;
use namche\lib\PDODatabase;
use namche\lib\Session;
use namche\lib\Item;

use namche\lib\Cart;
use namche\lib\Wish;

use Twig_Loader_Filesystem;
use Twig_Environment;
// database connection and using class
$db = new PDOdatabase(Bootstrap::DB_HOST, Bootstrap::DB_NAME, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_TYPE);
$itm = new Item($db);
$session = new Session($db);
$cart = new Cart($db);
$wish = new Wish($db);
$session->checkSession();
// this is for twig loadder
$loader = new Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new Twig_Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);

$ctg_id = (isset($_GET['ctg_id']) === true && preg_match('/^[0-9]+$/', $_GET['ctg_id']) === 1) ? $_GET['ctg_id'] : '';
// here is show all items

// $dataArr = $itm->getItemList($ctg_id);

$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;
$itemsPerPage = 12; // Adjust this value as needed

// Get paginated item data
$dataArr = $itm->getItemList($ctg_id, '', ($page - 1) * $itemsPerPage, $itemsPerPage);

$totalItems = $itm->getTotalItemCount($ctg_id);
$totalPages = ceil($totalItems / $itemsPerPage);



// here item inserting in to the cart
if (isset($item_id) && $item_id !== '') {
    $res = $cart->insCartData($customer_no, $item_id);
if ($res === false) {
    echo "fail item to add cart";
    exit();
    }
}
if(!isset($customer_no) || empty($customer_no)){
    $customer_no = $_SESSION['customer_no'];
}


$sumNum = [];
$wishSum = [];
$childCategory = [];
// here we get the total item and their price from cart
list($sumNum, $sumPrice) = $cart->getItemAndSumPrice($customer_no);
// here we get the total item from wishlist
list($wishSum, $wishPrice)=$wish->getwishsum($customer_no);




$context = [

    'username' =>  isset($_SESSION) &&  isset($_SESSION['username']) ? $_SESSION['username'] : '',
    'sumNum' =>  $sumNum, //for total num of item by cart
    'wishSum' =>  $wishSum, //for total item by wish
    'dataArr' => $dataArr, //data of item 
    'category' => $db->getParentCategory(),
    'childCategory' => $childCategory,
    'currentPage' => $page, // Current page number
    'totalPages' => $totalPages, // Total number of pages

];
$context['isUserLogin'] = $session->isUserLogin();
$template = $twig->loadTemplate('list.html.twig');
$template->display($context);
