<?php

namespace MagicToolbox\MagicZoomPlus\Helper;

use Magento\Catalog\Model\Product;

/**
 * Class ConfigurableData
 * Helper class for getting options
 *
 */
class ConfigurableData extends \Magento\ConfigurableProduct\Helper\Data
{
    /**
     * Helper
     *
     * @var \MagicToolbox\MagicZoomPlus\Helper\Data
     */
    public $magicToolboxHelper = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * Enable effect
     *
     * @var bool
     */
    protected $isEffectEnable = false;

    /**
     * Use original gallery
     *
     * @var bool
     */
    protected $useOriginalGallery = true;

    /**
     * Gallery data
     *
     * @var array
     */
    protected $galleryData = [];

    /**
     * Magic 360 icon url
     *
     * @var string
     */
    protected $spinIconUrl = '';

    /**
     * Original gallery data
     *
     * @var array
     */
    protected $originalGalleryData;

    /**
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \MagicToolbox\MagicZoomPlus\Helper\Data $magicToolboxHelper
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Catalog\Helper\Image $imageHelper,
        \MagicToolbox\MagicZoomPlus\Helper\Data $magicToolboxHelper,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($imageHelper);
        $this->magicToolboxHelper = $magicToolboxHelper;
        $this->coreRegistry = $registry;
        $this->spinIconUrl = $this->imageHelper->getDefaultPlaceholderUrl('thumbnail');
    }

    /**
     * Retrieve collection of gallery images
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return \Magento\Framework\Data\Collection|null
     */
    public function getGalleryImages(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        return ($this->isEffectEnable && !$this->useOriginalGallery) ? null : parent::getGalleryImages($product);
    }

    /**
     * Get Options for Configurable Product Options
     *
     * @param \Magento\Catalog\Model\Product $currentProduct
     * @param array $allowedProducts
     * @return array
     */
    public function getOptions($currentProduct, $allowedProducts)
    {
        $isEnabled = false;

        $data = $this->coreRegistry->registry('magictoolbox');
        if ($data && $data['current'] != 'product.info.media.image') {

            foreach ($data['blocks'] as $key => $block) {
                if (!in_array($key, ['product.info.media.image', 'product.info.media.magic360']) && $block) {
                    $this->useOriginalGallery = false;
                    break;
                }
            }

            $galleryBlock = $data['blocks'][$data['current']];
            $toolObj = $galleryBlock->magicToolboxHelper->getToolObj();
            $isEnabled = !$toolObj->params->checkValue('enable-effect', 'No', 'product');
            if ($isEnabled) {
                if (!$this->useOriginalGallery) {
                    $productId = $currentProduct->getId();
                    $this->galleryData[$productId] = $galleryBlock->renderGalleryHtml($currentProduct)->getRenderedHtml($productId);
                }
                $allProducts = $currentProduct->getTypeInstance()->getUsedProducts($currentProduct, null);
                foreach ($allProducts as $product) {
                    $productId = $product->getId();
                    $this->galleryData[$productId] = $galleryBlock->renderGalleryHtml($product, true)->getRenderedHtml($productId);
                }
                $this->isEffectEnable = true;
            }
        }

        $data = $this->coreRegistry->registry('magictoolbox_category');
        if ($data && $data['current-renderer'] == 'configurable.magiczoomplus') {
            $this->useOriginalGallery = false;
            $productId = $currentProduct->getId();
            $this->galleryData[$productId] = $this->magicToolboxHelper->getHtmlData($currentProduct, false, ['small_image']);

            $allProducts = $currentProduct->getTypeInstance()->getUsedProducts($currentProduct, null);
            foreach ($allProducts as $product) {
                $productId = $product->getId();
                $this->galleryData[$productId] = $this->magicToolboxHelper->getHtmlData($product, true, ['image', 'small_image', 'thumbnail']);
            }
            $this->isEffectEnable = true;
        }

        $options = parent::getOptions($currentProduct, $allowedProducts);

        if ($isEnabled && $this->useOriginalGallery) {

            $spinIconPath = $galleryBlock->getMagic360IconPath();
            if ($spinIconPath) {
                $this->spinIconUrl = $galleryBlock->magic360ImageHelper
                    ->init(null, 'product_page_image_small', ['width' => null, 'height' => null])
                    ->setImageFile($spinIconPath)
                    ->getUrl();
            }

            $productImages = $this->getProductImagesData($allowedProducts);

            $options['images'] = $this->updateImagesData($productImages);
        }

        return $options;
    }

