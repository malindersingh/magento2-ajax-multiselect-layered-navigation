<?php
namespace Codilar\LayeredNavigation\Controller\FilterData;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

Class Data extends \Magento\Framework\App\Action\Action {

    protected $fillFactory;
    protected $request;
    protected $url;
    protected $_helper;

	public function __construct(
	    \Codilar\LayeredNavigation\Helper\Data $url,
        \Magento\Framework\App\Request\Http $request,
		Context $context,
		PageFactory $pageFactory,
        \Codilar\LayeredNavigation\Helper\Data $config,
		\Magento\Catalog\Model\ProductFactory $product
	) {
	    $this->url = $url;
        $this->_helper = $config;
        $this->request = $request;
		$this->resultPageFactory = $pageFactory;
		$this->_productModel = $product->create();
        parent::__construct($context);
    }

    public function execute() {
    	$resultPage = $this->resultPageFactory ->create();
    	$collParam = $this->request->getParams();
    	$catId = $this->request->getParam('id');
    	unset($collParam['_'],$collParam['id']);
        foreach($collParam as $filter => $value){

            $value = explode(",", $value);
        }

    	$collection = $this->filterCollection($this->_productModel->getCollection()->addAttributeToSelect('*'), $collParam);

        $response = [];
    	$productList = $resultPage->getLayout()
	        		->createBlock('Codilar\LayeredNavigation\Block\Navigation\ProductList')->unsetChild('toolbar');
        $productList->addChild('toolbar', 'Magento\Catalog\Block\Product\ProductList\Toolbar')->setCollection($collection);
        // print_r($productList->getChildHtml('toolbar'));

        $selectedFilters = $resultPage->getLayout()
                             ->createBlock('Codilar\LayeredNavigation\Block\Navigation\FiltersSelected');
        $response['filters'] = $selectedFilters->setTemplate('Codilar_LayeredNavigation::layer/filter/filtersSelected.phtml')->toHtml();

        $response['products'] = $productList->setTemplate('Magento_Catalog::product/list.phtml')->toHtml();

        $status = $this->_helper->getConfig('codilar_layered/seo/enable_seo');
        $status?$response['url'] = $this->url->urlEncode($catId):$response['url']=$collParam;
        $response['config'] = $status;
        if (!$status) $response['getUrl'] =  $this->url->getUrl($catId);
        echo json_encode($response);
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
