<?php

namespace Berrybird\Tests\Classes;

use AbstractSniffUnitTest;

/**
 * Unit test class for EmptyConstructorCallSniff.
 *
 * @author     Kohana Team
 * @copyright  Copyright (C) 2011 Kohana Team
 * @license    BSD-3-Clause
 */
class EmptyConstructorCallUnitTest extends AbstractSniffUnitTest
{
    /**
     * {@inheritdoc}
     */
    public function getErrorList()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getWarningList()
    {
        return array(
            3 => 1
        );
    }
}
