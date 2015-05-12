<?php namespace Monitor;

use Mandrill;
use Mandrill_Error;

class Mailer {

    private $mailer;
    private $siteName;
    private $notify;
    private $message;
    private $subject;
    private $from;

    public function __construct(Mandrill $mandrill,$siteName,$notify,$from,$response)
    {
        $this->mailer = $mandrill;
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
            $messageData = array(
                'html' => $this->message,
                'subject' => $this->subject,
                'from_email' => $this->from,
                'from_name' => 'Site Monitor',
                'to' => array(
                    array(
                        'email' => $this->notify,
                        'type' => 'to'
                    )
                ),
                'headers' => array('Reply-To' => $this->from),
                'tags' => array('site-monitor'),
                'important' => true,
                'track_opens' => null,
                'track_clicks' => null,
                'auto_text' => null,
                'auto_html' => null,
                'inline_css' => null,
                'url_strip_qs' => null,
                'preserve_recipients' => null,
                'view_content_link' => null,
                'tracking_domain' => null,
                'signing_domain' => null,
                'return_path_domain' => null,
            );
            $async = false;
            $ip_pool = 'Main Pool';
            $result = $this->mailer->messages->send($messageData, $async, $ip_pool);

            if(isset($result[0]) && $result[0]['status'] != 'sent') {
                $message = $result[0]['reject_reason'];
            }
            // print_r($result);
        } catch(Mandrill_Error $e) {
            // print_r($e);
        }
    }
}