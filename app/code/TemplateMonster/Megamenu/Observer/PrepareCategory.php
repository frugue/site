<?php
namespace TemplateMonster\Megamenu\Observer;

use Magento\Framework\Event\ObserverInterface;

use TemplateMonster\Megamenu\Helper\Data;

class PrepareCategory implements ObserverInterface
{
	protected $_helper;

	public function __construct(
		Data $helper
	) {
		$this->_helper = $helper;
	}

	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$category = $observer->getCategory();
		/**
		 * 2020-01-20 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		 * «Uninitialized string offset: 0 in app/code/TemplateMonster/Megamenu/Observer/PrepareCategory.php on line 25»
		 * https://github.com/frugue/store/issues/4
		 */
		if ($image = $category->getMmImage()) {
			if (isset($image['delete']) && $image['delete']) {
				$category->setData('mm_image_additional_data', ['delete' => true]);
			} else {
				$category->setMmImage($image[0]['name']);
			}
		}
	}
}