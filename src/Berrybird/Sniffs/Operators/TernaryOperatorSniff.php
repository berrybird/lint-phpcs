<?php
/**
 * Ternary operator sniff.
 *
 * Logs an error when a ternary operation is not in parentheses.
 * Operands must also be in parentheses unless it is a single variable,
 * array access, object access or function call.
 *
 * @author     Kohana Team
 * @copyright  Copyright (C) 2011 Kohana Team
 * @license    BSD-3-Clause
 */
class Berrybird_Sniffs_Operators_TernaryOperatorSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        return array(
            T_INLINE_THEN
        );
    }

    /**
     * {@inheritdoc}
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (empty($tokens[$stackPtr]['nested_parenthesis'])) {
            $allowed = array(
                T_EQUAL,
                T_RETURN,
                T_ECHO,
                T_AND_EQUAL,
                T_CONCAT_EQUAL,
                T_DIV_EQUAL,
                T_MINUS_EQUAL,
                T_MOD_EQUAL,
                T_MUL_EQUAL,
                T_OR_EQUAL,
                T_PLUS_EQUAL,
                T_SL_EQUAL,
                T_SR_EQUAL,
                T_XOR_EQUAL,

                // To detect end of previous statement
                T_SEMICOLON,
            );

            $startPtr = $this->findPrevious($allowed, $tokens, $stackPtr - 1, 0, null);
            $endPtr   = $phpcsFile->findNext(array(T_CLOSE_TAG, T_SEMICOLON), $stackPtr + 1);

            if ($tokens[$startPtr]['code'] === T_SEMICOLON) {
                $error = 'Ternary operation must occur within assignment, echo or return statement';
                $phpcsFile->addError($error, $stackPtr, 'TernaryStart');
            }

            $colonPtr = $this->findNext(T_INLINE_ELSE, $tokens, $stackPtr + 1, $endPtr, null);
        } else {
            // Inside an array or function call
            $endPtr   = end($tokens[$stackPtr]['nested_parenthesis']);
            $startPtr = key($tokens[$stackPtr]['nested_parenthesis']);

            if ($comma = $this->findPrevious(array(T_COMMA, T_DOUBLE_ARROW), $tokens, $stackPtr - 1, $startPtr, $startPtr)) {
                $startPtr = $comma;
            }

            $colonPtr = $this->findNext(T_INLINE_ELSE, $tokens, $stackPtr + 1, $endPtr, $endPtr);

            if ($comma = $this->findNext(T_COMMA, $tokens, $colonPtr + 1, $endPtr, $endPtr)) {
                $endPtr = $comma;
            }
        }

        if ($tokens[$startPtr]['line'] < $tokens[$stackPtr]['line'] - 1) {
            $error = 'Ternary operator must appear on the same or following line of its condition';
            $phpcsFile->addError($error, $stackPtr, 'TernaryLine');
        }

        if ($tokens[$colonPtr]['line'] > $tokens[$stackPtr]['line'] + 1) {
            $error = 'Colon must appear on the same or following line of its ternary operator';
            $phpcsFile->addError($error, $stackPtr, 'TernaryColonLine');
        }

        $this->evaluatePortion($phpcsFile, 'condition', $startPtr + 1, $stackPtr - 1);
        $this->evaluatePortion($phpcsFile, 'true value', $stackPtr + 1, $colonPtr - 1);
        $this->evaluatePortion($phpcsFile, 'false value', $colonPtr + 1, $endPtr - 1);
    }

    /**
     * Verifies one operand of a ternary operation follows Berrybird coding standards
     *
     * @param   PHP_CodeSniffer_Sniff   $file
     * @param   string                  $name   Portion being evaluated. Used in error messages.
     * @param   integer                 $start  Index of the first token in the portion
     * @param   integer                 $end    Index of the last token in the portion
     * @return  void
     */
    protected function evaluatePortion(PHP_CodeSniffer_File $file, $name, $start, $end)
    {
        // Skip any whitespace or casts
        $current = $file->findNext(array(
            T_WHITESPACE,
            T_ARRAY_CAST,
            T_BOOL_CAST,
            T_DOUBLE_CAST,
            T_INT_CAST,
            T_OBJECT_CAST,
            T_STRING_CAST,
            T_UNSET_CAST,
        ), $start, $end, true);

        // Trim any trailing whitespace
        $end = $file->findPrevious(T_WHITESPACE, $end, $current, true);

        $tokens = $file->getTokens();
        $is_static_call = false;

        if ($tokens[$current]['code'] === T_OPEN_PARENTHESIS) {
            // Skip any variables or whitespace
            $next = $file->findNext(array(T_VARIABLE, T_WHITESPACE), $current + 1, null, true);

            if ($tokens[$next]['code'] === T_CLOSE_PARENTHESIS) {
                $error = 'A single variable should not have parenthesis in the '.$name.' portion of ternary operations';
                $file->addError($error, $current, 'TernaryParenthesizedVariable');
            }
        } else {
            $next = $file->findNext(T_WHITESPACE, $current + 1, $end, true);

            if ($tokens[$current]['code'] === T_STRING && $tokens[$next]['code'] === T_DOUBLE_COLON) {
                // Static access
                $current = $this->findNextInvocationOrAccess($current, $next, $tokens, $end, $name, $file);
            }

            if ($tokens[$current]['code'] === T_VARIABLE) {
                $current = $this->findNextInvocationOrAccess($current, $next, $tokens, $end, $name, $file);
            } else {
                if ($tokens[$current]['code'] === T_NEW) {
                    // Skip to class
                    $current = $file->findnext(T_WHITESPACE, $current + 1, $end, true);
                }

                if ($tokens[$current]['code'] === T_STRING
                    || $tokens[$current]['code'] === T_ARRAY
                    || $tokens[$current]['code'] === T_EMPTY
                    || $tokens[$current]['code'] === T_ISSET) {
                    // Constant, function call or array

                    // Skip to parenthesis
                    $current = $file->findNext(T_WHITESPACE, $current + 1, $end, true);

                    if ($tokens[$current]['code'] === T_OPEN_PARENTHESIS) {
                        $current = $tokens[$current]['parenthesis_closer'];
                    }
                } elseif ($tokens[$current]['code'] === T_MINUS) {
                    // Negation

                    // Skip to negated value
                    $current = $file->findNext(T_WHITESPACE, $current + 1, $end, true);
                }

                if ($current && $current < $end) {
                    // The current position is NOT the end. Some other comparison, operation, etc must be happening.
                    $error = 'Comparisons and operations must be in parentheses in the '.$name.' portion of ternary operations';
                    $file->addError($error, $current, 'TernaryParenthesized');
                }
            }
        }
    }

    /**
     * Find the next invokation or access of an object's or static class' members.
     *
     * @param   integer               The current position in the token stack
     * @param   integer               The next token position
     * @param   array                 Array of file's tokens
     * @param   integer               Pointer to token at end of section
     * @param   string                Section name (i.e. condition or value)
     * @param   PHP_CodeSniffer_File  The file we're examining
     * @return  integer               The position of the last token in the call (i.e. last non-whitespace token)
     */
    protected function findNextInvocationOrAccess($current, $next, $tokens, $end, $name, PHP_CodeSniffer_File $file)
    {
        while ($next = $file->findNext(T_WHITESPACE, $current + 1, $end, true)) {
            if ($tokens[$next]['code'] === T_OPEN_SQUARE_BRACKET
                || $tokens[$next]['code'] === T_OPEN_CURLY_BRACKET) {
                // Array or String access
                $current = $tokens[$next]['bracket_closer'];
            } elseif ($tokens[$next]['code'] === T_OBJECT_OPERATOR
                || $tokens[$next]['code'] === T_STRING
                || $tokens[$next]['code'] === T_VARIABLE) {
                // Object access
                $current = $next + 1;
            } elseif ($tokens[$next]['code'] === T_OPEN_PARENTHESIS) {
                // Call
                $current = $tokens[$next]['parenthesis_closer'];
            } elseif ($tokens[$next]['code'] === T_DOUBLE_COLON) {
                $current = $next + 1;
            } else {
                $error = 'Comparisons and operations must be in parentheses in the '.$name.' portion of ternary operations';
                $file->addError($error, $current, 'TernaryParenthesized');
                break;
            }

        }

        return $current;
    }

    /**
     * Find the index of the next token of a certain type in a particular parentheses group
     *
     * @param   array|integer   $type           Token type(s) to find
     * @param   array           $tokens         Tokens to search
     * @param   integer         $start          Index from which to begin
     * @param   integer         $end            Index at which to abort
     * @param   integer         $parenthesis    Index of the closing parenthesis or NULL
     * @return  integer|false   Index of the next token or FALSE
     */
    protected function findNext($type, $tokens, $start, $end, $parenthesis)
    {
        if ( ! is_array($type)) {
            $type = array($type);
        }

        if ($parenthesis === null) {
            for ($i = $start; $i < $end; ++$i) {
                if (in_array($tokens[$i]['code'], $type) && empty($tokens[$i]['nested_parenthesis'])) {
                    return $i;
                }
            }
        } else {
            for ($i = $start; $i < $end; ++$i) {
                if (in_array($tokens[$i]['code'], $type) && ! empty($tokens[$i]['nested_parenthesis']) && $parenthesis === end($tokens[$i]['nested_parenthesis'])) {
                    return $i;
                }
            }
        }

        return false;
    }

    /**
     * Find the index of the previous token of a certain type in a particular parentheses group
     *
     * @param   array|integer   $type           Token type(s) to find
     * @param   array           $tokens         Tokens to search
     * @param   integer         $start          Index from which to begin
     * @param   integer         $end            Index at which to abort
     * @param   integer         $parenthesis    Index of the opening parenthesis or NULL
     * @return  integer|false   Index of the next token or FALSE
     */
    protected function findPrevious($type, $tokens, $start, $end, $parenthesis)
    {
        if ( ! is_array($type)) {
            $type = array($type);
        }

        if ($parenthesis === null) {
            for ($i = $start; $i >= $end; --$i) {
                if (in_array($tokens[$i]['code'], $type) && empty($tokens[$i]['nested_parenthesis'])) {
                    return $i;
                }
            }
        } else {
            for ($i = $start; $i >= $end; --$i) {
                if (in_array($tokens[$i]['code'], $type) and ! empty($tokens[$i]['nested_parenthesis'])) {
                    end($tokens[$i]['nested_parenthesis']);

                    if ($parenthesis === key($tokens[$i]['nested_parenthesis'])) {
                        return $i;
                    }
                }
            }
        }
    }
}
