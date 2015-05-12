<?php
require 'vendor/autoload.php';
require 'config.php';

$checker = new Monitor\Check(new  GuzzleHttp\Client) ;
$mandrill = new Mandrill(API_KEY);

/*
$sites = [
    'label' => ['http://example.com','Site Name'],
];
*/

foreach($sites as $site) {

    $result = $checker->checkSite($site[0]);
    if(!$result->isOk) {
        $mailer = new Monitor\Mailer($mandrill,$site[1],NOTIFY_EMAIL,FROM_EMAIL,$result);
        $mailer->send();
        unset($mailer);
        echo 'DOWN '.$site[0].PHP_EOL;
    } else {
        echo 'OK '.$site[0].PHP_EOL;
    }
}
