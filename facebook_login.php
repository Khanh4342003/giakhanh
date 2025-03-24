<?php
require_once __DIR__ . '/vendor/autoload.php'; // Import thư viện Facebook SDK

session_start();

$fb = new \Facebook\Facebook([
    'app_id' => 'YOUR_APP_ID',
    'app_secret' => 'YOUR_APP_SECRET',
    'default_graph_version' => 'v18.0',
]);

$helper = $fb->getRedirectLoginHelper();
$loginUrl = $helper->getLoginUrl('http://localhost/boBitTet_PTMNM/facebook_callback.php', ['email']);

header('Location: ' . $loginUrl);
