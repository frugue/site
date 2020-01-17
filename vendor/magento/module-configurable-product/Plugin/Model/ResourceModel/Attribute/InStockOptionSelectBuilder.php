<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ConfigurableProduct\Plugin\Model\ResourceModel\Attribute;

use Magento\CatalogInventory\Model\ResourceModel\Stock\Status;
use Magento\ConfigurableProduct\Model\ResourceModel\Attribute\OptionSelectBuilderInterface;
use Magento\Framework\DB\Select;

/**
 * Plugin for OptionSelectBuilderInterface to add stock status filter.
 */
class InStockOptionSelectBuilder
{
    /**
     * CatalogInventory Stock Status Resource Model.
     *
     * @var Status
     */
    private $stockStatusResource;
    
    /**
     * @param Status $stockStatusResource
     */
    public function __construct(Status $stockStatusResource)
    {
        $this->stockStatusResource = $stockStatusResource;
    }

    /**
     * Add stock status filter to select.
     *
     * @param OptionSelectBuilderInterface $subject
     * @param Select $select
     * @return Select
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetSelect(OptionSelectBuilderInterface $subject, Select $select)
    {
		/**
		 * 2019-03-25 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		 * «Upgrade Magento 2 from 2.1.9 to 2.3.0»: https://www.upwork.com/ab/f/contracts/21810726
		 * My patch allows to show all swatches (even out of stock) as we did in Magento 2.1.9.
		 * As I understand, the current plugin has been implemented only for performance improvements,
		 * so it is safe to disable it.
		 * https://github.com/magento/magento2/commits/2.3-develop/app/code/Magento/ConfigurableProduct/Plugin/Model/ResourceModel/Attribute/InStockOptionSelectBuilder.php
		 * The second part of the patch:
		 * @see \Magento\ConfigurableProduct\Model\Product\Type\Configurable::loadUsedProducts()
		 */
		if (false) {
			$select->joinInner(
				['stock' => $this->stockStatusResource->getMainTable()],
				'stock.product_id = entity.entity_id',
				[]
			)->where(
				'stock.stock_status = ?',
				\Magento\CatalogInventory\Model\Stock\Status::STATUS_IN_STOCK
			);
		}
        return $select;
    }
}
