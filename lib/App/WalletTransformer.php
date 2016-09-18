<?php
namespace App;

class WalletTransformer extends RootTransformer
{

    public function transform(Wallet $wallet)
    {
        return array_merge([
            "name" => (string)$wallet->name ?: "",
            "currency_id" => $wallet->currency_id
        ], parent::transform($wallet));
    }
}