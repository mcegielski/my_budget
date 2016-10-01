<?php
namespace App;

class TransactionTransformer extends RootTransformer
{

    public function transform(Transaction $transaction)
    {
        return array_merge([
            "name" => (string)$transaction->name ?: "",
            "wallet_from_id" => $transaction->wallet_from_id,
            "value" => $transaction->value,
            "date" => $transaction->date->format('Y-m-d'),
            "wallet_to_id" => $transaction->wallet_to_id,
            "value_to" => $transaction->value_to
        ], parent::transform($transaction));
    }
}