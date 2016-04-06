<?php
/**
 * @package     Mautic
 * @copyright   2015 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\EmailBundle\Swiftmailer\Transport;

use Mautic\CoreBundle\Factory\MauticFactory;
use Mautic\EmailBundle\Helper\MailHelper;
use Mautic\EmailBundle\Swiftmailer\Message\MauticMessage;

/**
 * Class AbstractMailgunTokenArrayTransport
 */
abstract class AbstractMailgunTokenArrayTransport
{
    /**
     * @var \Swift_Message
     */
    protected $message;

    /**
     * @var
     */
    private $dispatcher;

    /**
     * @var bool
     */
    protected $started = false;

    /**
     * @var MauticFactory
     */
    protected $factory;

    /**
     * Test if this Transport mechanism has started.
     *
     * @return bool
     */

    public function isStarted()
    {
        return $this->started;
    }

    /**
     * Stop this Transport mechanism.
     */
    public function stop()
    {
    	
    }

    /**
     * Start this Transport mechanism.
     */
    public function start()
    {
    	
    }

    /**
     * Register a plugin in the Transport.
     *
     * @param \Swift_Events_EventListener $plugin
     */
    public function registerPlugin(\Swift_Events_EventListener $plugin)
    {
        $this->getDispatcher()->bindEventListener($plugin);
    }

    /**
     * @return \Swift_Events_SimpleEventDispatcher
     */
    protected function getDispatcher()
    {
        if ($this->dispatcher == null) {
            $this->dispatcher = new \Swift_Events_SimpleEventDispatcher();
        }

        return $this->dispatcher;
    }

    /**
     * @param \Swift_Mime_Message $message
     * @param null                $failedRecipients
     *
     * @return int
     * @throws \Exception
     */
    abstract public function send(\Swift_Mime_Message $message, &$failedRecipients = null);

    /**
     * Get the metadata from a MauticMessage
     *
     * @return array
     */
    public function getMetadata()
    {
        return ($this->message instanceof MauticMessage) ? $this->message->getMetadata() : array();
    }

    /**
     * Get attachments from a MauticMessage
     *
     * @return array
     */
    public function getAttachments()
    {
        return ($this->message instanceof MauticMessage) ? $this->message->getAttachments() : array();
    }


    /**
     * @param MauticFactory $factory
     */
    public function setMauticFactory(MauticFactory $factory)
    {
        $this->factory = $factory;
    }
}