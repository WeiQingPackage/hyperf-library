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

use BIP\BIP44;
use FurqanSiddiqui\BIP39\BIP39;
use FurqanSiddiqui\BIP39\Exception\MnemonicException;
use FurqanSiddiqui\BIP39\Exception\WordListException;

class Wallet
{
    public const DEFAULT_PATH = "m/44'/60'/0'/0/0";

    /**
     * 生成秘钥创建账户.
     */
    public static function newAccountByPrivateKey(): array
    {
        $privateKey = PEMHelper::generateNewPrivateKey();
        $address = PEMHelper::privateKeyToAddress($privateKey);

        return [
            'key' => $privateKey,
            'address' => $address,
        ];
    }

    /**
     * 生成助记词创建账户.
     * @param string $passphrase 密码
     * @param string $path BIP44路径
     * @throws MnemonicException
     * @throws WordListException
     */
    public static function newAccountByMnemonic(string $passphrase = '', string $path = self::DEFAULT_PATH): array
    {
        $mnemonic = BIP39::Generate(12);
        $seed = $mnemonic->generateSeed($passphrase);
        $HDKey = BIP44::fromMasterSeed($seed)->derive($path);

        $privateKey = $HDKey->privateKey;
        $address = PEMHelper::privateKeyToAddress($privateKey);
        return [
            'mnemonic' => implode(' ', $mnemonic->words),
            'key' => $privateKey,
            'address' => $address,
        ];
    }

    /**
     * 使用助记词还原账户.
     * @param string $mnemonic 助记词
     * @param string $passphrase 密码
     * @param string $path BIP44路径
     * @throws MnemonicException
     * @throws WordListException
     */
    public static function revertAccountByMnemonic(string $mnemonic, string $passphrase = '', string $path = self::DEFAULT_PATH): array
    {
        $seed = BIP39::Words($mnemonic)->generateSeed($passphrase);
        $HDKey = BIP44::fromMasterSeed($seed)->derive($path);

        $privateKey = $HDKey->privateKey;
        $address = PEMHelper::privateKeyToAddress($privateKey);
        return [
            'key' => $privateKey,
            'address' => $address,
        ];
    }
}
