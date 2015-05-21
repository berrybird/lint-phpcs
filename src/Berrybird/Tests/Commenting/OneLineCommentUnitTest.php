<?php

namespace Berrybird\Tests\Commenting;

use AbstractSniffUnitTest;

/**
 * Unit test class for OneLineCommentSniff.
 *
 * @author     Kohana Team
 * @copyright  Copyright (C) 2011 Kohana Team
 * @license    BSD-3-Clause
 */
class OneLineCommentUnitTest extends AbstractSniffUnitTest
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
