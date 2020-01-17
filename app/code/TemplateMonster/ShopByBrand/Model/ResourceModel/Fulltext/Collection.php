<?php
namespace TemplateMonster\ShopByBrand\Model\ResourceModel\Fulltext;
/**
 * 2018-09-15
 * "«Call to undefined method
 * TemplateMonster\ShopByBrand\Model\ResourceModel\Fulltext\Collection\Interceptor::getMemRequestBuilder()»
 * after the Amasty's «Improved Layered Navigation» module's installation":
 * https://github.com/frugue/core/issues/40
 */
class Collection extends \Amasty\Shopby\Model\ResourceModel\Fulltext\Collection {
    protected function _renderFiltersBefore(){
        parent::_renderFiltersBefore();
        $this->_totalRecords = null;
    }
}