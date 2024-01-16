<?php

namespace namche;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use namche\Bootstrap;
use namche\lib\PDODatabase;
use namche\lib\Session;
use namche\registration\errorClass;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_NAME, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_TYPE);
$ses = new Session($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);

if(!$ses->isUserLogin()){
    header('Location: http://localhost/DT/namche/list.php');
    exit;
}else{



// $data = (isset($_GET['id']) === true && preg_match('/^[0-9]+$/', $_GET['id']) === 1) ? $_GET['id'] : '';

$result = $db->getLoginUserProfile();
$data = $result['id'];

}



$username = isset($_POST['username']) ? $_POST['username'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$tel1 = isset($_POST['tel1']) ? $_POST['tel1'] : '';
$tel2 = isset($_POST['tel2']) ? $_POST['tel2'] : '';
$tel3 = isset($_POST['tel3']) ? $_POST['tel3'] : '';
$zip1 = isset($_POST['zip1']) ? $_POST['zip1'] : '';
$zip2 = isset($_POST['zip2']) ? $_POST['zip2'] : '';
$address = isset($_POST['address']) ? $_POST['address'] : '';

$errArr = [];
$dataArr['username'] = $username;
$dataArr['email'] = $_SESSION['email'];
$dataArr['password'] = $password;
$dataArr['tel1'] = $tel1;
$dataArr['tel2'] = $tel2;
$dataArr['tel3'] = $tel3;
$dataArr['zip1'] = $zip1;
$dataArr['zip2'] = $zip2;
$dataArr['address'] = $address;

$context['dataArr'] = isset($dataArr) ? $dataArr : null;
$context['errArr'] = isset($errArr) ? $errArr : null;

$errorClass = new errorClass;


if(
    $username  &&
    $password &&
    $tel1 &&
    $tel2 &&
    $tel3 &&
    $zip1 &&
    $zip2 &&
    $address
)
$context['errArr'] = $errorClass->errorCheck($context['dataArr']);
else{
    $context['errArr'] = null;
}

if($username &&  $tel1 &&  $tel2 &&  $tel3 && $zip1 && $zip2 &&  $address){
    $currentuser = $db->updateProfile($username, $tel1, $tel2, $tel3,$zip1,$zip2, $address);
    header ('Location: ' . Bootstrap::ENTRY_URL . 'profile.php');
    exit;
}








$context = [

    'userdata' => $result,
  
];

$template = $twig->loadTemplate('profile.html.twig');
$template->display($context);

?>
