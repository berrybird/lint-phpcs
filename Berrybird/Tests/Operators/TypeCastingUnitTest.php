<?php

namespace Berrybird\Tests\Operators;

use AbstractSniffUnitTest;

/**
 * Unit test class for the TypeCasting sniff.
 *
 * @author     Kohana Team
 * @copyright  Copyright (C) 2011 Kohana Team
 * @license    BSD-3-Clause
 */
class TypeCastingUnitTest extends AbstractSniffUnitTest
{
    /**
     * {@inheritdoc}
     */
    public function getErrorList()
    {
        return array(
            3  => 1,
            6  => 1,
            7  => 1,
            11 => 1,
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
