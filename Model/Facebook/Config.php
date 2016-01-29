<?php
/**
 * Scandiweb_FacebookLogin
 *
 * @category    Scandiweb
 * @package     Scandiweb_FacebookLogin
 * @author      Viktors Vipolzovs <info@scandiweb.com>
 * @copyright   Copyright (c) 2016 Scandiweb, Ltd (http://scandiweb.com)
 */

namespace Scandiweb\FacebookLogin\Model\Facebook;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    const VERSION = 'v2.5';
    const XML_PATH_SCOPE = 'social_login/facebook_login/scope';
    const XML_PATH_API_KEY = 'social_login/facebook_login/api_key';
    const XML_PATH_API_SECRET = 'social_login/facebook_login/api_secret';

    /**
     * Facebook config constructor
     *
     * @param ScopeConfigInterface $scopeConfigInterface
     */
    public function __construct(ScopeConfigInterface $scopeConfigInterface)
    {
        $this->scopeConfigInterface = $scopeConfigInterface;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfigInterface->getValue(self::XML_PATH_SCOPE)
        && $this->getApiKey()
        && $this->getApiSecret();
    }
    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->scopeConfigInterface->getValue(self::XML_PATH_API_KEY);
    }
    /**
     * @return string
     */
    public function getApiSecret()
    {
        return $this->scopeConfigInterface->getValue(self::XML_PATH_API_SECRET);
    }
    /**
     * @return string
     */
    public function getVersion()
    {
        return self::VERSION;
    }
}