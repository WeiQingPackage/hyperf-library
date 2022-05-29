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

interface ProxyApi
{
    public function getNetwork(): string;

    public function send($method, $params = []);

    public function gasPrice();

    public function ethBalance(string $address);

    public function receiptStatus(string $txHash): ?bool;

    public function getTransactionReceipt(string $txHash);

    public function sendRawTransaction($raw);

    public function getNonce(string $address);

    public function ethCall($params);

    public function blockNumber();

    public function getBlockByNumber(int $blockNumber);
}
