<?php

/**
 * Pure-PHP 32-bit BigInteger Engine
 *
 * PHP version 5 and 7
 *
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2017 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://pear.php.net/package/Math_BigInteger
 */

declare(strict_types=1);

namespace phpseclib3\Math\BigInteger\Engines;

/**
 * Pure-PHP 32-bit Engine.
 *
 * Uses 64-bit floats if int size is 4 bits
 *
 * @author  Jim Wigginton <terrafrost@php.net>
 */
class PHP32 extends PHP
{
    // Constants used by PHP.php
    public const BASE = 26;
    public const BASE_FULL = 0x4000000;
    public const MAX_DIGIT = 0x3FFFFFF;
    public const MSB = 0x2000000;

    /**
     * MAX10 in greatest MAX10LEN satisfying
     * MAX10 = 10**MAX10LEN <= 2**BASE.
     */
    public const MAX10 = 10000000;

    /**
     * MAX10LEN in greatest MAX10LEN satisfying
     * MAX10 = 10**MAX10LEN <= 2**BASE.
     */
    public const MAX10LEN = 7;
    public const MAX_DIGIT2 = 4503599627370496;

    /**
     * Initialize a PHP32 BigInteger Engine instance
     *
     * @see parent::initialize()
     */
    protected function initialize(int $base): void
    {
        if ($base != 256 && $base != -256) {
            parent::initialize($base);
            return;
        }

        $val = $this->value;
        $this->value = [];
        $vals = &$this->value;
        $i = strlen($val);
        if (!$i) {
            return;
        }

        while (true) {
            $i -= 4;
            if ($i < 0) {
                if ($i == -4) {
                    break;
                }
                $val = substr($val, 0, 4 + $i);
                $val = str_pad($val, 4, "\0", STR_PAD_LEFT);
                if ($val == "\0\0\0\0") {
                    break;
                }
                $i = 0;
            }
            [, $digit] = unpack('N', substr($val, $i, 4));
            if ($digit < 0) {
                $digit += 0xFFFFFFFF + 1;
            }
            $step = count($vals) & 3;
            if ($step) {
                $digit = floor($digit / 2 ** (2 * $step));
            }
            if ($step != 3) {
                $digit &= static::MAX_DIGIT;
                $i++;
            }
            $vals[] = $digit;
        }
        while (end($vals) === 0) {
            array_pop($vals);
        }
        reset($vals);
    }

    /**
     * Test for engine validity
     *
     * @see parent::__construct()
     */
    public static function isValidEngine(): bool
    {
        return PHP_INT_SIZE >= 4;
    }

    /**
     * Adds two BigIntegers.
     */
    public function add(PHP32 $y): PHP32
    {
        $temp = self::addHelper($this->value, $this->is_negative, $y->value, $y->is_negative);

        return $this->convertToObj($temp);
    }

    /**
     * Subtracts two BigIntegers.
     */
    public function subtract(PHP32 $y): PHP32
    {
        $temp = self::subtractHelper($this->value, $this->is_negative, $y->value, $y->is_negative);

        return $this->convertToObj($temp);
    }

    /**
     * Multiplies two BigIntegers.
     */
    public function multiply(PHP32 $y): PHP32
    {
        $temp = self::multiplyHelper($this->value, $this->is_negative, $y->value, $y->is_negative);

        return $this->convertToObj($temp);
    }

    /**
     * Divides two BigIntegers.
     *
     * Returns an array whose first element contains the quotient and whose second element contains the
     * "common residue".  If the remainder would be positive, the "common residue" and the remainder are the
     * same.  If the remainder would be negative, the "common residue" is equal to the sum of the remainder
     * and the divisor (basically, the "common residue" is the first positive modulo).
     *
     * @return array{PHP32, PHP32}
     */
    public function divide(PHP32 $y): array
    {
        return $this->divideHelper($y);
    }

    /**
     * Calculates modular inverses.
     *
     * Say you have (30 mod 17 * x mod 17) mod 17 == 1.  x can be found using modular inverses.
     * @return false|PHP32
     */
    public function modInverse(PHP32 $n)
    {
        return $this->modInverseHelper($n);
    }

