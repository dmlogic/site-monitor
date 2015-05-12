<?php namespace Monitor;

use StdClass;

class Check {

    private $client;

    public function __construct($client)
    {
        $this->client = $client;
    }

    public function checkSite($site)
    {
        set_time_limit ( 31 );
        $result = new StdClass;

        try {

            $response         = $this->client->get($site, ['timeout' => 30]);
            $result->code     = $response->getStatusCode();
            $result->isOk     = substr($result->code, 0, 1) == '2';
            $result->contents = (string) $response->getBody();

        } catch(\Exception $e) {

            $result->code     = $e->getCode();
            $result->isOk     = false;
            if ($e->hasResponse()) {
                $result->contents = (string) $e->getResponse()->getBody();
            } else {
                $result->contents = 'Guzzle could not connect';
            }
        }

        return $result;
    }
}