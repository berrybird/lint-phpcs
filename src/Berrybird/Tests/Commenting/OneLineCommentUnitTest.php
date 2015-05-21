<?php
/**
 * Unit test class for OneLineCommentSniff.
 *
 * @group  Berrybird
 *
 * @author     Kohana Team
 * @copyright  Copyright (C) 2011 Kohana Team
 * @license    BSD-3-Clause
 */
class Berrybird_Tests_Commenting_OneLineCommentUnitTest extends AbstractSniffUnitTest
{
    /**
     * {@inheritdoc}
     */
    public function getErrorList()
    {
        return array(
            3 => 1,
            4 => 1
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
