<?php
namespace Codilar\LayeredNavigation\Block\Navigation;

use Magento\Catalog\Model\Config;

class FiltersSelected extends \Magento\Framework\View\Element\Template {

    protected $_attrConfig;
    protected $request;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context,Config $attrConfig,\Magento\Framework\App\Request\Http $request)
    {
        $this->request = $request;
        $this->_attrConfig = $attrConfig;
        parent::__construct($context);
    }

    public function getParams() {
        $collParam = $this->request->getParams();
        unset($collParam['_']);
        unset($collParam['id']);

        $params = $collParam;
        $response = [];
        foreach ($params as $filter => $param){
            $values = explode(",", $param);
            foreach($values as $value){
                $response[$filter][] = $this->getSuperAttribute($filter, $value);
            }
        }
        return $response;
    }

    public function getSuperAttribute($attribute, $option)
    {
        $response = [];
        $attr = $this->getAttrIdByCode($attribute);
        $attrId = $attr->getSource()->getAllOptions();
        foreach ($attrId as $attrIndex => $attrGroup)
            if ($attrGroup['value'] == $option) {
                $response['label'] = $attrGroup['label'];
                $response['value'] = $option;
                return $response;
            }
        return 0;
    }

    public function getAttrIdByCode($attrName)
    {
        $attr = $this->_attrConfig->getAttribute('catalog_product', $attrName);
        return $attr;
    }
}