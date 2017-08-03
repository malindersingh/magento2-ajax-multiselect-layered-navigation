<?php
namespace Codilar\LayeredNavigation\Observer;

use \Magento\Catalog\Model\Product\Attribute\Repository;

class UrlDecode implements \Magento\Framework\Event\ObserverInterface {

    protected $_urlinterface;
    protected $productAttributeRepository;
    protected $_responseFactory;

    public function __construct(
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $urlinterface,
        Repository $productAttributeRepository
    ) {
        $this->_responseFactory = $responseFactory;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->_urlinterface = $urlinterface;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $parameters = $this->_urlinterface->getCurrentUrl();
        if (preg_match('/searchby/', $parameters)) {
            $keywords = preg_split("/searchby/", $parameters);
            $mainUrl = substr($keywords[0], 0, strrpos($keywords[0], '/')) . '.html';
            $removeLslash = ltrim($keywords[1], '/');
            $p = false;
            if (preg_match('/\?/',$removeLslash)) {
                $p = true;
                $priceBreak = explode('?', $removeLslash);
                $removeHtml = substr($priceBreak[0], 0, strrpos($priceBreak[0], '.'));
                $price = $priceBreak[1];
                $seperateFilters = explode('/', $removeHtml);
            } else {
                $removeHtml = substr($removeLslash, 0, strrpos($removeLslash, '.'));
                $seperateFilters = explode('/', $removeHtml);
            }
            $allFilters = [];
            foreach ($seperateFilters as $values) {
                $f = explode('-', $values);
                $filter = $f[0];
                $filterValues = [];
                $options = $this->productAttributeRepository->get($filter)->getOptions();
                foreach ($options as $option) {
                    for ($i = 1; $i < sizeof($f); $i++) {
                        if (strtolower($option->getLabel()) == str_replace('_', ' ', $f[$i])) {
                            array_push($filterValues, $option->getValue());
                        }
                    }
                }
                $singleFilterValues = implode(',', $filterValues);
                $singleFilter = $filter . '=' . $singleFilterValues;
                array_push($allFilters, $singleFilter);
            }
            $allFiltersCombine = implode('&', $allFilters);
            $p ? $url = $mainUrl . '?' . $allFiltersCombine . '&' . $price:$url = $mainUrl . '?' . $allFiltersCombine;
            echo file_get_contents($url);
            exit(0);
        }else {
            return;
        }
    }
}