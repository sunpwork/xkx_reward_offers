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
        if ($this->user()->id == $errand->user->id || ($errand->operator && $this->user()->id == $errand->operator->id)) {
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
        if ($this->user()->id == $errand->user->id || !$this->user()->real_name_auth) {
            return $this->response->errorForbidden("用户无权执行此操作");
        }
        if ($errand->gender_limit != Errand::GENDER_LIMITS_NOLIMIT && $errand->gender_limit != $this->user()->gender) {
            return $this->response->errorForbidden("订单性别限制不匹配");
        }
        $errand->operator_id = $this->user()->id;
        $errand->status = Errand::STATUS_WAITINGSERVICE;
        $errand->save();
        return $this->response->noContent()->setStatusCode(200);
    }

    public function done(Errand $errand)
    {
        if ($errand->status != Errand::STATUS_WAITINGSERVICE) {
            return $this->response->errorForbidden("订单状态错误");
        }
        if ($this->user()->id != $errand->user->id) {
            return $this->response->errorForbidden("用户无权执行此操作");
        }

        $transferResult = $this->transferToBalance($errand);

        if (isset($transferResult['partner_trade_no'])) {
            $errand->payment_partner_trade_no = $transferResult['partner_trade_no'];
            $errand->status = Errand::STATUS_DONE;
            $errand->save();
            return $this->response->noContent()->setStatusCode(200);
        } else {
            return $this->response->error($transferResult['err_code_des'],500);
        }
    }

    public function cancel(Errand $errand)
    {
        if ($errand->status == Errand::STATUS_WAITINGSERVICE || $errand->status == Errand::STATUS_DONE || $errand->status == Errand::STATUS_CANCELED) {
            return $this->response->errorForbidden("订单状态错误");
        }
        if ($this->user()->id != $errand->user->id) {
            return $this->response->errorForbidden("用户无权执行此操作");
        }
        $this->refund($errand);
        $errand->status = Errand::STATUS_CANCELED;
        $errand->save();
        return $this->response->noContent()->setStatusCode(200);
    }

    public function checkPaymentStatus(Errand $errand)
    {
        $order = $this->queryOrder($errand);
        if ($order && $order['trade_state'] == 'SUCCESS') {
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

    public function userIndex(Request $request){
        $errands = $this->user()->userErrands()->recent()->paginate(20);
        return $this->response->paginator($errands, new BaseErrandTransformer())
            ->addMeta('statusMap', Errand::STATUSES)
            ->addMeta('genderLimitMap', Errand::GENDER_LIMITS);
    }

    public function operatorIndex(Request $request){
        $errands = $this->user()->operatorErrands()->recent()->paginate(20);

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

    private function queryOrder(Errand $errand)
    {
        if ($errand->payment_out_trade_no) {
            $payment = \EasyWeChat::payment();
            $order = $payment->order->queryByOutTradeNumber($errand->payment_out_trade_no);
            return $order;
        }
        return null;
    }

    private function refund(Errand $errand)
    {
        $order = $this->queryOrder($errand);
        if ($order && $order['trade_state'] == 'SUCCESS') {
            $payment = \EasyWeChat::payment();
            $payment->refund->byOutTradeNumber($errand->payment_out_trade_no, time() . random_int(0, 9), $order['cash_fee'], $order['cash_fee']);
        }
    }

    private function transferToBalance(Errand $errand)
    {
        $payment = \EasyWeChat::payment();
        $result = $payment->transfer->toBalance([
            'partner_trade_no' => time() . random_int(0, 9), // 商户订单号，需保持唯一性(只能是字母或者数字，不能包含有符号)
            'openid' => $errand->operator->weapp_openid,
            'check_name' => 'NO_CHECK', // NO_CHECK：不校验真实姓名, FORCE_CHECK：强校验真实姓名
//            'amount' => $errand->expense * 100, // 企业付款金额，单位为分
            'amount' => 100,
            'desc' => '校客行跑腿赏金', // 企业付款操作说明信息。必填
        ]);
        return $result;
    }

}
