<?php
/**
 * @package     Mautic
 * @copyright   2015 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\EmailBundle\Helper;

use Mautic\AssetBundle\Entity\Asset;
use Mautic\CoreBundle\Factory\MauticFactory;
use Mautic\EmailBundle\Entity\Copy;
use Mautic\EmailBundle\Swiftmailer\Exception\BatchQueueMaxedException;
use Mautic\EmailBundle\Swiftmailer\Exception\BatchQueueMaxException;
use Mautic\EmailBundle\Swiftmailer\Message\MauticMessage;
use Mautic\EmailBundle\Swiftmailer\Transport\InterfaceTokenTransport;
use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Entity\Email;
use Mautic\EmailBundle\Entity\Stat;
use Mautic\EmailBundle\Event\EmailSendEvent;
use Mautic\CoreBundle\Helper\EmojiHelper;

/**
 * Class SpamScoreHelper
 */
class SpamScoreHelper
{
	private $detailInfo;
	
	private $summaryScore;
	
	public function getDetailInfo()
	{
		return $this->detailInfo;
	}
	
	public function getSummaryScore()
	{
		return $this->summaryScore;
	}
	
    /**
     * {@inheritdoc}
     *
     * @return \Mautic\EmailBundle\Entity\EmailRepository
     */
    public function getRepository ()
    {
        return $this->em->getRepository('MauticEmailBundle:SpamScore');
    }
    
    public function calculateSpamScore($emailContent, $email, $user)
    {
    	$fullContent = "";
    	
    	
    	// Add Return-Path
    	$fullContent = $fullContent."Return-Path: <".$user->getEmail().">\n";
    	
    	// Add Received: from
    	$fullContent = $fullContent."Received: from [".$_SERVER['SERVER_ADDR']."] \n";
    	
    	// Add Message-ID
    	$fullContent = $fullContent."Message-ID: <02d414fd10e2425d3ba19a9eedb672a9@mautic> \n";
    	
    	// Add Date
		$fullContent = $fullContent."Date: ".date("D, d M Y H:i:s")."\n";
    	
    	// Add subject
    	$fullContent = $fullContent."Subject: ".$email->getSubject().">\n";
    	
    	// Add From
    	$fullContent = $fullContent."From: ".$user->getFirstName()." ".$user->getLastName(). "<".$user->getEmail().">\n";
    	
    	// Add To
    	$fullContent = $fullContent."To: ".$user->getFirstName()." ".$user->getLastName(). "<".$user->getEmail().">\n";
    	
    	/*
    	// Add swift header
    	$fullContent = $fullContent."MIME-Version: 1.0\n";
		$fullContent = $fullContent."Content-Type: multipart/alternative;\n";
		$fullContent = $fullContent." boundary=\"_=_swift_v4_1452069220_d654e903735cbc75cf6fa7cd79c43d84_=_\"\n\n";
		$fullContent = $fullContent."--_=_swift_v4_1452069220_d654e903735cbc75cf6fa7cd79c43d84_=_\n";
		$fullContent = $fullContent."Content-Type: text/html; charset=utf-8\n";
		$fullContent = $fullContent."Content-Transfer-Encoding: quoted-printable\n\n";
		*/
    	
    	$fullContent = $fullContent.$emailContent."\n";
    	
    	// Add swift footer
    	//$fullContent = $fullContent."--_=_swift_v4_1452069220_d654e903735cbc75cf6fa7cd79c43d84_=_--\n";
    	//file_put_contents('D:\emailcontent.txt', $fullContent);
    	
    	$url = 'http://spamcheck.postmarkapp.com/filter';
		$fields = array(
			'email' => $fullContent,
			'options' => 'long');
		$fields_string = "";
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
		rtrim($fields_string, '&');

		$ch = curl_init();

		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_POST, count($fields));
		curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

		//execute post
		$result = curl_exec($ch);
		//file_put_contents('D:\result.txt', str_replace('\n', "\n", $result));
		//close connection
		curl_close($ch);

		//echo $result;

		$jsonObj = json_decode($result);
		
	
		$detail = str_replace("\n", "<br/>", $jsonObj->report);
		$detail = str_replace(" ", "&nbsp;", $detail);
		$this->detailInfo = $detail;
		$this->summaryScore = $jsonObj->score.'/5';
		//$row = $pieces = explode("\n", $jsonObj->report);
		//return $strDisplay;
    }
}
