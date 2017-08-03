<?php

namespace Codilar\LayeredNavigation\Test\Constraint;

use Magento\Codilar\Test\Page\Adminhtml\SystemConfigCodilarLayered;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assertion to check Success Save Message for Synonyms.
 */
class AssertUrlSuccessSaveMessage extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'You saved the rule.';

    /**
     * Check Success Save Message for Synonyms.
     *
     * @param SystemConfigCodilarLayered $configLayered
     * @return void
     */
    public function processAssert(SystemConfigCodilarLayered $configLayered)
    {
        $actualMessage = $configLayered->getMessagesBlock()->getSuccessMessage();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Text success save message is displayed
     *
     * @return string
     */
    public function toString()
    {
        return 'Assert that success message is displayed.';
    }
}