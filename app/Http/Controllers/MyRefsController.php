<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Master;
use App\Models\Payment;
use App\Models\Referral;
use App\Services\Referral\ReferralService;
use Error;
use Illuminate\Http\Request as HttpRequest;

class MyRefsController extends Controller
{
    /**
     * Id Master
     * @var int
     */
    public int $userId;

    /**
     * RefCode из Body
     * @var string
     */
    public string $refCode;

    /**
     * Все реферальные пользователи которые относятся к Мастеру
     * @var array
     */
    public array $refsUsers;


    /**
     * Все транзакции по рефералам 
     * @var array
     */
    public array $refsAllPayment;

    /**
     * Все данные по реферелам
     * @var array
     */
    public array $referedData;

    public function __construct(
        public HttpRequest $request,
        public ReferralService $refsClass
    ) {
        $this->userId = $this->request
            ->attributes
            ->get('current_master')
            ->id; 

        $this->refCode = $this->request->get('code') ?? ''; // реф код по current id

        $this->refsUsers = Master::find($this->userId)
            ->referrals
            ->keyBy('id')
            ->toArray(); // Все рефы по одному мастеру 

        $this->refsAllPayment = Payment::all()->keyBy('master_id')->toArray(); // все транзакции по рефералам 

        $this->referedData = Master::find(array_keys($this->refsUsers))->toArray();

    }
    public function myrefs()
    {

        $dataJson[] = array_map(function ($item) {
            return [
                'name' => $item['name'],
                'created_at' => $item['created_at'],
                'status' => $this->refsUsers[$item['id']]['status'],
                'amount' => array_key_exists($item['id'], $this->refsAllPayment) ? $this->refsClass->rewardAmount($this->refsAllPayment[$item['id']]['amount']) : 0

            ];
        }, $this->referedData);

        return response($dataJson);
    }

    public function create()
    {
        try{
            $this->refsClass->registerReferral(Master::where('id',$this->userId)->first(), $this->refCode);
            return response("Пользователь добавлен по реферальной программе");
        }catch(Error $e){
            return response('Не удалось добавить пользователя по реферальной программе' . $e->getMessage(), 403 );
        }

    }

    public function earnings() {

        $allPayRefs = $this->myrefs()->original;

        $data = [
            'PaidOut' => array_sum(array_column($allPayRefs[0],'amount')),
            'Expectation' => count(array_filter($allPayRefs[0],fn($item)=> $item['amount'] == 0)),
            'Completed' => count((array_filter($allPayRefs[0],fn($item)=> $item['amount'] > 0)))
        ];

        return response($data);
    }
}
