<?php

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

use namche\Bootstrap;
use namche\lib\PDODatabase;
use namche\lib\Session;
use namche\lib\Crud;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_NAME, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_TYPE);

$ses = new Session($db);
$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR,
]);



$order_id_from = isset($_POST['order_id_from']) ? intval($_POST['order_id_from']) : null;


if($order_id_from){
    $db->updateOrderStatus(
        $order_id_from,
        ($_POST['status']=='pending' ? 'pending' : 'delivered')
    );
}



$allItems = $db->getAdminOrders();
foreach($allItems  as $key => $item){
    $allItems[$key]['products'] = json_decode($item['products'], true);
}

$context = [
    
    'allItems' => $allItems,
];

$template = $twig->loadTemplate('orders.html.twig');

$template->display($context);

?>
