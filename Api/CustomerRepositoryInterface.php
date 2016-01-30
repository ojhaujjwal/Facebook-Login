<?php
/**
 * Scandiweb_FacebookLogin
 *
 * @category    Scandiweb
 * @package     Scandiweb_FacebookLogin
 * @author      Viktors Vipolzovs <info@scandiweb.com>
 * @copyright   Copyright (c) 2016 Scandiweb, Ltd (http://scandiweb.com)
 */

namespace Scandiweb\FacebookLogin\Api;

use Magento\Customer\Api\CustomerRepositoryInterface as MagentoCustomerRepositoryInterface;

interface CustomerRepositoryInterface extends MagentoCustomerRepositoryInterface
{

    /**
     * Retrieve customer by facebook id
     *
     * @param int $facebookId
     * @return \Magento\Customer\Api\Data\CustomerInterface | null
     */
    public function getByFacebookId($facebookId);

}