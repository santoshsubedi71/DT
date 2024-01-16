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

if (!$ses->isUserLogin()) {
    header('Location: http://localhost/DT/namche/list.php');
    exit;
} else {
    $result = $db->getLoginUserProfile();
    $userID = $result['id'];
    $storedPassword = $result['password'];

    $error = '';
    $error2 = '';
    $error3 = '';

    if (isset($_POST['submit'])) {
       
        $oldPassword = $_POST['oldPassword'];
        $newPassword = $_POST['newPassword'];
        $confirmPassword = $_POST['confirmPassword'];

        if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
            $error = 'All fields are required.';
        } else {
            
            if (password_verify($oldPassword, $storedPassword)) {
                
               
                if ($newPassword === $confirmPassword) {
                    // Hash the new password
                    $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                    
                    $db->updateUserPassword($userID, $hashedNewPassword);

                    $successMessage = 'Password changed successfully!';
                    header("Location: profile.php?message=$successMessage");
                    exit();
                } else {
                    $error3 = 'New password and confirm password do not match.';
                }
            } else {
                $error2 = 'Incorrect old password.';
            }
        }
    }

    $context = [
        'error' => $error,
        'error2' => $error2,
        'error3' =>$error3
    ];

    $template = $twig->loadTemplate('changePassword.html.twig');
    $template->display($context);
}
?>
