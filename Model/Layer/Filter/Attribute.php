<?php
namespace Codilar\LayeredNavigation\Model\Layer\Filter;
//use Magento\CatalogSearch\Model\Layer\Filter\Attribute as CoreAttribute;

/**
 * Layer attribute filter
 */
class Attribute extends \Magento\CatalogSearch\Model\Layer\Filter\Attribute
{

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder
     * @param \Magento\Framework\Filter\StripTags $tagFilter
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Framework\Filter\StripTags $tagFilter,
        array $data = []
    ) {

        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $tagFilter,
            $data
        );
    }


    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        $attributeValue = $request->getParam($this->_requestVar);
        if (empty($attributeValue)) {
            return $this;
        }
//        $attribute = $this->getAttributeModel();
        /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $productCollection */
        $productCollection = $this->getLayer()
            ->getProductCollection();
//        $productCollection->addFieldToFilter('gold',6);
//        $label = $this->getOptionText($attributeValue);
//        $this->getLayer()
//            ->getState()
//            ->addFilter($this->_createItem('gold',6));
//        $this->getLayer()
//            ->getState()
//            ->addFilter($this->_createItem($label, $attributeValue));

//        $this->setItems([]); // set items to disable show filtering
        return $this;
    }
}