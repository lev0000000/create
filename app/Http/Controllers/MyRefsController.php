<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Master;
use App\Models\Payment;
use App\Models\Referral;
use App\Services\Referral\ReferralService;
use Illuminate\Http\Request as HttpRequest;

class MyRefsController extends Controller
{
    public function myrefs(HttpRequest $request)
    {
        $userId = $request->attributes->get('current_master')->id;

        $refsClass = new ReferralService;

        $refered = Master::where('id', $userId)->first();

        $refs = $refsClass->registerReferral($refered, $refered->referral_code);

        $refsAmount = $refsClass->rewardAmount(Payment::where('master_id', 3)->value('amount'));
    
        dump(Referral::find(1)->referrerMaster);
    }
}
