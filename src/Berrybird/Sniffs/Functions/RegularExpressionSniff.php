<?php

namespace Berrybird\Sniffs\Functions;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Sniff;

/**
 * Regular expression sniff.
 *
 * Performs the following regular expression checks:
 *
 *  - PCRE (preg) functions are used over POSIX (ereg) functions
 *  - If regular expression have an EOL hole
 *  - Regular expression is surrounded by single quotes
 *  - Regular expression replacement is surrounded by single quotes
 *  - Backreference in regular expression is using $n rather than \\n  notation
 *
 * @author     Kohana Team
 * @copyright  Copyright (C) 2011 Kohana Team
 * @license    BSD-3-Clause
 */
class RegularExpressionSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        return array(
            T_STRING
        );
    }

    /**
     * {@inheritdoc}
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Is this a function call?
        $prevPtr = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);
        $nextPtr = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);

        if ($tokens[$prevPtr]['type'] != 'T_FUNCTION'
            && $tokens[$nextPtr]['type'] == 'T_OPEN_PARENTHESIS') {

            // Is this a POSIX function?
            if (preg_match('/^(ereg|spliti?|sql_regcase)$/', $tokens[$stackPtr]['content'])) {
                $error = 'PCRE (preg) functions are preferred over POSIX (ereg) functions';
                $phpcsFile->addError($error, $stackPtr);

            // Is this a PCRE function?
            } elseif (strpos($tokens[$stackPtr]['content'], 'preg') === 0) {

                // Is the regular expression surrounded by single quotes?
                $nextPtr = $phpcsFile->findNext(T_CONSTANT_ENCAPSED_STRING, $nextPtr + 1);
                $content = $tokens[$nextPtr]['content'];
                if (substr($content, 0, 1) == '"') {
                    $error = 'Regular expressions must be surrounded by single quotes';
                    $phpcsFile->addError($error, $stackPtr);
                }

                // Does the regular expression have a EOL hole?
                if (preg_match('/\$\/[^D]*$/', $content)) {
                    $error = 'Regular expression may have an EOL hole and need /D modifier';
                    $phpcsFile->addWarning($error, $stackPtr);
                }

                // Is this function preg_replace?
                if ($tokens[$stackPtr]['content'] != 'preg_replace') {
                    return;
                }

                // Is the replacement surrounded by single quotes?
                // In some cases this functionality is required, hence this is a warning
                $nextPtr = $phpcsFile->findNext(T_CONSTANT_ENCAPSED_STRING, $nextPtr + 1);
                $content = $tokens[$nextPtr]['content'];

                if (substr($content, 0, 1) == '"') {
                    $error = 'It is recommended that regular expression replacements are surrounded by single quotes';
                    $phpcsFile->addWarning($error, $stackPtr);
                }

                // Is the replacement using the $n notation for backreferences?
                if (preg_match('/\\\\[0-9]+/', $content)) {
                    $error = 'Backreferences in regular expressions must use $n notation rather than \\n notation';
                    $phpcsFile->addError($error, $stackPtr);
                }
            }
        }
    }
}
