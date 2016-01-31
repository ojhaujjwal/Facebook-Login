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

use Magento\Framework\View\Element\Messages as MagentoMessage;

class Messages extends MagentoMessage
{

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->addMessages($this->messageManager->getMessages(true));
        return parent::_prepareLayout();
    }

}