<?php
namespace TemplateMonster\Megamenu\Observer;
use Magento\Catalog\Model\Category as C;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as O;
// 2020-01-22
class PrepareCategory implements ObserverInterface {
	/**
	 * 2020-01-22
	 * @param O $o
	 */
	function execute(O $o) {
		$c = $o['category']; /** @var C $c */
		/**
		 * 2020-01-20 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		 * «Uninitialized string offset: 0 in app/code/TemplateMonster/Megamenu/Observer/PrepareCategory.php on line 25»
		 * https://github.com/frugue/store/issues/4
		 */
		if ($i = $c['mm_image']) { /** @var array(int|string => mixed) $i */
			if (dfa($i, 'delete')) {
				$c['mm_image_additional_data'] = ['delete' => true];
			} 
			else {
				/**
				 * 2020-01-20 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
				 * «Undefined index: name in app/code/TemplateMonster/Megamenu/Observer/PrepareCategory.php on line 30»
				 * when a category is saved on a store level: https://github.com/frugue/store/issues/4
				 */
				$c['mm_image'] = dfa_deep($i, '0/name');
			}
		}
	}
}