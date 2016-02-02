<?php
/**
 * Scandiweb_FacebookLogin
 *
 * @category    Scandiweb
 * @package     Scandiweb_FacebookLogin
 * @author      Viktors Vipolzovs <info@scandiweb.com>
 * @copyright   Copyright (c) 2016 Scandiweb, Ltd (http://scandiweb.com)
 */

namespace Scandiweb\FacebookLogin\Block;

use Facebook\Exceptions\FacebookSDKException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session;
use Scandiweb\FacebookLogin\Model\Facebook\Facebook;
use Scandiweb\FacebookLogin\Logger\Logger;
use Magento\Framework\Message\ManagerInterface;
use Exception;

class Account extends Template
{
    /**
     * Get a facebook user data (TODO remove duplicate)
     *
     * @see https://developers.facebook.com/docs/graph-api/reference/user
     */
    const FIELDS = 'id,email,first_name,last_name,middle_name,gender';

    /**
     * @var Facebook
     */
    protected $facebook;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * Account constructor
     *
     * @param Context          $context
     * @param array            $data
     * @param Facebook         $facebook
     * @param Session          $customerSession
     * @param ManagerInterface $messageManager
     * @param Logger           $logger
     */
    public function __construct(
        Context $context,
        array $data,
        Facebook $facebook,
        Session $customerSession,
        ManagerInterface $messageManager,
        Logger $logger
    ) {
        $this->facebook = $facebook;
        $this->customerSession = $customerSession;
        $this->messageManager = $messageManager;
        $this->logger = $logger;

        parent::__construct($context, $data);
    }

    /**
     * Get facebook user
     *
     * @return array|null
     */
    public function getFacebookUser()
    {
        $facebookUser = null;
        $customer = $this->customerSession->getCustomerData();
        $accessTokenAttribute = $customer->getCustomAttribute('sf_access_token');

        if ($accessTokenAttribute) {
            /** @var $accessToken \Facebook\Authentication\AccessToken */
            $accessToken = unserialize($accessTokenAttribute->getValue());

            try {
                $facebookUser = $this->facebook->get(
                    '/me?fields=' . static::FIELDS, $accessToken
                )->getGraphUser()->all();
            } catch (FacebookSDKException $e) {
                $this->logger->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->logger->addError($e->getMessage());
            }
        }

        return $facebookUser;
    }

    /**
     * Get login url through Facebook SDK
     *
     * @return string
     */
    public function getLoginUrl()
    {
        $facebookHelper = $this->facebook->getRedirectLoginHelper();

        return $facebookHelper->getLoginUrl($this->getUrl('facebook/login'), ['scope' => 'email']);
    }

}