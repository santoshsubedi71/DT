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

$fdate = isset($_POST['fdate']) ? $_POST['fdate'] : null;
$tdate = isset($_POST['tdate']) ? $_POST['tdate'] : null;


$allItems = ($fdate && $tdate) ? $db->getAdminOrdersByDate(
    $fdate,
    $tdate
) :  null;

$allAmountInfo = ($fdate && $tdate) ? $db->getAdminOrdersStatusAmountByDate(
    $fdate,
    $tdate
) : [];

$pending = 0 ;
$delivered = 0 ;

foreach($allAmountInfo as $row){
    if($row['status'] == 'pending')
        $pending = $row['total'];
    if($row['status'] == 'delivered')
        $delivered = $row['total'];
}

$context = [
    
    'allItems' => $allItems,  
    'fdate' => $fdate,
    'tdate' => $tdate,
    'sales_amount' => number_format($delivered ,2),
    'pending_amount' => number_format($pending ,2),
    'total_amount' => number_format(
$pending + $delivered
        ,2),
];

$template = $twig->loadTemplate('sales.html.twig');

$template->display($context);

?>
