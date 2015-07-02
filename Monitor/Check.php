<?php namespace Monitor;

use StdClass;
use GuzzleHttp\Exception\ConnectException;

class Check {

    private $client;
    private $debug;

    public function __construct($client, $debug = false)
    {
        $this->client = $client;
        $this->debug = $debug;
    }

    public function checkSite($site)
    {
        set_time_limit ( 31 );
        $result = new StdClass;

        try {

            $response         = $this->client->get($site, ['timeout' => 30 , 'debug' => $this->debug]);
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

            if(in_array(get_class($e), ['GuzzleHttp\Exception\ConnectException','GuzzleHttp\Exception\RequestException'])) {
                $result->contents .= PHP_EOL.'More detail: '.$e->getHandlerContext()['error'];
            }
        }

        return $result;
    }
}