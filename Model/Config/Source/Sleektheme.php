<?php
/**
 * My own options
 *
 */
namespace Codilar\LayeredNavigation\Model\Config\Source;
class Sleektheme implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'Default', 'label' => __('Default')],
        ];
    }
}
 
?>