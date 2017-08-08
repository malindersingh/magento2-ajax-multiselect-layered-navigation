<?php

namespace Codilar\LayeredNavigation\Block\Navigation;

use Magento\Catalog\Model\Layer\Filter\FilterInterface;

class FilterRenderer extends \Magento\LayeredNavigation\Block\Navigation\FilterRenderer
{

    protected $filter;
    protected $_helper;
    protected $_registry;
    protected $_categoryFactory;
    protected $_productCollectionFactory;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Codilar\LayeredNavigation\Helper\Data $helper,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = [])
    {
        $this->_registry = $registry;
        $this->_helper = $helper;
        $this->_categoryFactory = $categoryFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $data);

    }

    public function render(FilterInterface $filter)
    {
        $this->filter = $filter;

        $displayStatus = $this->_helper->getConfig('codilar_layered/general/display');
        
        $template = $this->getTemplateByConfig($displayStatus);
        $this->setTemplate($template);

        return parent::render($filter);
    }

    protected function getTemplateByConfig($displaySetting)
    {
        switch($displaySetting) {
            default:
                $template = "layer/filter/default.phtml";
                break;
        }
        return $template;
    }

    public function getCategory($categoryId)
    {
        $this->_category = $this->_categoryFactory->create();
        $this->_category->load($categoryId);
        return $this->_category;
    }

    public function getAppliedPriceFilter(){
        return isset($_GET['price'])?explode("-", $_GET['price']):null;
    }

    public function getMinPrice() {
        $products = $this->getCategory($this->_registry->registry('current_category')->getId())->getProductCollection();
        $products->addAttributeToSelect('*')->addAttributeToSelect('price')->addAttributeToSort('price','ASC');
        $minPrice = $products->getFirstItem()->getMinPrice();
        return $minPrice;
    }
    public function getMaxPrice() {
        $products = $this->getCategory($this->_registry->registry('current_category')->getId())->getProductCollection();
        $products->addAttributeToSelect('*')->addAttributeToSelect('price')->addAttributeToSort('price','ASC');
        $maxPrice = $products->getLastItem()->getMinPrice();
        return $maxPrice;
    }
}
