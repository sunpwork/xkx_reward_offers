<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ErrandRequest;
use App\Models\Errand;
use function EasyWeChat\Kernel\Support\generate_sign;
use Illuminate\Http\Request;

class ErrandsController extends Controller
{

    public function store(ErrandRequest $request,Errand $errand)
    {
        $errand->fill($request->all());
        $errand->user_id = $this->user()->id;

        $payment = \EasyWeChat::payment();
        $outTradeNo = time() . random_int(0,9);
        $unifyResult = $payment->order->unify([
            'body' => '校客行跑腿',
            'out_trade_no' => $outTradeNo,
//            'total_fee' => $errand->expense * 100,
            'total_fee' => 1,
            'trade_type' => 'JSAPI',
            'openid' => $this->user()->weapp_openid
        ]);


        if ($unifyResult['return_code'] == 'SUCCESS' && $unifyResult['return_msg'] == 'OK'){
            $errand->save();
            $payParams = $this->getPayParams($unifyResult);
            return $this->response->array($payParams)->setStatusCode(201);
        }
    }

    public function checkPaymentStatus(Errand $errand){
        $payment = \EasyWeChat::payment();
        $order = $payment->order->queryByOutTradeNumber($errand->payment_out_trade_no);
        if ($order['trade_state'] == 'SUCCESS'){
            $errand->status = Errand::STATUS_PENDING;
            $errand->save();
        }
        return $this->response->noContent();
    }

    private function getPayParams(array $params){
        $payParams = [
            'appId' => $params['appid'],
            'timeStamp' => time(),
            'nonceStr' => $params['nonce_str'],
            'package' => "prepay_id={$params['prepay_id']}",
            'signType' => 'MD5',
        ];
        $payParams['paySign'] = generate_sign($payParams,config('wechat.payment.default.key'));
        return $payParams;
    }

}
