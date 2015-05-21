<?php
/**
 * Berrybird_Sniffs_Commenting_OneLineCommentSniff.
 *
 * Checks if single-line comment begins with "// ".
 *
 * @author     Kohana Team
 * @copyright  Copyright (C) 2011 Kohana Team
 * @license    BSD-3-Clause
 */
class Berrybird_Sniffs_Commenting_OneLineCommentSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        return array(
            T_COMMENT
        );
    }

    /**
     * {@inheritdoc}
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens  = $phpcsFile->getTokens();
        $content = $tokens[$stackPtr]['content'];

        if (preg_match('/^\s*(?:\/\/[^ ]|#)/', $content)) {
            $error = 'Single-line comments must begin with "// " (e.g. // My comment)';
            $phpcsFile->addError($error, $stackPtr);
        }
    }
}
