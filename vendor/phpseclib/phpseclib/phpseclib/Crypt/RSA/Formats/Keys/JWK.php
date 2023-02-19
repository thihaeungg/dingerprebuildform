<?php

/**
 * JSON Web Key (RFC7517) Formatted RSA Handler
 *
 * PHP version 5
 *
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2015 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://phpseclib.sourceforge.net
 */

declare(strict_types=1);

namespace phpseclib3\Crypt\RSA\Formats\Keys;

use phpseclib3\Common\Functions\Strings;
use phpseclib3\Crypt\Common\Formats\Keys\JWK as Progenitor;
use phpseclib3\Math\BigInteger;

/**
 * JWK Formatted RSA Handler
 *
 * @author  Jim Wigginton <terrafrost@php.net>
 */
abstract class JWK extends Progenitor
{
    /**
     * Break a public or private key down into its constituent components
     *
     * @param string|array $key
     */
    public static function load($key, ?string $password = null): array
    {
        $key = parent::loadHelper($key);

        if ($key->kty != 'RSA') {
            throw new \RuntimeException('Only RSA JWK keys are supported');
        }

        $count = $publicCount = 0;
        $vars = ['n', 'e', 'd', 'p', 'q', 'dp', 'dq', 'qi'];
        foreach ($vars as $var) {
            if (!isset($key->$var) || !is_string($key->$var)) {
                continue;
            }
            $count++;
            $value = new BigInteger(Strings::base64url_decode($key->$var), 256);
            switch ($var) {
                case 'n':
                    $publicCount++;
                    $components['modulus'] = $value;
                    break;
                case 'e':
                    $publicCount++;
                    $components['publicExponent'] = $value;
                    break;
                case 'd':
                    $components['privateExponent'] = $value;
                    break;
                case 'p':
                    $components['primes'][1] = $value;
                    break;
                case 'q':
                    $components['primes'][2] = $value;
                    break;
                case 'dp':
                    $components['exponents'][1] = $value;
                    break;
                case 'dq':
                    $components['exponents'][2] = $value;
                    break;
                case 'qi':
                    $components['coefficients'][2] = $value;
            }
        }

        if ($count == count($vars)) {
            return $components + ['isPublicKey' => false];
        }

        if ($count == 2 && $publicCount == 2) {
            return $components + ['isPublicKey' => true];
        }

        throw new \UnexpectedValueException('Key does not have an appropriate number of RSA parameters');
    }

    /**
     * Convert a private key to the appropriate format.
     *
     * @param string $password optional
     * @param array $options optional
     */
    public static function savePrivateKey(BigInteger $n, BigInteger $e, BigInteger $d, array $primes, array $exponents, array $coefficients, ?string $password = null, array $options = []): string
    {
        if (count($primes) != 2) {
            throw new \InvalidArgumentException('JWK does not support multi-prime RSA keys');
        }

        $key = [
            'kty' => 'RSA',
            'n' => Strings::base64url_encode($n->toBytes()),
            'e' => Strings::base64url_encode($e->toBytes()),
            'd' => Strings::base64url_encode($d->toBytes()),
            'p' => Strings::base64url_encode($primes[1]->toBytes()),
            'q' => Strings::base64url_encode($primes[2]->toBytes()),
            'dp' => Strings::base64url_encode($exponents[1]->toBytes()),
            'dq' => Strings::base64url_encode($exponents[2]->toBytes()),
            'qi' => Strings::base64url_encode($coefficients[2]->toBytes()),
        ];

        return self::wrapKey($key, $options);
    }

    /**
     * Convert a public key to the appropriate format
     */
    public static function savePublicKey(BigInteger $n, BigInteger $e, array $options = []): string
    {
        $key = [
            'kty' => 'RSA',
            'n' => Strings::base64url_encode($n->toBytes()),
            'e' => Strings::base64url_encode($e->toBytes()),
        ];

        return self::wrapKey($key, $options);
    }
}
