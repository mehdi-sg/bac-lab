<?php

namespace App\Service;

/**
 * Safe formula parser for T score calculations
 * Parses formulas like "FG+(A+Ang+F)/3" without using eval()
 */
class FormulaParserService
{
    // Subject code mapping for formula parsing
    private const SUBJECT_CODES = [
        'FG' => 'FG',
        'MG' => 'MG', 
        'A' => 'A',
        'PH' => 'PH',
        'HG' => 'HG',
        'F' => 'F',
        'Ang' => 'Ang',
        'M' => 'M',
        'SP' => 'SP',
        'SVT' => 'SVT',
        'Ec' => 'Ec',
        'Ge' => 'Ge',
        'TE' => 'TE',
        'Algo' => 'Algo',
        'STI' => 'STI',
        'SB' => 'SB',
        'SP_Sport' => 'SP_Sport',
        'EP' => 'EP',
        'ALL' => 'ALL' // Special case for some formulas
    ];

    /**
     * Parse and evaluate a formula safely
     */
    public function parseFormula(string $formula, array $variables): ?float
    {
        try {
            // Clean the formula
            $formula = trim($formula);
            
            // Handle empty or invalid formulas
            if (empty($formula) || $formula === 'الاطلاععلىالصيغةالاجمالية(FG)والترتيب') {
                return null;
            }

            // Handle special cases
            if ($formula === 'FG+ALL') {
                return $this->handleSpecialFormula($formula, $variables);
            }

            // Replace variables with their values
            $processedFormula = $this->replaceVariables($formula, $variables);
            
            if ($processedFormula === null) {
                return null; // Missing required variables
            }

            // Parse and calculate the result
            return $this->evaluateExpression($processedFormula);
            
        } catch (\Exception $e) {
            // Log error in production
            error_log("Formula parsing error: " . $e->getMessage() . " for formula: " . $formula);
            return null;
        }
    }

    /**
     * Replace variables in formula with their numeric values
     */
    private function replaceVariables(string $formula, array $variables): ?string
    {
        $processedFormula = $formula;
        $requiredVars = [];

        // Find all variable references
        foreach (self::SUBJECT_CODES as $code => $name) {
            if (preg_match('/\b' . preg_quote($code, '/') . '\b/', $processedFormula)) {
                $requiredVars[] = $code;
            }
        }

        // Replace variables with values
        foreach ($requiredVars as $var) {
            if (!isset($variables[$var])) {
                // Missing required variable
                return null;
            }
            
            $value = (float) $variables[$var];
            
            // Use word boundaries to avoid partial replacements
            $processedFormula = preg_replace('/\b' . preg_quote($var, '/') . '\b/', (string) $value, $processedFormula);
        }

        return $processedFormula;
    }

    /**
     * Safely evaluate a mathematical expression
     */
    private function evaluateExpression(string $expression): float
    {
        // Remove spaces
        $expression = str_replace(' ', '', $expression);
        
        // Validate expression contains only allowed characters
        if (!preg_match('/^[0-9+\-*\/().]+$/', $expression)) {
            throw new \InvalidArgumentException("Invalid characters in expression: " . $expression);
        }

        // Parse and evaluate using recursive descent parser
        return $this->parseExpression($expression);
    }

    /**
     * Recursive descent parser for mathematical expressions
     */
    private function parseExpression(string $expr): float
    {
        $tokens = $this->tokenize($expr);
        $index = 0;
        return $this->parseAddSubtract($tokens, $index);
    }

    /**
     * Tokenize expression into numbers, operators, and parentheses
     */
    private function tokenize(string $expr): array
    {
        $tokens = [];
        $i = 0;
        $len = strlen($expr);

        while ($i < $len) {
            $char = $expr[$i];
            
            if (is_numeric($char) || $char === '.') {
                // Parse number
                $number = '';
                while ($i < $len && (is_numeric($expr[$i]) || $expr[$i] === '.')) {
                    $number .= $expr[$i];
                    $i++;
                }
                $tokens[] = (float) $number;
            } elseif (in_array($char, ['+', '-', '*', '/', '(', ')'])) {
                $tokens[] = $char;
                $i++;
            } else {
                $i++; // Skip unknown characters
            }
        }

        return $tokens;
    }

    /**
     * Parse addition and subtraction (lowest precedence)
     */
    private function parseAddSubtract(array $tokens, int &$index): float
    {
        $left = $this->parseMultiplyDivide($tokens, $index);

        while ($index < count($tokens) && in_array($tokens[$index], ['+', '-'])) {
            $operator = $tokens[$index++];
            $right = $this->parseMultiplyDivide($tokens, $index);
            
            if ($operator === '+') {
                $left += $right;
            } else {
                $left -= $right;
            }
        }

        return $left;
    }

    /**
     * Parse multiplication and division (higher precedence)
     */
    private function parseMultiplyDivide(array $tokens, int &$index): float
    {
        $left = $this->parseFactor($tokens, $index);

        while ($index < count($tokens) && in_array($tokens[$index], ['*', '/'])) {
            $operator = $tokens[$index++];
            $right = $this->parseFactor($tokens, $index);
            
            if ($operator === '*') {
                $left *= $right;
            } else {
                if ($right == 0) {
                    throw new \InvalidArgumentException("Division by zero");
                }
                $left /= $right;
            }
        }

        return $left;
    }

    /**
     * Parse factors (numbers and parentheses)
     */
    private function parseFactor(array $tokens, int &$index): float
    {
        if ($index >= count($tokens)) {
            throw new \InvalidArgumentException("Unexpected end of expression");
        }

        $token = $tokens[$index];

        if (is_numeric($token)) {
            $index++;
            return (float) $token;
        }

        if ($token === '(') {
            $index++; // Skip opening parenthesis
            $result = $this->parseAddSubtract($tokens, $index);
            
            if ($index >= count($tokens) || $tokens[$index] !== ')') {
                throw new \InvalidArgumentException("Missing closing parenthesis");
            }
            
            $index++; // Skip closing parenthesis
            return $result;
        }

        if ($token === '-') {
            $index++; // Skip minus sign
            return -$this->parseFactor($tokens, $index);
        }

        throw new \InvalidArgumentException("Unexpected token: " . $token);
    }

    /**
     * Handle special formula cases
     */
    private function handleSpecialFormula(string $formula, array $variables): ?float
    {
        if ($formula === 'FG+ALL') {
            // This seems to be a special case - return FG if available
            return isset($variables['FG']) ? (float) $variables['FG'] : null;
        }

        return null;
    }

    /**
     * Extract required variables from a formula
     */
    public function getRequiredVariables(string $formula): array
    {
        $required = [];
        
        foreach (self::SUBJECT_CODES as $code => $name) {
            if (preg_match('/\b' . preg_quote($code, '/') . '\b/', $formula)) {
                $required[] = $code;
            }
        }

        return array_unique($required);
    }

    /**
     * Validate if a formula is parseable
     */
    public function isValidFormula(string $formula): bool
    {
        try {
            // Test with dummy variables
            $dummyVars = [];
            foreach (self::SUBJECT_CODES as $code => $name) {
                $dummyVars[$code] = 10.0; // Use 10 as test value
            }
            
            $result = $this->parseFormula($formula, $dummyVars);
            return $result !== null;
            
        } catch (\Exception $e) {
            return false;
        }
    }
}