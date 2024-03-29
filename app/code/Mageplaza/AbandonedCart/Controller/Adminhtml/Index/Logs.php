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
 * Class Logs
 * @package Mageplaza\AbandonedCart\Controller\Adminhtml\Index
 */
class Logs extends AbandonedCart
{
	/**
	 * @return \Magento\Framework\View\Result\Page
	 */
	public function execute()
	{
	    $resultPage = $this->_initAction();
		$resultPage->setActiveMenu('Mageplaza_AbandonedCart::log');
		$resultPage->getConfig()->getTitle()->prepend((__('Logs')));
		$resultPage->addBreadcrumb(__('AbandonedCart'), __('Logs'));

		return $resultPage;
	}
}
