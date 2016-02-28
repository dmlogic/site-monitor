<?php
require 'vendor/autoload.php';
require 'config.php';

use Mailgun\Mailgun;

$debug = defined('DEBUG_FILE') ? fopen(DEBUG_FILE,'a') : false;

$checker = new Monitor\Check(new  GuzzleHttp\Client,$debug);
$mailgun = new Mailgun(API_KEY);

/*
$sites = [
    'label' => ['http://example.com','Site Name'],
];
*/

foreach($sites as $site) {

    $result = $checker->checkSite($site[0]);
    if(!$result->isOk) {
        $mailer = new Monitor\Mailer($mailgun,$site[1],NOTIFY_EMAIL,FROM_EMAIL,$result);
        $mailer->send();
        unset($mailer);
        echo 'DOWN '.$site[0].PHP_EOL;
    } else {
        echo 'OK '.$site[0].PHP_EOL;
    }
}
