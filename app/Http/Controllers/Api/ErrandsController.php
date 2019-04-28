<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ErrandRequest;
use App\Models\Errand;
use App\Models\User;
use App\Transformers\Errand\BaseErrandTransformer;
use App\Transformers\Errand\FullErrandTransformer;
use function EasyWeChat\Kernel\Support\generate_sign;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Null_;

class ErrandsController extends Controller
{

    public function store(ErrandRequest $request, Errand $errand)
    {
        $errand->fill($request->all());
        $errand->user_id = $this->user()->id;

        $payParams = $this->getPayParams($errand, $this->user());

        if ($payParams) {
            return $this->response->array(['payParams' => $payParams, 'errandId' => $errand->id])->setStatusCode(201);
        } else {
            return $this->response->errorInternal("支付接口异常，请稍后重试");
        }
    }

    public function show(Errand $errand)
    {
        if ($this->user()->id == $errand->user->id || $this->user()->id == $errand->operator->id) {
            $response = $this->response->item($errand, new FullErrandTransformer());
        } else {
            $response = $this->response->item($errand, new BaseErrandTransformer());

        }
        return $response->addMeta('genderLimitMap', Errand::GENDER_LIMITS)
            ->addMeta('statusMap', Errand::STATUSES)
            ->addMeta('genderMap', User::GENDERS);
    }

    public function pay(Errand $errand)
    {
        if ($errand->status != Errand::STATUS_WAITINGPAY) {
            return $this->response->errorForbidden("订单状态错误");
        }
        if ($this->user()->id != $errand->user->id) {
            return $this->response->errorForbidden("用户无权执行此操作");
        }
        $payParams = $this->getPayParams($errand, $this->user());

        if ($payParams) {
            return $this->response->array(['payParams' => $payParams, 'errandId' => $errand->id]);
        } else {
            return $this->response->errorInternal("支付接口异常，请稍后重试");
        }
    }

    public function take(Errand $errand)
    {
        if ($errand->status != Errand::STATUS_PENDING) {
            return $this->response->errorForbidden("订单状态错误");
        }
        if ($this->user()->id == $errand->user->id || $this->user()->real_name_auth) {
            return $this->response->errorForbidden("用户无权执行此操作");
        }
        if ($errand->gender_limit != Errand::GENDER_LIMITS_NOLIMIT && $errand->gender_limit != $this->user()->gender) {
            return $this->response->errorForbidden("订单性别限制不匹配");
        }
        $errand->operator_id = $this->user()->id;
        $errand->status = Errand::STATUS_WAITINGSERVICE;
        $errand->save();
        return $this->response->noContent();
    }

    public function done(Errand $errand)
    {
        if ($errand->status != Errand::STATUS_WAITINGSERVICE) {
            return $this->response->errorForbidden("订单状态错误");
        }
        if ($this->user()->id != $errand->user->id) {
            return $this->response->errorForbidden("用户无权执行此操作");
        }
        $errand->status = Errand::STATUS_DONE;
        $errand->save();
        return $this->response->noContent()->setStatusCode(200);
    }

    public function checkPaymentStatus(Errand $errand)
    {
        $payment = \EasyWeChat::payment();
        $order = $payment->order->queryByOutTradeNumber($errand->payment_out_trade_no);
        if ($order['trade_state'] == 'SUCCESS') {
            $errand->status = Errand::STATUS_PENDING;
            $errand->save();
        }
        return $this->response->noContent()->setStatusCode(200);
    }

    public function index(Request $request, Errand $errand)
    {
        $query = $errand->query();
        $query->recent();
        if ($status = $request->status) {
            $query->where('status', $status);
        }
        $errands = $query->paginate(20);
        return $this->response->paginator($errands, new BaseErrandTransformer())
            ->addMeta('statusMap', Errand::STATUSES)
            ->addMeta('genderLimitMap', Errand::GENDER_LIMITS);
    }

    private function getPayParams(Errand $errand, User $user)
    {
        $payment = \EasyWeChat::payment();
        $outTradeNo = time() . random_int(0, 9);
        $unifyResult = $payment->order->unify([
            'body' => '校客行跑腿',
            'out_trade_no' => $outTradeNo,
//            'total_fee' => $errand->expense * 100,
            'total_fee' => 1,
            'trade_type' => 'JSAPI',
            'openid' => $user->weapp_openid
        ]);
        if ($unifyResult['return_code'] == 'SUCCESS' && $unifyResult['return_msg'] == 'OK') {
            $payParams = [
                'appId' => $unifyResult['appid'],
                'timeStamp' => time(),
                'nonceStr' => $unifyResult['nonce_str'],
                'package' => "prepay_id={$unifyResult['prepay_id']}",
                'signType' => 'MD5',
            ];
            $payParams['paySign'] = generate_sign($payParams, config('wechat.payment.default.key'));
            $errand->payment_out_trade_no = $outTradeNo;
            $errand->save();
            return $payParams;
        }
        return null;
    }

}