    /**
     * Calculates modular inverses.
     *
     * Say you have (30 mod 17 * x mod 17) mod 17 == 1.  x can be found using modular inverses.
     * @return PHP32[]
     */
    public function extendedGCD(PHP32 $n): array
    {
        return $this->extendedGCDHelper($n);
    }

    /**
     * Calculates the greatest common divisor
     *
     * Say you have 693 and 609.  The GCD is 21.
     */
    public function gcd(PHP32 $n): PHP32
    {
        return $this->extendedGCD($n)['gcd'];
    }

    /**
     * Logical And
     */
    public function bitwise_and(PHP32 $x): PHP32
    {
        return $this->bitwiseAndHelper($x);
    }

    /**
     * Logical Or
     */
    public function bitwise_or(PHP32 $x): PHP32
    {
        return $this->bitwiseOrHelper($x);
    }

    /**
     * Logical Exclusive Or
     */
    public function bitwise_xor(PHP32 $x): PHP32
    {
        return $this->bitwiseXorHelper($x);
    }

    /**
     * Compares two numbers.
     *
     * Although one might think !$x->compare($y) means $x != $y, it, in fact, means the opposite.  The reason for this is
     * demonstrated thusly:
     *
     * $x  > $y: $x->compare($y)  > 0
     * $x  < $y: $x->compare($y)  < 0
     * $x == $y: $x->compare($y) == 0
     *
     * Note how the same comparison operator is used.  If you want to test for equality, use $x->equals($y).
     *
     * {@internal Could return $this->subtract($x), but that's not as fast as what we do do.}
     *
     * @return int in case < 0 if $this is less than $y; > 0 if $this is greater than $y, and 0 if they are equal.
     * @see self::equals()
     */
    public function compare(PHP32 $y): int
    {
        return $this->compareHelper($this->value, $this->is_negative, $y->value, $y->is_negative);
    }

    /**
     * Tests the equality of two numbers.
     *
     * If you need to see if one number is greater than or less than another number, use BigInteger::compare()
     */
    public function equals(PHP32 $x): bool
    {
        return $this->value === $x->value && $this->is_negative == $x->is_negative;
    }

    /**
     * Performs modular exponentiation.
     */
    public function modPow(PHP32 $e, PHP32 $n): PHP32
    {
        return $this->powModOuter($e, $n);
    }

    /**
     * Performs modular exponentiation.
     *
     * Alias for modPow().
     */
    public function powMod(PHP32 $e, PHP32 $n): PHP32
    {
        return $this->powModOuter($e, $n);
    }

    /**
     * Generate a random prime number between a range
     *
     * If there's not a prime within the given range, false will be returned.
     *
     * @return false|PHP32
     */
    public static function randomRangePrime(PHP32 $min, PHP32 $max)
    {
        return self::randomRangePrimeOuter($min, $max);
    }

    /**
     * Generate a random number between a range
     *
     * Returns a random number between $min and $max where $min and $max
     * can be defined using one of the two methods:
     *
     * BigInteger::randomRange($min, $max)
     * BigInteger::randomRange($max, $min)
     */
    public static function randomRange(PHP32 $min, PHP32 $max): PHP32
    {
        return self::randomRangeHelper($min, $max);
    }

    /**
     * Performs exponentiation.
     */
    public function pow(PHP32 $n): PHP32
    {
        return $this->powHelper($n);
    }

    /**
     * Return the minimum BigInteger between an arbitrary number of BigIntegers.
     */
    public static function min(PHP32 ...$nums): PHP32
    {
        return self::minHelper($nums);
    }

    /**
     * Return the maximum BigInteger between an arbitrary number of BigIntegers.
     */
    public static function max(PHP32 ...$nums): PHP32
    {
        return self::maxHelper($nums);
    }

    /**
     * Tests BigInteger to see if it is between two integers, inclusive
     */
    public function between(PHP32 $min, PHP32 $max): bool
    {
        return $this->compare($min) >= 0 && $this->compare($max) <= 0;
    }
}
