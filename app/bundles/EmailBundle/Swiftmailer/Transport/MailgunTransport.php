<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\EmailBundle\Swiftmailer\Transport;

use Mailgun\Mailgun;
use Mautic\EmailBundle\Helper\MailHelper;

/**
 * Class MailgunTransport
 */
class MailgunTransport extends AbstractMailgunTokenArrayTransport implements \Swift_Transport
{
	/**
     * @var
     */
    private $username;

    /**
     * @var
     */
    private $password;
    
    /**
     * @var
     */
    private $apiLink;
    
    /**
     * @var
     */
    private $privateAPIKey;
    
    /**
     * @var
     */
    private $publicAPIKey;
    
    public function setAPILink($apiLink)
    {
    	$this->apiLink = $apiLink;
    }
    
    public function setPrivateAPIKey($privateAPIKey)
    {
    	$this->privateAPIKey = $privateAPIKey;
    }
    
    public function setPublicAPIKey($publicAPIKey)
    {
    	$this->publicAPIKey = $publicAPIKey;
    }

    /**
     * @param $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param \Swift_Mime_Message $message
     * @param null                $failedRecipients
     *
     * @return int
     * @throws \Swift_TransportException
     */
    public function send(\Swift_Mime_Message $message, &$failedRecipients = null)
    {
        $this->message = $message;

        // from
    	$from = array_shift(array_keys($message->getFrom()));

    	// to
    	$to = array_shift(array_keys($message->getTo()));

    	// subject
    	$subject = $message->getSubject();

    	// text
    	$body = $message->getBody();

		$textPlain = MailHelper::getPlainTextFromMessage($message);

    	$mg = new Mailgun($this->privateAPIKey );
		$domain = $this->apiLink;

		# Now, compose and send your message.
		$result = $mg->sendMessage($domain, array('from'    => $from, 
				                                'to'      	=> $to, 
				                                'subject' 	=> $subject, 
				                                'html'    	=> $body,
												'text'		=> $textPlain));

    }
}
