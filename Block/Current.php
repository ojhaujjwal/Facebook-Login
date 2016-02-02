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

use Magento\Framework\App\DefaultPathInterface;
use Magento\Framework\View\Element\Html\Link\Current as MagentoCurrent;
use Magento\Framework\View\Element\Template\Context;
use Scandiweb\FacebookLogin\Model\Facebook\Config;

class Current extends MagentoCurrent
{

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        Context $context,
        DefaultPathInterface $defaultPath,
        array $data,
        Config $config
    ) {
        $this->config = $config;

        parent::__construct($context, $defaultPath, $data);
    }

    /**
     * @return null|string
     */
    protected function _toHtml()
    {
        if ($this->config->isEnabled()) {
            return parent::_toHtml();
        }

        return null;
    }

}