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

use League\Event\EmitterTrait;
use Web3p\EthereumTx\Transaction;

class Eth
{
    use EmitterTrait;

    protected $proxyApi;

    public function __construct(ProxyApi $proxyApi)
    {
        $this->proxyApi = $proxyApi;
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->proxyApi, $name], $arguments);
    }

    // type:[safeLow|standard|fast|fastest]
    public static function gasPriceOracle($type = 'standard')
    {
        $url = 'https://www.etherchain.org/api/gasPriceOracle';
        $res = Utils::httpRequest('GET', $url);
        if ($type && isset($res[$type])) {
            return Utils::toWei((string) $res[$type], 'gwei');
            //            $price = $price * 1e9;
        }
        return $res;
    }

    public static function getChainId($network): int
    {
        $chainId = 1;
        switch ($network) {
            case 'rinkeby':
                $chainId = 4;
                break;
            case 'ropsten':
                $chainId = 3;
                break;
            case 'kovan':
                $chainId = 42;
                break;
            case 'heco-main':
                // https://http-mainnet-node.huobichain.com
                // https://http-mainnet-node.defibox.com
                // https://http-mainnet.hecochain.com

                // https://hecoinfo.com
                $chainId = 128;
                break;
            case 'heco-test':
                // https://http-testnet.hecochain.com
                // https://testnet.hecoinfo.com

                // https://scan-testnet.hecochain.com/faucet
                $chainId = 256;
                break;
            default:
                break;
        }

        return $chainId;
    }

    public function transfer(string $privateKey, string $to, float $value, string $gasPrice = 'standard')
    {
        $from = PEMHelper::privateKeyToAddress($privateKey);
        $nonce = $this->proxyApi->getNonce($from);
        if (! Utils::isHex($gasPrice)) {
            $gasPrice = Utils::toHex(self::gasPriceOracle($gasPrice), true);
        }

        $eth = Utils::toWei("{$value}", 'ether');
        //        $eth = $value * 1e16;
        $eth = Utils::toHex($eth, true);

        $transaction = new Transaction([
            'nonce' => "{$nonce}",
            'from' => $from,
            'to' => $to,
            'gas' => '0x76c0',
            'gasPrice' => "{$gasPrice}",
            'value' => "{$eth}",
            'chainId' => self::getChainId($this->proxyApi->getNetwork()),
        ]);

        $raw = $transaction->sign($privateKey);
        $res = $this->proxyApi->sendRawTransaction('0x' . $raw);
        if ($res !== false) {
            $this->emit(new TransactionEvent($transaction, $privateKey, $res));
        }

        return $res;
    }
}
