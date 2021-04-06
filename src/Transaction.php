<?php
namespace API;

use API\Exceptions\TransactionAPIInsufficientBalance;
use API\Exceptions\TransactionAPINonexistentPayer;
use API\Exceptions\TransactionAPINonexistentReceiver;
use API\Exceptions\UserAPINonexistentId;

class Transaction
{
    private float $value;
    private string $payerId;
    private string $receiverId;
    private User $user;

    public function __construct(User $user, float $value, string $payerId, string $receiverId)
    {
        $this->user = $user;
        $this->value = $value;
        $this->payerId = $payerId;
        $this->receiverId = $receiverId;
    }

    public function execute(): void
    {
        $this->verify();
        $this->user->decreaseUserBalance($this->payerId, $this->value);
        $this->user->increaseUserBalance($this->receiverId, $this->value);
    }

    public function verify(): void
    {
        try {
            $payer = $this->user->getUserById($this->payerId);
        } catch (UserAPINonexistentId $exception) {
            throw new TransactionAPINonexistentPayer();
        }

        try {
            $this->user->getUserById($this->receiverId);
        } catch (UserAPINonexistentId $exception) {
            throw new TransactionAPINonexistentReceiver();
        }

        if ($this->value > $payer["balance"]) {
            throw new TransactionAPIInsufficientBalance();
        }
    }
}