<?php
namespace Codilar\LayeredNavigation\Block\Navigation;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\DataObject\IdentityInterface;

class ProductList extends  \Magento\Catalog\Block\Product\ListProduct
{
    protected $_productModel;
    protected $request;

	public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Catalog\Model\ProductFactory $product,
        array $data = []
    ) {
        $this->request = $request;
		$this->_catalogLayer = $layerResolver->get();
		$this->_productModel = $product->create();
		$this->categoryRepository = $categoryRepository;

        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data
        );
    }

    public function _prepareLayout()
	{
	    $this->setChild('toolbar',
	        $this->getLayout()->createBlock('Magento\Catalog\Block\Product\ProductList\Toolbar')
	    )->setCollection($this->_getProductCollection());
	    return parent::_prepareLayout();
	}

	public function getLoadedProductCollection()
    {
        $collParam = $this->request->getParams();
        unset($collParam['_']);
        unset($collParam['id']);

    	$collection = $this->filterCollection($this->_productModel->getCollection()->addAttributeToSelect('*'), $collParam);

    	$this->_productCollection = $collection;
    	return $this->_productCollection;
    }

    public function getLayer()
    {
        $collParam = $this->request->getParams();
        unset($collParam['_']);
        unset($collParam['id']);

        $collection = $this->filterCollection($this->_productModel->getCollection()->addAttributeToSelect('*'), $collParam);
        return $this->_catalogLayer->prepareProductCollection($this->_productModel->getCollection());
    }

    /**
     * Retrieve loaded category collection
     *
     * @return AbstractCollection
     */
    protected function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            $layer = $this->getLayer();
            /* @var $layer \Magento\Catalog\Model\Layer */
            if ($this->getShowRootCategory()) {
                $this->setCategoryId($this->_storeManager->getStore()->getRootCategoryId());
            }

            // if this is a product view page
            if ($this->_coreRegistry->registry('product')) {
                // get collection of categories this product is associated with
                $categories = $this->_coreRegistry->registry('product')
                    ->getCategoryCollection()->setPage(1, 1)
                    ->load();
                // if the product is associated with any category
                if ($categories->count()) {
                    // show products from this category
                    $this->setCategoryId(current($categories->getIterator()));
                }
            }

            $origCategory = null;
            if ($this->getCategoryId()) {
                try {
                    $category = $this->categoryRepository->get($this->getCategoryId());
                } catch (NoSuchEntityException $e) {
                    $category = null;
                }

                if ($category) {
                    $origCategory = $layer->getCurrentCategory();
                    $layer->setCurrentCategory($category);
                }
            }

            $this->_productCollection = $layer->getProductCollection();


            $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

            if ($origCategory) {
                $layer->setCurrentCategory($origCategory);
            }
        }
        return $this->_productCollection;
    }

    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->_getProductCollection();

        // use sortable parameters
        $orders = $this->getAvailableOrders();
        if ($orders) {
            $toolbar->setAvailableOrders($orders);
        }
        $sort = $this->getSortBy();
        if ($sort) {
            $toolbar->setDefaultOrder($sort);
        }
        $dir = $this->getDefaultDirection();
        if ($dir) {
            $toolbar->setDefaultDirection($dir);
        }
        $modes = $this->getModes();
        if ($modes) {
            $toolbar->setModes($modes);
        }

        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);

        $this->setChild('toolbar', $toolbar);
        $this->_eventManager->dispatch(
            'catalog_block_product_list_collection',
            ['collection' => $this->_getProductCollection()]
        );

        $this->_getProductCollection()->load();

        return parent::_beforeToHtml();
    }


    public function filterCollection($collection, $filters){
    	foreach($filters as $filter => $value){
    		if($filter == "price"){
    			$prices = explode("-", $value);
    			$prices[0] = (int) $prices[0];
    			$prices[1] = $prices[1]!=""?(int) $prices[1]:PHP_INT_MAX;
    			$collection->addAttributeToFilter('price', array('gteq' => $prices[0]))->addAttributeToFilter('price', array('lteq' => $prices[1]));
    			continue;
    		}
    		$value = explode(",", $value);
    		$collection->addAttributeToFilter($filter, array('in'=>$value));
    	}
    	return $collection;

    }

}