<?php
/**
 * Unit test class for the RegularExpression sniff.
 *
 * @group  Berrybird
 *
 * @author     Kohana Team
 * @copyright  Copyright (C) 2011 Kohana Team
 * @license    BSD-3-Clause
 */
class Berrybird_Tests_Functions_RegularExpressionUnitTest extends AbstractSniffUnitTest
{
    /**
     * {@inheritdoc}
     */
    public function getErrorList()
    {
        return array(
            3 => 1,
            5 => 1
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getWarningList()
    {
        return array(
            7 => 1
        );
    }
}
