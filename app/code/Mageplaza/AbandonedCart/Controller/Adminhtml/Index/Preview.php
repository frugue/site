<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_AbandonedCart
 * @copyright   Copyright (c) 2017 Mageplaza (https://www.mageplaza.com/)
 * @license     http://mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\AbandonedCart\Controller\Adminhtml\Index;

use Mageplaza\AbandonedCart\Controller\Adminhtml\AbandonedCart;

/**
 * Class Preview
 * @package Mageplaza\AbandonedCart\Controller\Adminhtml\Index
 */
class Preview extends AbandonedCart
{
	/**
     * Preview abandoned cart email action
     *
     * @return void
     */
    public function execute()
    {
        try {
            $this->_view->loadLayout();
            $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Email Preview'));
            $this->_view->renderLayout();
        } catch (\Exception $e) {
            $this->messageManager->addError(__('An error occurred. The email template can not be opened for preview.'));
            $this->_redirect('abandonedcart/*/');
        }
    }
}
