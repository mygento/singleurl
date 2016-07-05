<?php

/**
 *
 *
 * @category Mygento
 * @package Mygento_Singleurl
 * @copyright Copyright © 2016 NKS LLC. (http://www.mygento.ru)
 */
class Mygento_Singleurl_Model_Rewrite_Url extends Mage_Catalog_Model_Url {

    //just one product
    public function refreshProductRewrite($productId, $storeId = null) {
        if (is_null($storeId)) {
            foreach ($this->getStores() as $store) {
                $this->refreshProductRewrite($productId, $store->getId());
            }
            return $this;
        }

        $product = $this->getResource()->getProduct($productId, $storeId);
        if ($product) {
            $store = $this->getStores($storeId);
            $storeRootCategoryId = $store->getRootCategoryId();

            // List of categories the product is assigned to, filtered by being within the store's categories root
            $categories = Mage::helper('singleurl')->getCategories($product->getId(), $storeId);
            $this->_rewrites = $this->getResource()->prepareRewrites($storeId, '', $productId);

            // Create product url rewrites
            foreach ($categories as $category) {
                $this->_refreshProductRewrite($product, $category);
            }

            // Remove all other product rewrites created earlier for this store - they're invalid now
            $excludeCategoryIds = array_keys($categories);
            $this->getResource()->clearProductRewrites($productId, $storeId, $excludeCategoryIds);

            unset($categories);
            unset($product);
        } else {
            // Product doesn't belong to this store - clear all its url rewrites including root one
            $this->getResource()->clearProductRewrites($productId, $storeId, array());
        }

        return $this;
    }

    /**
     * Refresh all product rewrites for designated store
     *
     * @param int $storeId
     * @return Mage_Catalog_Model_Url
     */
    public function refreshProductRewrites($storeId) {

        $excludeProductsDisabled = Mage::getStoreConfigFlag('dev/singleurl/disable');
        $excludeHidden = Mage::getStoreConfigFlag('dev/singleurl/notvisible');


        $this->_categories = array();
        $storeRootCategoryId = $this->getStores($storeId)->getRootCategoryId();
        $storeRootCategoryPath = $this->getStores($storeId)->getRootCategoryPath();
        $this->_categories[$storeRootCategoryId] = $this->getResource()->getCategory($storeRootCategoryId, $storeId);

        $lastEntityId = 0;
        $process = true;

        while ($process == true) {
            //todo - make join this status and visibility
            $products = $this->getResource()->getProductsByStore($storeId, $lastEntityId);
            if (!$products) {
                $process = false;
                break;
            }

            $this->_rewrites = $this->getResource()->prepareRewrites($storeId, false, array_keys($products));

            $loadCategories = array();
            foreach ($products as $product) {
                foreach (Mage::helper('singleurl')->getCategories($product->getId(), $storeId, true) as $categoryId) {
                    if (!isset($this->_categories[$categoryId])) {
                        $loadCategories[$categoryId] = $categoryId;
                    }
                }
            }

            if ($loadCategories) {
                foreach ($this->getResource()->getCategories($loadCategories, $storeId) as $category) {
                    $this->_categories[$category->getId()] = $category;
                }
            }

            foreach ($products as $product) {

                if ($excludeProductsDisabled && $product->getData('status') == Mage_Catalog_Model_Product_Status::STATUS_DISABLED) {
                    continue;
                }

                if ($excludeHidden && $product->getData('visibility') == Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE) {
                    continue;
                }




                $cnt_cats = count($product->getCategoryIds());
                if (Mage::getStoreConfig('dev/singleurl/level') == 'root' || $cnt_cats == 0) {
                    $this->_refreshProductRewrite($product, $this->_categories[$storeRootCategoryId]);
                    continue;
                }
                foreach (Mage::helper('singleurl')->getCategories($product->getId(), $storeId, true) as $categoryId) {
                    if ($categoryId != $storeRootCategoryId && isset($this->_categories[$categoryId])) {
                        if (strpos($this->_categories[$categoryId]['path'], $storeRootCategoryPath . '/') !== 0) {
                            continue;
                        }
                        $this->_refreshProductRewrite($product, $this->_categories[$categoryId]);
                    }
                }
            }

            unset($products);
            $this->_rewrites = array();
        }

        $this->_categories = array();
        return $this;
    }

}
