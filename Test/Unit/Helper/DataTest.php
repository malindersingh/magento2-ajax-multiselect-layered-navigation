<?php
/**
 * Created by PhpStorm.
 * User: Blackhat
 * Date: 04/08/2017
 * Time: 19:11
 */

namespace Codilar\LayeredNavigation\Test\Unit\Helper;
use Codilar\LayeredNavigation\Helper\Data;

class DataTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Data
     */

    protected $url;

    /**
     * @var string
     */
    protected $expectedMessage;
    protected $categoryFactory;
    protected $registry;
    protected $productAttributeRepository;
    protected $_url,$request,$scopeConfig;
    protected $dataClass,$category;

    const SCOPE = 1;
    const CATEGORY_ID = 4;
    const PATH = 'http://localhost/extension';

    public function setUp() {

        $this->categoryFactory = $this->getMockBuilder(\Magento\Catalog\Model\CategoryFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()->getMock();

        $this->registry = $this->getMockBuilder(\Magento\Framework\Registry::class)
            ->disableOriginalConstructor()->getMock();

        $this->productAttributeRepository = $this->getMockBuilder(\Magento\Catalog\Model\Product\Attribute\Repository::class)
            ->disableOriginalConstructor()->getMock();

        $this->_url = $this->getMockBuilder(\Magento\Framework\UrlInterface::class)
            ->disableOriginalConstructor()->getMock();

        $this->request = $this->getMockBuilder(\Magento\Framework\App\Request\Http::class)
            ->disableOriginalConstructor()->getMock();

        $this->scopeConfig = $this->getMockBuilder(\Magento\Framework\App\Config\ScopeConfigInterface::class)
            ->disableOriginalConstructor()->getMock();

        $this->category = $this->getMockBuilder('Magento\Catalog\Model\Category')
            ->disableOriginalConstructor()
            ->setMethods(['load'])
            ->getMock();

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->dataClass = $objectManager->getObject(
            \Codilar\LayeredNavigation\Helper\Data::class,
            [
                'registry' => $this->registry,
                'productAttributeRepository' => $this->productAttributeRepository,
                'url' => $this->_url,
                'request' => $this->request,
                'scopeConfig' => $this->scopeConfig,
                'categoryFactory' => $this->categoryFactory
            ]
        );

    }

    public function testGetConfig() {

        $this->scopeConfig
            ->method('getValue')
            ->willReturn(self::SCOPE);

        $this->assertEquals(self::SCOPE, $this->dataClass->getConfig('codilar_layered/general/enable'));
    }

    public function testGetUrl() {

        $this->category->expects($this->any())
            ->method('load')
            ->with(self::CATEGORY_ID)
            ->will($this->returnValue($this->category));

        $this->category->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('beads'));

        $this->category->expects($this->once())
            ->method('getPath')
            ->will($this->returnValue($this->category));

        $this->categoryFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->category));

        $this->_url->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue(self::PATH));

        $this->assertEquals('http://localhost/extension/charms/beads.html', $this->dataClass->getUrl(4));
    }

}