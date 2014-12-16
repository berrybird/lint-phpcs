<?php

namespace Berrybird\Tests\WhiteSpace;

use AbstractSniffUnitTest;

/**
 * Unit test class for the Parentheses sniff.
 *
 * @author     Kohana Team
 * @copyright  Copyright (C) 2011 Kohana Team
 * @license    BSD-3-Clause
 */
class ParenthesesUnitTest extends AbstractSniffUnitTest
{
    /**
     * {@inheritdoc}
     */
    public function getErrorList()
    {
        return array(
            5 => 1,
            6 => 2,
            7 => 2,
            8 => 1
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getWarningList()
    {
        return array();
    }
}
