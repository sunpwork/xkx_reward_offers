<?php

namespace App\Admin\Controllers;

use App\Models\Errand;
use App\Http\Controllers\Controller;
use App\Models\User;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ErrandsController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Errand);

        $grid->model()->with(['user']);

        $grid->column('user.name', '用户');
        $grid->content('内容');
        $grid->expense('价格')->label('success');
        $grid->location_name('地点');
        $grid->column('status', '状态')->display(function ($status) {
            $labelClass = Errand::STATUSES[$status]['label_class'];
            return "<span class='label $labelClass'>" . Errand::STATUSES[$status]['name'] . '</span>';
        });
        $grid->created_at('创建时间');

        $grid->filter(function (Grid\Filter $filter) {
            $filter->disableIdFilter();
            $filter->equal('user_id', '用户')->select(User::all()->pluck('name', 'id'));

        });

        $grid->actions(function (Grid\Displayers\Actions $actions){
            $actions->disableEdit();
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Errand::findOrFail($id));

        $show->user('用户信息', function (Show $user) {
            $user->name('姓名');
            $user->avatar('头像')->image();
            $user->gender('性别')->using(['male' => '男', 'female' => '女', 'secret' => '保密']);
            $user->phone('电话');
            $user->created_at('创建时间');
        });

        $show->content('内容');
        $show->hidden_content('详细内容');
        $show->appointment_time('出发时间');
        $show->gender_limit('性别限制')->using(array_pluck(Errand::GENDER_LIMITS, 'name', 'value'));
        $show->expense('价格');
        $show->location_name('地点名称');
        $show->payment_out_trade_no('微信支付订单号');
        $show->status('状态')->using(array_pluck(Errand::STATUSES, 'name', 'value'));
        $show->created_at('创建时间');
        if ($show->getModel()->operator) {
            $show->operator('跑腿员信息', function (Show $user) {

                $user->name('姓名');
                $user->avatar('头像')->image();
                $user->gender('性别')->using(['male' => '男', 'female' => '女', 'secret' => '保密']);
                $user->phone('电话');
                $user->created_at('创建时间');

            });
        }
        $show->payment_partner_trade_no('微信转账订单号');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Errand);

        $form->number('user_id', 'User id');
        $form->textarea('content', 'Content');
        $form->textarea('hidden_content', 'Hidden content');
        $form->text('appointment_time', 'Appointment time');
        $form->text('gender_limit', 'Gender limit');
        $form->decimal('expense', 'Expense');
        $form->text('location_name', 'Location name');
        $form->text('location_address', 'Location address');
        $form->decimal('location_latitude', 'Location latitude');
        $form->decimal('location_longitude', 'Location longitude');
        $form->text('payment_out_trade_no', 'Payment out trade no');
        $form->text('status', 'Status');
        $form->number('operator_id', 'Operator id');
        $form->text('payment_partner_trade_no', 'Payment partner trade no');

        return $form;
    }
}
