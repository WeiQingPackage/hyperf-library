<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace WeiQing\Library\Blockchain\Ethereum;

use Elliptic\EC;
use InvalidArgumentException;
use kornrunner\Keccak;
use RuntimeException;
use Sop\CryptoEncoding\PEM;
use Sop\CryptoTypes\Asymmetric\EC\ECPrivateKey;

class PEMHelper
{
    /**
     * Generate a new Private / Public key pair.
     *
     * @return string
     */
    public static function generateNewPrivateKey()
    {
        $config = [
            'private_key_type' => OPENSSL_KEYTYPE_EC,
            'curve_name' => 'secp256k1',
        ];

        $res = openssl_pkey_new($config);
        if (! $res) {
            throw new RuntimeException(
                'ERROR: Failed to generate private key. -> ' . openssl_error_string()
            );
        }

        // Generate Private Key
        openssl_pkey_export($res, $priv_key);

        // Get The Public Key
        $priv_pem = PEM::fromString($priv_key);

        // Convert to Elliptic Curve Private Key Format
        $ec_priv_key = ECPrivateKey::fromPEM($priv_pem);

        // Then convert it to ASN1 Structure
        $ec_priv_seq = $ec_priv_key->toASN1();

        // Private Key & Public Key in HEX
        return bin2hex($ec_priv_seq->at(1)->asOctetString()->string());
    }

    /**
     * Generate the Address of the provided Public key.
     *
     * @return string
     */
    public static function publicKeyToAddress(string $publicKey)
    {
        if (Utils::isHex($publicKey) === false) {
            throw new InvalidArgumentException('Invalid public key format.');
        }
        $publicKey = Utils::stripZero($publicKey);
        if (strlen($publicKey) !== 130) {
            throw new InvalidArgumentException('Invalid public key length.');
        }
        return '0x' . substr(self::sha3(substr(hex2bin($publicKey), 1)), 24);
    }

    /**
     * Generate the Address of the provided Private key.
     *
     * @return string
     */
    public static function privateKeyToAddress(string $privateKey)
    {
        return self::publicKeyToAddress(
            self::privateKeyToPublicKey($privateKey)
        );
    }

    /**
     * Generate the Public key for provided Private key.
     *
     * @param string $privateKey Private Key
     *
     * @return string
     */
    public static function privateKeyToPublicKey(string $privateKey)
    {
        if (Utils::isHex($privateKey) === false) {
            throw new InvalidArgumentException('Invalid private key format.');
        }
        $privateKey = Utils::stripZero($privateKey);

        if (strlen($privateKey) !== 64) {
            throw new InvalidArgumentException('Invalid private key length.');
        }

        $secp256k1 = new EC('secp256k1');
        $privateKey = $secp256k1->keyFromPrivate($privateKey, 'hex');
        $publicKey = $privateKey->getPublic(false, 'hex');

        return '0x' . $publicKey;
    }

    /**
     * Get sha3
     * keccak256.
     *
     * @return string
     */
    public static function sha3(string $value)
    {
        $hash = Keccak::hash($value, 256);
        // null sha
        $null = 'c5d2460186f7233c927e7db2dcc703c0e500b653ca82273b7bfad8045d85a470';
        if ($hash === $null) {
            return null;
        }
        return $hash;
    }
}
