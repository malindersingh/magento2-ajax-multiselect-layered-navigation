<?php
namespace Codilar\LayeredNavigation\Helper;

use \Magento\Catalog\Model\Product\Attribute\Repository;
use Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Framework\UrlInterface;
use Magento\Framework\Registry;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

   protected $scopeConfig;
   protected $request;
   protected $urlBuilder;
   protected $_categoryFactory;
   protected $productAttributeRepository;
   private $registry;

    public function __construct(
        Registry $registry,
        Repository $productAttributeRepository,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        UrlInterface $url,
        \Magento\Framework\App\Request\Http $request,
        ScopeConfigInterface $scopeConfig
   )
   {
       $this->registry = $registry;
       $this->productAttributeRepository = $productAttributeRepository;
       $this->urlBuilder = $url;
       $this->request = $request;
       $this->scopeConfig = $scopeConfig;
       $this->_categoryFactory = $categoryFactory;
   }

   public function getConfig($config_path) {
      return $this->scopeConfig->getValue(
        $config_path,
        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
      );
   }

   public function urlEncode($id) {
       $currentUrl = $this->request->getParams();
       unset($currentUrl['_'],$currentUrl['id']);
       $currentPriceAvailable = false;
       foreach ($currentUrl as $filter => $value) {
           if ($filter == 'price') {
               $currentPriceAvailable = true;
               $currentPrice = $currentUrl['price'];
           }
       }
       $currentCategory = $this->_categoryFactory->create();
       $categoryName = $currentCategory->load($id)->getPath();
       $categoryPath = explode('/',$categoryName);
       $urlBuilder = [];
       $urlBuild = $this->urlBuilder->getUrl();
       foreach ($categoryPath as $item) {
           if (!($item == 1 || $item == 2)) {
               $currentCategoryName = strtolower($currentCategory->load($item)->getName());
               array_push($urlBuilder,$currentCategoryName);
           }
       }
       if (((sizeof($currentUrl) == 1 || sizeof($currentUrl)) && !$currentPriceAvailable) || (sizeof($currentUrl) > 1 && $currentPriceAvailable)) array_push($urlBuilder, 'searchby');
       foreach ($currentUrl as $filter => $values) {
           if (!($filter == 'id' || $filter == 'price')) {
               $urlAddValues = strtolower($filter);
               $params = explode(',', $values);
               foreach ($params as $param) {
                   $options = $this->productAttributeRepository->get($filter)->getOptions();
                   foreach ($options as $option) {
                       if ($option->getValue() == $param)
                           $urlAddValues .= '-' . str_replace(' ', '_', strtolower($option->getLabel()));
                   }
               }
               array_push($urlBuilder, $urlAddValues);
           }
       }
       if (!$currentPriceAvailable) {
           $url = $urlBuild . implode('/', $urlBuilder) . '.html';
           return $url;
       }
       else {
           $url = $urlBuild . implode('/', $urlBuilder) . '.html?price='.$currentPrice;
           return $url;
       }

   }

   public function getUrl($id) {
       $currentCategory = $this->_categoryFactory->create();
       $categoryName = $currentCategory->load($id)->getPath();
       $categoryPath = explode('/',$categoryName);
       $urlBuilder = [];
       $urlBuild = $this->urlBuilder->getUrl();
       foreach ($categoryPath as $item) {
           if (!($item == 1 || $item == 2)) {
               $currentCategoryName = strtolower($currentCategory->load($item)->getName());
               array_push($urlBuilder,$currentCategoryName);
           }
       }
       $url = $urlBuild . implode('/', $urlBuilder) . '.html';
       return $url;
   }

}