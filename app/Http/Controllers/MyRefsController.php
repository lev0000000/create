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

        $referedId = Master::find($userId)
            ->referrals
            ->keyBy('id')
            ->toArray(); // Рефералы 

        $referedPayMent = Payment::all()->keyBy('master_id')->toArray();


        $referedData = Master::find(array_keys($referedId))->toArray();



        $dataJson[] = array_map(function ($item) use ($referedId, $refsClass, $referedPayMent) {
            return [
                'name' => $item['name'],
                'created_at' => $item['created_at'],
                'status' => $referedId[$item['id']]['status'],
                'amount' => array_key_exists($item['id'],$referedPayMent) ? $refsClass->rewardAmount($referedPayMent[$item['id']]['amount']) : 0
                
            ];
        }, $referedData);

        return response($dataJson);
    }
}
