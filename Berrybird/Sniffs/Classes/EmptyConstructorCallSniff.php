<?php

namespace Berrybird\Sniffs\Classes;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Sniff;

/**
 * Empty contructor sniff.
 *
 * This sniff prohibits the use of parentheses for constructor calls
 * that do not accept parameters.
 *
 * @author     Kohana Team
 * @copyright  Copyright (C) 2011 Kohana Team
 * @license    BSD-3-Clause
 */
class EmptyConstructorCallSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        return array(
            T_NEW
        );
    }

    /**
     * {@inheritdoc}
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $open = $phpcsFile->findNext(
            T_OPEN_PARENTHESIS,
            $stackPtr,
            null,
            false,
            null,
            true
        );

        if ($open !== false
            && $phpcsFile->getTokensAsString($open, 2) == '()') {
            $phpcsFile->addWarning(
                'Parentheses should not be used in calls to class constructors without parameters',
                $stackPtr
            );
        }
    }
}
