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

class EtherscanApi implements ProxyApi
{
    protected $apiKey;

    protected $network;

    public function __construct(string $apiKey, $network = 'mainnet')
    {
        $this->apiKey = $apiKey;
        $this->network = $network;
    }

    public function send($method, $params = [])
    {
        $defaultParams = [
            'module' => 'proxy',
            'tag' => 'latest',
        ];

        foreach ($defaultParams as $key => $val) {
            if (! isset($params[$key])) {
                $params[$key] = $val;
            }
        }

        $url = $this->getUrl($method);
        if ($params && count($params) > 0) {
            $strParams = http_build_query($params);
            $url .= "&{$strParams}";
        }

        $res = Utils::httpRequest('GET', $url);
        if (isset($res['result'])) {
            return $res['result'];
        }
        var_dump($res);
        return false;
    }

    public function gasPrice()
    {
        return $this->send('eth_gasPrice');
    }

    public function ethBalance(string $address)
    {
        $params['module'] = 'account';
        $params['address'] = $address;

        $retDiv = Utils::fromWei($this->send('balance', $params), 'ether');
        if (is_array($retDiv)) {
            return Utils::divideDisplay($retDiv, 16);
        }
        return $retDiv;
    }

    public function receiptStatus(string $txHash): ?bool
    {
        $res = $this->send('eth_getTransactionByHash', ['txhash' => $txHash]);
        if (! $res) {
            return false;
        }

        if (! $res['blockNumber']) {
            return null;
        }

        $params['module'] = 'transaction';
        $params['txhash'] = $txHash;

        $res = $this->send('gettxreceiptstatus', $params);
        return $res['status'] == '1';
    }

    public function getTransactionReceipt(string $txHash)
    {
        return $this->send('eth_getTransactionReceipt', ['txhash' => $txHash]);
    }

    public function sendRawTransaction($raw)
    {
        return $this->send('eth_sendRawTransaction', ['hex' => $raw]);
    }

    public function getNonce(string $address)
    {
        return $this->send('eth_getTransactionCount', ['address' => $address]);
    }

    public function getNetwork(): string
    {
        return $this->network;
    }

    public function ethCall($params): string
    {
        // TODO: Implement ethCall() method.
    }

    public function blockNumber()
    {
        // TODO: Implement blockNumber() method.
    }

    public function getBlockByNumber(int $blockNumber)
    {
        // TODO: Implement getBlockByNumber() method.
    }

    protected function getUrl($method)
    {
        $preApi = 'api';
        if ($this->network != 'mainnet') {
            $preApi .= '-' . $this->network;
        }
        return "https://{$preApi}.etherscan.io/api?action={$method}&apikey={$this->apiKey}";
    }
}
