<?php
/**
 * Scandiweb_FacebookLogin
 *
 * @category    Scandiweb
 * @package     Scandiweb_FacebookLogin
 * @author      Viktors Vipolzovs <info@scandiweb.com>
 * @copyright   Copyright (c) 2016 Scandiweb, Ltd (http://scandiweb.com)
 */

namespace Scandiweb\FacebookLogin\Controller\Account;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Scandiweb\FacebookLogin\Model\Facebook\Config;

class Index extends Action
{

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var Config
     */
    private $config;

    /**
     * Index constructor.
     *
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     * @param Session     $customerSession
     * @param Config      $config
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Session $customerSession,
        Config $config
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->session = $customerSession;
        $this->config = $config;

        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @return PageFactory
     */
    public function execute()
    {
        return $this->resultPageFactory->create();
    }

    public function dispatch(RequestInterface $request)
    {
        if (!$this->session->isLoggedIn()) {
            return $this->_redirect('customer/account/login');
        }

        if (!$this->config->isEnabled()) {
            return $this->_redirect($this->_redirect->getRefererUrl());
        }

        return parent::dispatch($request);
    }
}