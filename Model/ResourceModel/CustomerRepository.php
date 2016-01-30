<?php
/**
 * Scandiweb_FacebookLogin
 *
 * @category    Scandiweb
 * @package     Scandiweb_FacebookLogin
 * @author      Viktors Vipolzovs <info@scandiweb.com>
 * @copyright   Copyright (c) 2016 Scandiweb, Ltd (http://scandiweb.com)
 */

namespace Scandiweb\FacebookLogin\Model\ResourceModel;

use Magento\Customer\Model\ResourceModel\CustomerRepository as MagentoCustomerRepository;
use Scandiweb\FacebookLogin\Api\CustomerRepositoryInterface;

class CustomerRepository extends MagentoCustomerRepository implements CustomerRepositoryInterface
{

    /**
     * Retrieve customer by facebook id
     *
     * @param int $facebookId
     * @return \Magento\Customer\Api\Data\CustomerInterface | null
     */
    public function getByFacebookId($facebookId)
    {
        /** @var \Magento\Customer\Model\ResourceModel\Customer\Collection $collection */
        $collection = $this->customerFactory->create()->getCollection();

        /** @var $customer \Magento\Customer\Api\Data\CustomerInterface */
        $customer = $collection
            ->addFieldToSelect('*')
            ->addAttributeToSelect('sf_id')
            ->addFieldToFilter('website_id', $this->storeManager->getWebsite()->getId())
            ->addAttributeToFilter('sf_id', $facebookId)
            ->load()
            ->getFirstItem();

        if ($customer->getId()) {
            return $customer;
        }

        return null;
    }

}