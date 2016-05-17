<?php

/**
 *
 *
 * @category Mygento
 * @package Mygento_Singleurl
 * @copyright Copyright © 2016 NKS LLC. (http://www.mygento.ru)
 */
if (Mage::helper('core')->isModuleEnabled('Mygento_Seo')) {

    class Mygento_Singleurl_Model_Rewrite_Product_Url_Abstract extends Mygento_Seo_Model_Product_Url {
        
    }

} else {

    class Mygento_Singleurl_Model_Rewrite_Product_Url_Abstract extends Mage_Catalog_Model_Product_Url {
        
    }

}

class Mygento_Singleurl_Model_Rewrite_Product_Url extends Mygento_Singleurl_Model_Rewrite_Product_Url_Abstract {

    public function getUrl(Mage_Catalog_Model_Product $product, $params = array()) {
        $read = Mage::getModel('core/resource')->getConnection('core_read');
        $select = $read->select();
        $select->from(Mage::getConfig()->getTablePrefix() . 'core_url_rewrite');
        $select->where('product_id = ?', $product->getId());
        $select->limit(1);
        $row = $read->fetchRow($select);
        return Mage::getBaseUrl('link') . $row['request_path'];
    }

}
