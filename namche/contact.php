<?php

namespace namche;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use namche\Bootstrap;
use namche\lib\PDODatabase;
use namche\lib\Session;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_NAME, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_TYPE);
$ses = new Session($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);



$ses->checkSession();

$err = '';
// making directory for save the comment file
$directory = dirname(__DIR__,) . DIRECTORY_SEPARATOR . 'namche/comment';
$filePath = $directory . DIRECTORY_SEPARATOR . 'comment.txt';



// Check if the directory exists, if not, create it
if (!file_exists($directory)) {
    mkdir($directory, 0777, true);
}


$fp = fopen($filePath, 'a');

if ($fp) {
    if (isset($_POST['submit'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $subject = $_POST['subject'];
        $comment = $_POST['comment'];

        if ($name !== '' && $email !== '' && $subject !== '' &&  $comment !== '') {
            if (flock($fp, LOCK_EX) === true) {
                // Write to the file
                fwrite($fp, $name . "," . "\t" . $email ."," . "\t" . $subject . "," . "\t"  . $comment . "\n");

                flock($fp, LOCK_UN);
            } else {
                echo 'Could not lock the file for writing.';
            }
        } else {
            $err = 'You need to fill up the full form!!';
        }
    }
    fclose($fp);
} else {
    echo 'Could not open the file for writing.';
}

$context = [
    'err' => $err
];

$template = $twig->loadTemplate('contact.html.twig');
$template->display($context);

?>
