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

use League\Event\AbstractEvent;
use Web3p\EthereumTx\Transaction;

class TransactionEvent extends AbstractEvent
{
    public function __construct(Transaction $transaction, string $privateKey, string $txHash)
    {
        $this->transaction = $transaction;
        $this->privateKey = $privateKey;
        $this->txHash = $txHash;
    }
}
