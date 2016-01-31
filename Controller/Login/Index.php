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
use Magento\Framework\Exception\NoSuchEntityException;
use Scandiweb\FacebookLogin\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Scandiweb\FacebookLogin\Model\Facebook\Facebook;

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
     * Index constructor
     *
     * @param Context                     $context
     * @param Facebook                    $facebook
     * @param Session                     $customerSession
     * @param CustomerInterface           $customer
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Context $context,
        Facebook $facebook,
        Session $customerSession,
        CustomerInterface $customer,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->facebook = $facebook;
        $this->customerSession = $customerSession;
        $this->customer = $customer;
        $this->customerRepository = $customerRepository;

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
                } else {
                    $customer = $this->create($facebookUser, $accessToken);
                    $this->login($customer->getId());
                }
            } else {
                var_dump('Ooops 1');
            }
        } catch (FacebookSDKException $e) {
            var_dump('Ooops 2');
        } catch (Exception $e) {
            var_dump('Ooops 3');
            var_dump($e->getMessage());
        }

        $this->_redirect($this->_redirect->getRefererUrl());
    }

    /**
     * Authorization customer by id
     *
     * @param int $customerId
     *
     * @throws NoSuchEntityException
     */
    private function login($customerId)
    {
        $this->customerSession->loginById($customerId);
        $this->customerSession->regenerateId();
    }

    /**
     * Create new user by using data from facebook
     *
     * @param GraphUser   $facebookUser
     * @param AccessToken $accessToken
     *
     * @return CustomerInterface
     */
    private function create(GraphUser $facebookUser, AccessToken $accessToken)
    {
        $this->customer->setEmail($facebookUser->getEmail());
        $this->customer->setFirstname($facebookUser->getFirstName());
        $this->customer->setLastname($facebookUser->getLastName());
        $this->customer->setGender((int)($facebookUser->getGender() == 'male'));
        $this->customer->setCustomAttribute('sf_id', $facebookUser->getId());
        $this->customer->setCustomAttribute('sf_access_token', serialize($accessToken));

        return $this->customerRepository->save($this->customer);
    }
}