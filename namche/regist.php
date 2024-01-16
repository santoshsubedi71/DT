<?php

namespace namche;


require_once dirname(__FILE__) . '/Bootstrap.class.php';


use namche\Bootstrap;
use namche\lib\PDODatabase;
use namche\registration\errorClass;

$db = new PDOdatabase(Bootstrap::DB_HOST, Bootstrap::DB_NAME, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_TYPE);

$errorClass = new errorClass();




$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);





$username = isset($_POST['username']) ? $_POST['username'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$tel1 = isset($_POST['tel1']) ? $_POST['tel1'] : '';
$tel2 = isset($_POST['tel2']) ? $_POST['tel2'] : '';
$tel3 = isset($_POST['tel3']) ? $_POST['tel3'] : '';
$zip1 = isset($_POST['zip1']) ? $_POST['zip1'] : '';
$zip2 = isset($_POST['zip2']) ? $_POST['zip2'] : '';
$address = isset($_POST['address']) ? $_POST['address'] : '';

$errArr = [];
$dataArr['username'] = $username;
$dataArr['email'] = $email;
$dataArr['password'] = $password;
$dataArr['tel1'] = $tel1;
$dataArr['tel2'] = $tel2;
$dataArr['tel3'] = $tel3;
$dataArr['zip1'] = $zip1;
$dataArr['zip2'] = $zip2;
$dataArr['address'] = $address;


$context = [];
$context['dataArr'] = isset($dataArr) ? $dataArr : null;
$context['errArr'] = isset($errArr) ? $errArr : null;


if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $context['errArr'] = $errorClass->errorCheck($context['dataArr']);
}
else
    $context['errArr'] = null;


// die($errorClass->getErrorFlg());

if($errorClass->getErrorFlg()===false){

    //insert code for signup table
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['submit'])){    
            // Check if the email already exists
                    
                
            if (!$emailcheck) {
                $insertResult = $db->insertUser($username, $email, $password, $tel1, $tel2, $tel3, $zip1, $zip2, $address);
    
                if ($insertResult) {
                    session_start();
                    $_SESSION['registred'] = true;
                    header ('Location: ' . Bootstrap::ENTRY_URL . 'login.php');
                    exit();
                } else {
                    $context['error_message'] = "Registration failed. Please try again.";
                }
            }
        }
    }

}



// print_r($context);

$template = $twig->loadTemplate('regist.html.twig');
$template->display($context);