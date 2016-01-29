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
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Scandiweb\FacebookLogin\Model\Facebook\Facebook;

class Index extends Action
{

    /**
     * @var Facebook
     */
    private $facebook;

    /**
     * Index constructor
     *
     * @param Context  $context
     * @param Facebook $facebook
     */
    public function __construct(Context $context, Facebook $facebook)
    {
        $this->facebook = $facebook;

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

            var_dump($accessToken);
        } catch (Exception $e) {
            var_dump('Ooops');
        }
    }
}