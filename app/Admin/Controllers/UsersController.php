<?php

namespace App\Admin\Controllers;

use App\Models\Position;
use App\Models\User;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UsersController extends Controller
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
        $grid = new Grid(new User);

        $grid->name('姓名');
        $grid->avatar('头像')->image('', 40, 40);
        $grid->gender('性别')->using(['female' => '女', 'male' => '男', 'secret' => '保密']);
        $grid->phone('联系电话');
        $grid->created_at('创建时间')->sortable();

        $grid->filter(function (Grid\Filter $filter) {
            $filter->disableIdFilter();
            $filter->equal('id', '用户')->select(User::all()->pluck('name', 'id'));
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
        $show = new Show(User::findOrFail($id));

        $show->name('姓名');
        $show->avatar('头像')->image();
        $show->gender('性别')->using(['male' => '男', 'female' => '女', 'secret' => '保密']);
        $show->phone('电话');
        $show->created_at('创建时间');
        $show->updated_at('修改时间');
        $show->weapp_openid('Weapp openid');
        $show->weixin_session_key('Weixin session key');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User);

        $form->text('name', '姓名')->required();
        $form->image('avatar', '头像');
        $form->select('gender', '性别')->options(['male' => '男', 'female' => '女', 'secret' => '保密'])->required();
        $form->mobile('phone', '电话')->required();
        $form->text('weapp_openid', 'Weapp openid');
        $form->text('weixin_session_key', 'Weixin session key');

        return $form;
    }
}
