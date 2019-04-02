<?php

namespace App\Admin\Controllers;

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

        $grid->column('user.name','用户');
        $grid->name('真实姓名');
        $grid->gender('性别')->using(['male' => '男', 'female' => '女']);;
        $grid->phone('联系电话');
        $grid->column('status', '状态')->display(function ($status) {
            $labelClass = RealNameAuth::STATUSES[$status]['label_class'];
            return "<span class='label $labelClass'>" . RealNameAuth::STATUSES[$status]['name'] . '</span>';
        });
        $grid->created_at('提交时间');

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

        $show->id('Id');
        $show->user_id('User id');
        $show->name('Name');
        $show->gender('Gender');
        $show->phone('Phone');
        $show->status('Status');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

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
