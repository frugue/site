<?php

namespace TemplateMonster\NewsletterPopup\Observer;

use Magento\Framework\UrlInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Add dynamic styles link.
 *
 * @package TemplateMonster\NewsletterPopup\Observer
 */
class AddDynamicStyles implements ObserverInterface
{
    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var array
     */
    protected $_cssOptions = [
        'content_type' => 'link',
        'rel' => 'stylesheet',
        'type' => 'text/css',
        'src_type' => 'url',
        'media' => 'all'
    ];

    /**
     * AddDynamicStyles constructor.
     *
     * @param UrlInterface $urlBuilder
     */
    public function __construct(UrlInterface $urlBuilder)
    {
        $this->_urlBuilder = $urlBuilder;
    }

    /**
     * @inheritdoc
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $observer->getData('layout');

        $pageConfig = $layout->getReaderContext()->getPageConfigStructure();
		/**
		 * 2019-03-23 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		 * «Upgrade Magento 2 from 2.1.9 to 2.3.0»: https://www.upwork.com/ab/f/contracts/21810726
		 * I have fixed the issue:
		 * «Notice: Undefined index: src
		 * in vendor/magento/framework/View/Page/Config/Generator/Head.php on line 126».
		 * 2019-03-24
		 * The previous code was:
		 * 		$pageConfig->addAssets($this->_getCssUrl(), $this->_cssOptions);
		 */
        $pageConfig->addAssets($src = $this->_getCssUrl(), $this->_cssOptions + ['src' => $src]);
    }

    /**
     * Get CSS url.
     *
     * @return string
     */
    protected function _getCssUrl()
    {
        return $this->_urlBuilder->getUrl('newsletter_popup/css/index');
    }
}