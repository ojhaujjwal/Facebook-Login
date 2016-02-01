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

class Index extends Action
{

    /**
     * Get a facebook user data
     *
     * @see https://developers.facebook.com/docs/graph-api/reference/user
     */
    const FIELDS = 'id,email,first_name,last_name,middle_name,gender';

    /**
     * @var Facebook
     */
    private $facebook;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var CustomerInterface
     */
    private $customer;

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
     * @param CustomerInterface           $customer
     * @param CustomerRepositoryInterface $customerRepository
     * @param Logger                      $logger
     * @param Config                      $config
     */
    public function __construct(
        Context $context,
        Facebook $facebook,
        Session $customerSession,
        CustomerInterface $customer,
        CustomerRepositoryInterface $customerRepository,
        Logger $logger,
        Config $config
    ) {
        $this->facebook = $facebook;
        $this->customerSession = $customerSession;
        $this->customer = $customer;
        $this->customerRepository = $customerRepository;
        $this->logger = $logger;
        $this->config = $config;

        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $facebookHelper = $this->facebook->getRedirectLoginHelper();

        try {
            $accessToken = $facebookHelper->getAccessToken();

            if (isset($accessToken)) {
                $facebookUser = $this->facebook->get(
                    '/me?fields=' . static::FIELDS, $accessToken
                )->getGraphUser();
                $customer = $this->customerRepository->getByFacebookId(
                    $facebookUser->getId()
                );

                if (!is_null($customer)) {
                    $this->login($customer->getId());

                    $this->messageManager->addSuccess(__(
                        "You have successfully logged in using your Facebook account."
                    ));
                } else {
                    try {
                        $this->customer = $this->customerRepository->get($facebookUser->getEmail());
                    } finally {
                        $customer = $this->createOrUpdate($facebookUser, $accessToken);
                        $this->login($customer->getId());

                        if ($this->customer->getId() == $customer->getId()) {
                            $this->messageManager->addSuccess(__(
                                "We have discovered you already have an account at our store."
                                . " Your Facebook account is now connected to your store account."
                            ));
                        } else {
                            $this->messageManager->addSuccess(__(
                                "Your Facebook account is now connected to your new user account at our store."
                            ));
                        }
                    }
                }
            } else {
                throw new FacebookSDKException('The Facebook code is null');
            }
        } catch (FacebookSDKException $e) {
            $this->logger->addError($e->getMessage());

            $this->messageManager->addError(__(
                "Oops. Something went wrong! Please try again later."
            ));
        } catch (InputException $e) {
            $this->logger->addError($e->getMessage());

            $this->messageManager->addError(__(
                "Some of required values is not received. Please, check your Facebook settings."
                . "Required fields: email, first name, last name."
            ));
        } catch (Exception $e) {
            $this->logger->addError($e->getMessage());

            $this->messageManager->addError(__(
                "Oops. Something went wrong! Please try again later."
            ));
        }

        $this->_redirect($this->_redirect->getRefererUrl());
    }

    /**
     * Authorization customer by id
     *
     * @param int $customerId
     */
    private function login($customerId)
    {
        $this->customerSession->loginById($customerId);
        $this->customerSession->regenerateId();
    }

    /**
     * Create or update user by using data from facebook
     *
     * @param GraphUser   $facebookUser
     * @param AccessToken $accessToken
     *
     * @return CustomerInterface
     */
    private function createOrUpdate(GraphUser $facebookUser, AccessToken $accessToken)
    {
        if (!$this->customer->getId()) {
            $this->customer->setEmail($facebookUser->getEmail());
            $this->customer->setFirstname($facebookUser->getFirstName());
            $this->customer->setLastname($facebookUser->getLastName());
            $this->customer->setMiddlename($facebookUser->getMiddleName());
            $this->customer->setGender(
                (int)($facebookUser->getGender() == 'male')
            );
        }
        $this->customer->setCustomAttribute('sf_id', $facebookUser->getId());
        $this->customer->setCustomAttribute(
            'sf_access_token', serialize($accessToken)
        );

        return $this->customerRepository->save($this->customer);
    }

    /**
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->config->isEnabled()) {
            $this->_redirect($this->_redirect->getRefererUrl());
        }

        return parent::dispatch(
            $request
        );
    }
}