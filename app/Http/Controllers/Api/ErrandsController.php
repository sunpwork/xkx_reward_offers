<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ErrandRequest;
use App\Models\Errand;
use App\Transformers\Errand\BaseErrandTransformer;
use function EasyWeChat\Kernel\Support\generate_sign;
use Illuminate\Http\Request;

class ErrandsController extends Controller
{

    public function store(ErrandRequest $request, Errand $errand)
    {
        $errand->fill($request->all());
        $errand->user_id = $this->user()->id;

        $payment = \EasyWeChat::payment();
        $errand->payment_out_trade_no = time() . random_int(0, 9);
        $unifyResult = $payment->order->unify([
            'body' => '校客行跑腿',
            'out_trade_no' => $errand->payment_out_trade_no,
//            'total_fee' => $errand->expense * 100,
            'total_fee' => 1,
            'trade_type' => 'JSAPI',
            'openid' => $this->user()->weapp_openid
        ]);


        if ($unifyResult['return_code'] == 'SUCCESS' && $unifyResult['return_msg'] == 'OK') {
            $errand->save();
            $payParams = $this->getPayParams($unifyResult);
            return $this->response->array(['payParams' => $payParams, 'errandId' => $errand->id])->setStatusCode(201);
        }
    }

    public function pay(Errand $errand)
    {
        if ($errand->status != Errand::STATUS_WAITINGPAY){
            return $this->response->errorForbidden("订单状态错误");
        }
        if (!$this->user()->real_name_auth)
        {
            return $this->response->errorForbidden("用户未完成实名认证");
        }
    }

    public function checkPaymentStatus(Errand $errand)
    {
        $payment = \EasyWeChat::payment();
        $order = $payment->order->queryByOutTradeNumber($errand->payment_out_trade_no);
        if ($order['trade_state'] == 'SUCCESS') {
            $errand->status = Errand::STATUS_PENDING;
            $errand->save();
        }
        return $this->response->noContent();
    }

    public function index(Request $request,Errand $errand)
    {
        $query = $errand->query();
        $query->recent();
        if ($status = $request->status)
        {
            $query->where('status',$status);
        }
        $errands = $query->paginate(20);
        return $this->response->paginator($errands,new BaseErrandTransformer())
            ->addMeta('statusMap',Errand::STATUSES)
            ->addMeta('genderLimitMap',Errand::GENDER_LIMITS);
    }

    private function getPayParams(array $params)
    {
        $payParams = [
            'appId' => $params['appid'],
            'timeStamp' => time(),
            'nonceStr' => $params['nonce_str'],
            'package' => "prepay_id={$params['prepay_id']}",
            'signType' => 'MD5',
        ];
        $payParams['paySign'] = generate_sign($payParams, config('wechat.payment.default.key'));
        return $payParams;
    }

}
