<?php
/**
 * Created by PhpStorm.
 * User: Blackhat
 * Date: 08/08/2017
 * Time: 00:59
 */

namespace Codilar\LayeredNavigation\Test\Unit\Block\Navigation;
use Codilar\LayeredNavigation\Block\Navigation\FilterRenderer;

class FilterRendererTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var FilterRenderer
     */

    protected $filter;

    protected $registry;
    protected $_helper,$_categoryFactory,$_productCollectionFactory,$_category;
    protected $filterRendererClass;

    const CATEGORY_ID = 4;

    public function setUp() {

        $this->registry = $this->getMockBuilder(\Magento\Framework\Registry::class)
            ->disableOriginalConstructor()->getMock();

        $this->_helper = $this->getMockBuilder(\Codilar\LayeredNavigation\Helper\Data::class)
            ->disableOriginalConstructor()->getMock();

        $this->_categoryFactory = $this->getMockBuilder(\Magento\Catalog\Model\CategoryFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()->getMock();

        $this->_productCollectionFactory = $this->getMockBuilder(\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()->getMock();

        $this->_category = $this->getMockBuilder(\Magento\Catalog\Model\Category::class)
            ->disableOriginalConstructor()->getMock();

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->filterRendererClass = $objectManager->getObject(
            \Codilar\LayeredNavigation\Block\Navigation\FilterRenderer::class,
            [
                'registry' => $this->registry,
                'helper' => $this->_helper,
                'categoryFactory' => $this->_categoryFactory,
                'productCollectionFactory' => $this->_productCollectionFactory
            ]
        );

    }

    public function testGetCategory() {

        $this->_category->expects($this->any())
            ->method('load')
            ->with(self::CATEGORY_ID)
            ->will($this->returnValue($this->_category));

        $this->_categoryFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_category));

        $this->assertEquals('object', gettype($this->filterRendererClass->getCategory(self::CATEGORY_ID)));
    }

//    public function testGetMinPrice() {
//
//        $this->_category->expects($this->any())
//            ->method('load')
//            ->with(self::CATEGORY_ID)
//            ->will($this->returnValue($this->_category));
//
//        $this->registry->expects($this->any())
//            ->method('registry')
//            ->with('current_category');
//
//        $this->registry->expects($this->any())
//            ->method('getId');
//
//        $this->_category->expects($this->any())
//            ->method('getProductCollection');
//
//        $this->_category->expects($this->any())
//            ->method('addAttributeToSelect')
//            ->with('*');
//
//        $this->_category->expects($this->any())
//            ->method('addAttributeToSelect')
//            ->with('price');
//
//        $this->_category->expects($this->any())
//            ->method('addAttributeToSort')
//            ->withAnyParameters('price','ASC');
//
//        $this->_category->expects($this->any())
//            ->method('getFirstItem');
//
//        $this->_category->expects($this->any())
//            ->method('getMinPrice')
//            ->will($this->returnValue(699));
//
//        $this->_categoryFactory->expects($this->any())
//            ->method('create')
//            ->will($this->returnValue($this->_category));
//
//        var_dump($this->filterRendererClass->getMinPrice());
//
//        $this->assertEquals('699', $this->filterRendererClass->getMinPrice());
//
//    }

}