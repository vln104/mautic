<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic, NP. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\ApiBundle\Security\OAuth2\Firewall;

use Mautic\CoreBundle\Factory\MauticFactory;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;


/**
 * Class OAuthListener
 *
 * @package Mautic\ApiBundle\Security\Firewall
 */
class OAuthListener extends \FOS\OAuthServerBundle\Security\Firewall\OAuthListener
{
    /**
     * @var MauticFactory $factory
     */
    private $factory;

    public function setFactory(MauticFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function handle(GetResponseEvent $event)
    {
        $apiMode = $this->factory->getParameter('api_mode');
        if ($apiMode != 'oauth2') {
            return;
        }

        parent::handle($event);
    }
}