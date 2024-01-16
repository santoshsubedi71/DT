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

$db = new PDOdatabase(Bootstrap::DB_HOST, Bootstrap::DB_NAME, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_TYPE);


$itm = new Item($db);
$session = new Session($db);
$cart = new Cart($db);
$wish = new Wish($db);
$session->checkSession();

$loader = new Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new Twig_Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);

// Session keyを見て、DBへの登録と状態をチエックする
$ctg_id = (isset($_GET['ctg_id']) === true && preg_match('/^[0-9]+$/', $_GET['ctg_id']) === 1) ? $_GET['ctg_id'] : '';
$child = (isset($_GET['child']) === true && preg_match('/^[0-9]+$/', $_GET['child']) === 1) ? $_GET['child'] : '';

// category list 一覧　を　取得する
$cateArr = $itm->getCategoryList();
// 商品リスト取得する

$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;
$itemsPerPage = 5; // Adjust this value as needed

$dataArr = $itm->getItemList($ctg_id, $child, ($page - 1) * $itemsPerPage, $itemsPerPage);

$totalItems = $itm->getTotalItemCount($ctg_id);
$totalPages = ceil($totalItems / $itemsPerPage);














if (isset($item_id) && $item_id !== '') {
    $res = $cart->insCartData($customer_no, $item_id);
if ($res === false) {
    echo "商品購入に失敗しました。";
    exit();
    }
}

$sumNum = '';

if(!isset($customer_no) || empty($customer_no)){
    $customer_no = $_SESSION['customer_no'];
}
list($sumNum, $sumPrice) = $cart->getItemAndSumPrice($customer_no);


$childCategory = [];

$childCategory[$ctg_id] = $db->getCategory($ctg_id);

list($wishSum, $wishPrice)=$wish->getwishsum($customer_no);


$context = [

    'username' =>  isset($_SESSION) &&  isset($_SESSION['username']) ? $_SESSION['username'] : '',
    'sumNum' =>  $sumNum,
    'wishSum' =>  $wishSum, 
    'cateArr' => $cateArr,
    'dataArr' => $dataArr,    
    'category' => $db->getSingleCategory($ctg_id),
    'childCategory' => $childCategory,
    'currentPage' => $page, // Current page number
    'totalPages' => $totalPages, // Total number of pages

    
    
    
];
$context['isUserLogin'] = $session->isUserLogin();

// print_r(($context));

$template = $twig->loadTemplate('list2.html.twig');


$template->display($context);
