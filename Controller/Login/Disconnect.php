<?php
/**
 * Scandiweb_FacebookLogin
 *
 * @category    Scandiweb
 * @package     Scandiweb_FacebookLogin
 * @author      Viktors Vipolzovs <info@scandiweb.com>
 * @copyright   Copyright (c) 2016 Scandiweb, Ltd (http://scandiweb.com)
 */

namespace Scandiweb\FacebookLogin\Controller\Login;

use Exception;
use Facebook\Authentication\AccessToken;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\GraphNodes\GraphUser;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\InputException;
use Scandiweb\FacebookLogin\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Scandiweb\FacebookLogin\Logger\Logger;
use Scandiweb\FacebookLogin\Model\Facebook\Config;
use Scandiweb\FacebookLogin\Model\Facebook\Facebook;
use Magento\Framework\App\RequestInterface;

class Disconnect extends Action
{

    /**
     * @var Facebook
     */
    private $facebook;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Config
     */
    private $config;

    /**
     * Index constructor
     *
     * @param Context                     $context
     * @param Facebook                    $facebook
     * @param Session                     $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param Logger                      $logger
     * @param Config                      $config
     */
    public function __construct(
        Context $context,
        Facebook $facebook,
        Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        Logger $logger,
        Config $config
    ) {
        $this->facebook = $facebook;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->logger = $logger;
        $this->config = $config;

        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $customer = $this->customerSession->getCustomerData();
        $accessTokenAttribute = $customer->getCustomAttribute('sf_access_token');
        $facebookUserIdAttribute = $customer->getCustomAttribute('sf_access_token');

        if ($accessTokenAttribute && $facebookUserIdAttribute) {
            /** @var $accessToken \Facebook\Authentication\AccessToken */
            $accessToken = unserialize($accessTokenAttribute->getValue());
            $facebookUserId = $facebookUserIdAttribute->getValue();

            try {
                $this->facebook->delete('/' . $facebookUserId . '/permissions', [], $accessToken);

                $customer->setCustomAttribute('sf_id', null);
                $customer->setCustomAttribute('sf_access_token', null);
                $this->customerRepository->save($customer);

                $this->messageManager->addSuccess(__(
                    'You have successfully disconnected your Facebook account from our store account.'
                ));
            } catch (Exception $e) {
                $this->logger->addError($e->getMessage());

                $this->messageManager->addError(__(
                    "Oops. Something went wrong! Please try again later."
                ));
            }
        }

        return $this->_redirect($this->_redirect->getRefererUrl());
    }

    /**
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->config->isEnabled() || !$this->customerSession->isLoggedIn()) {
            $this->_redirect($this->_redirect->getRefererUrl());
        }

        return parent::dispatch(
            $request
        );
    }
}