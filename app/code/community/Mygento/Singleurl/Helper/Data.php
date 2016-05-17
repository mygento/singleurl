<?php

/**
 *
 *
 * @category Mygento
 * @package Mygento_Singleurl
 * @copyright Copyright © 2016 NKS LLC. (http://www.mygento.ru)
 */
class Mygento_Singleurl_Helper_Data extends Mage_Core_Helper_Abstract {

    public function addLog($text) {
        if (Mage::getStoreConfig('singleurl/general/debug')) {
            Mage::log($text, null, 'singleurl.log', true);
        }
    }

    public function getCategories($productId, $storeId, $justIds = false) {
        $collection = Mage::getModel('catalog/category')->getCollection();
        $collection->getSelect()->joinLeft(
                array('cat' => Mage::getSingleton('core/resource')->getTableName('catalog/category_product')), 'e.entity_id = cat.category_id', array('cat_position' => 'position')
        );
        $collection->getSelect()->where('product_id = ?', $productId);
        switch (Mage::getStoreConfig('dev/singleurl/level')) {
            case "long":
                $collection->getSelect()->order('level DESC');
                break;
            case "short":
                $collection->getSelect()->order('level ASC');
                break;
            default:
                $collection->getSelect()->where('level = ?', 1);
        }
        $collection->getSelect()->order('cat_position ASC');
        $collection->getSelect()->limit(1);
        //echo $collection->getSelect();
        if (!$justIds) {
            return $collection;
        }
        return $collection->getColumnValues('entity_id');
    }

}
