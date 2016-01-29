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

use Facebook\Facebook as FacebookSdk;

class Facebook extends FacebookSdk
{

    /**
     * Facebook constructor
     *
     * @param Config $config
     *
     * @throws \Facebook\Exceptions\FacebookSDKException
     */
    public function __construct(Config $config)
    {
        parent::__construct(
            [
                'app_id'                => $config->getApiKey(),
                'app_secret'            => $config->getApiSecret(),
                'default_graph_version' => $config->getVersion()
            ]
        );
    }

}