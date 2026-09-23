<?php

namespace App\Services\Referral;

use App\Models\Master;
use App\Models\Referral;
use Error;
use ErrorException;
use Exception;

class ReferralService
{
    public function registerReferral(Master $referred, string $code): ?Referral
    {
        try {
            $referrer = Master::where('referral_code', $code)->first();

            if (empty($referrer) || $referrer->id === $referred->id) {
                throw new Error('Нельзя пригласить самого себя');
            }

            return Referral::firstOrCreate(
                [
                    'referred_master_id' => $referred->id,
                ],
                [
                    'referrer_master_id' => $referrer->id,
                    'program' => Referral::PROGRAM_MASTER_INVITE,
                    'status' => Referral::STATUS_PENDING,
                ]
            );
        } catch (Error $e) {
            throw $e;
        }
    }

    public function rewardAmount(int $paymentAmount): int
    {
        $percent = (int) config('referral.percent');

        return (int) round($paymentAmount * $percent);
    }
}