    /**
     * Get images data for configurable product options
     *
     * @param array $allowedProducts
     * @return array
     */
    public function getProductImagesData($allowedProducts)
    {
        $images = [];
        foreach ($allowedProducts as $product) {
            $productId = $product->getId();
            $productImages = $this->getGalleryImages($product) ?: [];
            $productMainImage = $product->getImage();
            foreach ($productImages as $image) {
                $images[$productId][] = [
                    'thumb' => $image->getData('small_image_url'),
                    'img' => $image->getData('medium_image_url'),
                    'full' => $image->getData('large_image_url'),
                    'caption' => $image->getLabel(),
                    'position' => $image->getPosition(),
                    'isMain' => $image->getFile() == $productMainImage,
                    'type' => str_replace('external-', '', $image->getMediaType()),
                    'videoUrl' => $image->getVideoUrl(),
                ];
            }
        }
        return $images;
    }

    /**
     * Update images data with spin data
     *
     * @param array $data
     * @return array
     */
    public function updateImagesData($data = [])
    {
        if (!isset($this->originalGalleryData)) {
            foreach ($data as $productId => &$images) {
                //NOTE: if we have 360 data for this product
                if (!empty($this->galleryData[$productId])) {
                    //NOTE: reposition images data
                    foreach ($images as &$image) {
                        $image['position'] = (int)$image['position'] + 1;
                        $image['isMain'] = false;
                    }
                    //NOTE: add 360 data
                    array_unshift($images, [
                        'magic360' => 'Magic360-product-'.$productId,
                        'thumb' => $this->spinIconUrl,
                        'html' => '<div class="fotorama__select">'.$this->galleryData[$productId].'</div>',
                        'caption' => '',
                        'position' => 0,
                        'isMain' => true,
                        'fit' => 'none',
                        'type' => 'magic360',
                        'videoUrl' => null
                    ]);
                }
                //NOTE: clear unnecessary 360 data (to leave only 360 data for products that does not have images)
                if (isset($this->galleryData[$productId])) {
                    unset($this->galleryData[$productId]);
                }
            }

            //NOTE: product that has no images but has 360 images
            foreach ($this->galleryData as $productId => $html) {
                if (!empty($html)) {
                    $data[$productId] = [];
                    $data[$productId][] = [
                        'magic360' => 'Magic360-product-'.$productId,
                        'thumb' => $this->spinIconUrl,
                        'html' => '<div class="fotorama__select">'.$this->galleryData[$productId].'</div>',
                        'caption' => '',
                        'position' => 0,
                        'isMain' => true,
                        'fit' => 'none',
                        'type' => 'magic360',
                        'videoUrl' => null
                    ];
                }
            }
            //NOTE: clear 360 data (they are no longer needed)
            $this->galleryData = [];

            $this->originalGalleryData = $data;
        }
        return $this->originalGalleryData;
    }

    /**
     * Get original gallery data
     *
     * @return array
     */
    public function getOriginalGalleryData()
    {
        return $this->originalGalleryData;
    }

    /**
     * Get gallery data
     *
     * @return array
     */
    public function getGalleryData()
    {
        return $this->galleryData;
    }

    /**
     * Use original gallery flag
     *
     * @return bool
     */
    public function useOriginalGallery()
    {
        return $this->useOriginalGallery;
    }

    /**
     * Get registry
     *
     * @return \Magento\Framework\Registry
     */
    public function getRegistry()
    {
        return $this->coreRegistry;
    }
}
