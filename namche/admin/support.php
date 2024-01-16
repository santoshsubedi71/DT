<?php

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

use namche\Bootstrap;
use namche\lib\PDODatabase;
use namche\lib\Session;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_NAME, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_TYPE);

$ses = new Session($db);
$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR,
]);


$directory = dirname(__DIR__,) . DIRECTORY_SEPARATOR . 'comment';
$filePath = $directory . DIRECTORY_SEPARATOR . 'comment.txt';






$fp = fopen($filePath, 'r');





$linearray3 = []; 
while ($res = fgets($fp)) {
    $linearray = explode("\t", $res);

    if (isset($linearray[0]) && isset($linearray[1])) {
        $linearray3[] = ['name' => $linearray[0], 'email' => $linearray[1], 'subject' => $linearray[2], 'comment' => $linearray[3]];
    }
}







    $context = [
        'comments' => $linearray3,
    ];
    




$template = $twig->loadTemplate('support.html.twig');

$template->display($context);

?>
