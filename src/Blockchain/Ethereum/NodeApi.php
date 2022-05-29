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

class NodeApi implements ProxyApi
{
    protected $server;

    protected $user;

    protected $password;

    protected $network;

    public function __construct(string $server, string $user = null, string $password = null, string $network = 'mainnet')
    {
        $this->server = $server;
        $this->user = $user;
        $this->password = $password;
        $this->network = $network;
    }

    public function send($method, $params = [])
    {
        $url = $this->server;

        $strParams = json_encode(array_values($params));
        $data_string = <<<data
{"jsonrpc":"2.0","method":"{$method}","params": {$strParams},"id":1}
data;

        $data = [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => $data_string,
        ];
        if ($this->user && $this->password) {
            $data['auth'] = [
                $this->user,
                $this->password,
            ];
        }

        $res = Utils::httpRequest('POST', $url, $data);
        if (isset($res['result'])) {
            return $res['result'];
        }
        return false;
    }

    public function gasPrice()
    {
        return $this->send('eth_gasPrice');
    }

    public function ethBalance(string $address, int $decimals = 18)
    {
        $balance = $this->send('eth_getBalance', ['address' => $address, 'latest']);
        return Utils::toDisplayAmount($balance, $decimals);
    }

    public function receiptStatus(string $txHash): bool
    {
        $res = $this->send('eth_getTransactionReceipt', ['txHash' => $txHash]);
        return hexdec($res['status']) ? true : false;
    }

    public function sendRawTransaction($raw)
    {
        return $this->send('eth_sendRawTransaction', ['hex' => $raw]);
    }

    public function getNonce(string $address)
    {
        return $this->send('eth_getTransactionCount', ['address' => $address, 'latest']);
    }

    public function getTransactionReceipt(string $txHash)
    {
        return $this->send('eth_getTransactionReceipt', ['txHash' => $txHash]);
    }

    public function getNetwork(): string
    {
        return $this->network;
    }

    public function ethCall($params): string
    {
        return $this->send('eth_call', ['params' => $params, 'latest']);
    }

    public function blockNumber()
    {
        return hexdec($this->send('eth_blockNumber'));
    }

    public function getBlockByNumber(int $blockNumber)
    {
        $blockNumber = Utils::toHex($blockNumber, true);
        return $this->send('eth_getBlockByNumber', ['blockNumber' => $blockNumber, true]);
    }
}
