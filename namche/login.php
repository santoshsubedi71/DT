<?php

namespace namche;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use namche\Bootstrap;
use namche\lib\PDODatabase;
use namche\lib\Session;
use namche\lib\Cart;
use namche\registration\errorClass;

$errorClass = new errorClass();

$db = new PDOdatabase(Bootstrap::DB_HOST, Bootstrap::DB_NAME, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_TYPE);
$ses = new Session($db);
$cart = new Cart($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);




// here is logic for login form

$email = isset($_POST['email']) ? $_POST['email'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

$errArr = [];
$dataArr['email'] = $email;
$dataArr['password'] = $password;



$context = [];
$context['dataArr'] = isset($dataArr) ? $dataArr : null;
$context['errArr'] = isset($errArr) ? $errArr : null;



if ($_SERVER['REQUEST_METHOD'] === 'POST'){
   $xyz = $errorClass->errorCheckLogin($context['dataArr']);
}
else
    $xyz = null;




if(isset($_GET['deactive'])){
    $ses->checkSession();
    $db->UserToggleDeactive(
        $_SESSION['email'],
        1
    );
}

if(isset($_GET['logout'])){
    $ses->logout();
    header ('Location: ' . Bootstrap::ENTRY_URL . 'login.php');
    exit();
}


if($ses->isUserLogin()){
    header('Location: list.php');
    exit;
}

// print_r($context['errArr']);



if($errorClass->getErrorFlg()===false){

    //insert code for signup table
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['submit'])){   
           
            // Check if the email already exists
                    
                
           
                $loginFlag = $ses->loginUser($email, $password);
    
                if ($loginFlag) {
                    header ('Location: ' . Bootstrap::ENTRY_URL . 'list.php');
                    exit();
                } else {
                    $abc_err = "login failed. Please try again.";
                    header ('Location: ' . Bootstrap::ENTRY_URL . 'login.php');
                    exit();
                }
            
        }
    }

}

// print_r($context);
$context = [
    'error_message' => isset($abc_err) ? $abc_err : null, 
    'registred' => isset($_SESSION['registred']) ? $_SESSION['registred'] : false,
    'errArr' => $xyz,
    'dataArr' => $dataArr
];

if(isset($_SESSION['registred'])){
    $_SESSION['registred'] = false;
}

$template = $twig->loadTemplate('login.html.twig');
$template->display($context);



