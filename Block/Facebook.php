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

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Scandiweb\FacebookLogin\Model\Facebook\Config;
use Scandiweb\FacebookLogin\Model\Facebook\Facebook as FacebookModel;

class Facebook extends Template
{

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var FacebookModel
     */
    protected $facebook;

    /**
     * Login constructor.
     *
     * @param Context       $context
     * @param array         $data
     * @param Config        $config
     * @param FacebookModel $facebook
     */
    public function __construct(
        Context $context,
        array $data,
        Config $config,
        FacebookModel $facebook
    ) {
        $this->config = $config;
        $this->facebook = $facebook;

        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->config->isEnabled();
    }

    /**
     * @return string
     */
    public function getLoginUrl()
    {
        $facebookHelper = $this->facebook->getRedirectLoginHelper();

        return $facebookHelper->getLoginUrl($this->getUrl('facebook/login'), ['email']);
    }

}