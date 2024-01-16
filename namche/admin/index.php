<?php

namespace namche;

require_once dirname(__FILE__) . '..\\../Bootstrap.class.php';

use \namche\Bootstrap;
use \namche\lib\PDODatabase;
use \namche\lib\Session;
 


    $loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
    $twig = new \Twig_Environment($loader, [
        'cache' => Bootstrap::CACHE_DIR
    ]);

    $db = new PDOdatabase(Bootstrap::DB_HOST, Bootstrap::DB_NAME, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_TYPE);

    $session = new Session($db); 

    if($session->isAdminLogin()){
        header('Location: crud.php');
    }

     
        
        $err_msg = '';
        $err_msg1 = '';
        $err_msg2 = '';
    
        
    
        $username = (isset($_POST['username'])) ? $_POST['username'] : '';
        $password = (isset($_POST['password'])) ? $_POST['password'] : '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['submit'])) {
                if ($username === '') {
                    $err_msg = 'Please insert admin name';
                } elseif ($password === '') {
                    $err_msg1 = 'Please inser admin password';
                } else {
                    // Call the login method
                    $result = $session->loginAdmin($username, $password);
        
                    if ($result) {
                        // Login successful, redirect to a protected page or display a success message
                        header('Location: crud.php');
                        exit;
                    } else {
                        $err_msg2 = 'ADMIN NAME & PASSWORD does not match';
                    }
                }
            }
        }
        
        
        
        
        $context = [
            'err_msg' => $err_msg,
            'err_msg1' => $err_msg1,
            'err_msg2' => $err_msg2,
        
            
        ];


    
    $template = $twig->loadTemplate('admin_login.html.twig');
    $template->display($context);
    
    

?>