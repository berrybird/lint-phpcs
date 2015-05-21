<?php
/**
 * Unit test class for EmptyConstructorCallSniff.
 *
 * @group  Berrybird
 *
 * @author     Kohana Team
 * @copyright  Copyright (C) 2011 Kohana Team
 * @license    BSD-3-Clause
 */
class Berrybird_Tests_Classes_EmptyConstructorCallUnitTest extends AbstractSniffUnitTest
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
