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

class Mygento_Singleurl_Model_Rewrite_Url extends Mygento_Singleurl_Model_Rewrite_Url_Abstract {

    public function getUrl(Mage_Catalog_Model_Product $product, $params = array()) {
        return Mage_Catalog_Model_Product_Url::getUrl($product, $params);
    }

}
