<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Master;
use App\Models\Referral;
use Illuminate\Http\Request as HttpRequest;

class PostController extends Controller
{
    public function create(HttpRequest $request)
    {
        $user = $request->attributes->get('current_master')->id;
        $refCode = $request->get('code');

        $idMaster = Master::where('referral_code', $refCode)->value('id');

        $isExist = Referral::where('referrer_master_id', $idMaster)->where('referred_master_id', $user)->exists();


        if($idMaster===$user || $isExist){
            return response('Вы не можете использовать свой реферальный код', 403);
        }

        Referral::create([
            'referrer_master_id' => $idMaster,
            'referred_master_id' => $user
        ]);

        Referral::where("id",8)->delete();

    }

}
