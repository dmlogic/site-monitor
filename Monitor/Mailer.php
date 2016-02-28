<?php namespace Monitor;

use Mailgun\Mailgun;

class Mailer {

    private $mailer;
    private $siteName;
    private $notify;
    private $message;
    private $subject;
    private $from;

    public function __construct(Mailgun $mailgun,$siteName,$notify,$from,$response)
    {
        $this->mailer = $mailgun;
        $this->siteName = $siteName;
        $this->notify = $notify;
        $this->from = $from;
        $this->prepare($response);
    }

    public function prepare($response)
    {
        $this->message = sprintf('
            <p>%s is currently down with response code %s</p>
            <p>A copy of the response is below</p>
            <pre>
            <code>
                %s
            </code>
            </pre>
        ',$this->siteName,$response->code,htmlentities($response->contents));

        $this->subject = $this->siteName.' IS DOWN';
    }

    public function send()
    {
        try {

            $result = $this->mailer->sendMessage(API_DOMAIN, [
                                            'from'    => 'Site Monitor <'. $this->from.'>',
                                            'to'      => $this->notify,
                                            'subject' => $this->subject,
                                            'html'    => $this->message
                                            ]);
            // print_r($result);
        } catch(Mandrill_Error $e) {
            // print_r($e);
        }
    }
}