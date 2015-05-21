<?php
/**
 * Comparison operators sniff.
 *
 * Throws errors if TRUE, FALSE, or NULL comes before comparison
 * operator and variable after.
 *
 * @author     Kohana Team
 * @copyright  Copyright (C) 2011 Kohana Team
 * @license    BSD-3-Clause
 */
class Berrybird_Sniffs_Operators_ComparisonOperatorSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        return array(
            T_IS_GREATER_OR_EQUAL,
            T_IS_SMALLER_OR_EQUAL,
            T_IS_EQUAL,
            T_IS_NOT_EQUAL,
            T_IS_IDENTICAL,
            T_IS_NOT_IDENTICAL,
            T_IS_NOT_EQUAL,
            T_GREATER_THAN,
            T_LESS_THAN
        );
    }

    /**
     * {@inheritdoc}
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $beforePtr = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);
        $afterPtr  = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);

        if ($tokens[$afterPtr]['type'] == 'T_VARIABLE') {
            switch ($tokens[$beforePtr]['type']) {
                case 'T_STRING':
                    $beforePtr = $phpcsFile->findPrevious(T_WHITESPACE, $beforePtr - 1, null, true);
                    if ($tokens[$beforePtr]['type'] == 'T_OBJECT_OPERATOR') {
                        break;
                    }
                    // No break
                case 'T_FALSE':
                case 'T_TRUE':
                case 'T_NULL':
                    $error = 'Variables should precede constants in comparison operations';
                    $phpcsFile->addError($error, $stackPtr);
                    break;
            }
        }
    }
}
