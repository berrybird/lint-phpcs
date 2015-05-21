<?php
/**
 * Unit test class for the TernaryOperator sniff.
 *
 * @group  Berrybird
 *
 * @author     Kohana Team
 * @copyright  Copyright (C) 2011 Kohana Team
 * @license    BSD-3-Clause
 */
class Berrybird_Tests_Operators_TernaryOperatorUnitTest extends AbstractSniffUnitTest
{
    /**
     * {@inheritdoc}
     */
    public function getErrorList()
    {
        return array(
            4 => 2,
            6 => 2
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
