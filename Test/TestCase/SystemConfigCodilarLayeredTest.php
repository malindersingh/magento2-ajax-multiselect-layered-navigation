<?php
namespace Codilar\LayeredNavigation\Test\TestCase;

use Magento\Backend\Test\Page\Adminhtml\SystemConfigEdit;
use Magento\Backend\Test\Page\AdminAuthLogin;
use Magento\Mtf\TestCase\Injectable;
use Magento\Codilar\Test\Page\Adminhtml\SystemConfigCodilarLayered;

class SystemConfigCodilarLayeredTest extends Injectable
{
    /**
     * Search System Config page.
     *
     * @var SystemConfigCodilarLayered
     */
    private $configLayered;

    /**
     * Inject pages.
     *
     * @param SystemConfigCodilarLayered $configLayered
     * @return void
     */
    public function __inject(
        SystemConfigCodilarLayered $configLayered
    ) {
        $this->configLayered = $configLayered;
    }

    /**
     * Create Synonym group test.
     *
     * @param SystemConfigCodilarLayered $configLayered
     * @return void
     */
    public function test(SystemConfigCodilarLayered $configLayered)
    {
        $this->configLayered->open();
        $this->configLayered->getPageActions()->save();
    }
}