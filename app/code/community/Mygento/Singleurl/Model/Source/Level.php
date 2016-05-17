<?php

/**
 *
 *
 * @category Mygento
 * @package Mygento_Singleurl
 * @copyright Copyright © 2016 NKS LLC. (http://www.mygento.ru)
 */
class Mygento_Singleurl_Model_Source_Level {

    public function toOptionArray() {
        return array(
            array('value' => 'root', 'label' => Mage::helper('singleurl')->__('Root')),
            array('value' => 'long', 'label' => Mage::helper('singleurl')->__('Longest category level')),
            array('value' => 'short', 'label' => Mage::helper('singleurl')->__('Shortest category level')),
        );
    }

}
