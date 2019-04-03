<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Actions\RealNameAuthCheck;
use App\Models\RealNameAuth;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class RealNameAuthsController extends Controller
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
        $grid = new Grid(new RealNameAuth);

        $grid->model()->with(['user']);

        $grid->column('user.name', '用户');
        $grid->name('真实姓名');
        $grid->gender('性别')->using(['male' => '男', 'female' => '女']);;
        $grid->phone('联系电话');
        $grid->column('status', '状态')->display(function ($status) {
            $labelClass = RealNameAuth::STATUSES[$status]['label_class'];
            return "<span class='label $labelClass'>" . RealNameAuth::STATUSES[$status]['name'] . '</span>';
        });
        $grid->created_at('提交时间');

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableEdit();
            if ($actions->row->status == RealNameAuth::STATUS_PENDING) {
                $actions->append(new RealNameAuthCheck($actions->row, RealNameAuth::STATUS_ACTIVE, 'fa-check', '确定通过审核？'));
                $actions->append(new RealNameAuthCheck($actions->row, RealNameAuth::STATUS_INVALID, 'fa-times', '确定不通过审核？'));
            }
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
        $show = new Show(RealNameAuth::findOrFail($id));

        $show->user('用户信息', function (Show $user) {
            $user->name('姓名');
            $user->avatar('头像')->image();
            $user->gender('性别')->using(['male' => '男', 'female' => '女', 'secret' => '保密']);
            $user->phone('电话');
            $user->created_at('创建时间');
        });

        $show->name('真实姓名');
        $show->gender('性别')->using(['male' => '男', 'female' => '女']);
        $show->phone('联系电话');
        $show->realNameAuthImages('认证照片')->unescape()->as(function ($realNameAuthImages) {
            $display = '';
            foreach ($realNameAuthImages as $realNameAuthImage) {
                $display .= "<img src='$realNameAuthImage->url' class='img' />";
            }
            return $display;
        });
        $show->status('状态')->unescape()->as(function ($status) {
            $labelClass = RealNameAuth::STATUSES[$status]['label_class'];
            return "<span class='label $labelClass'>" . RealNameAuth::STATUSES[$status]['name'] . '</span>';
        });
        $show->created_at('提交时间');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new RealNameAuth);

        $form->number('user_id', 'User id');
        $form->text('name', 'Name');
        $form->text('gender', 'Gender');
        $form->mobile('phone', 'Phone');
        $form->text('status', 'Status');

        return $form;
    }
}
