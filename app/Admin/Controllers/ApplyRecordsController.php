<?php

namespace App\Admin\Controllers;

use App\Models\ApplyRecord;
use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\User;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;

class ApplyRecordsController extends Controller
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
        $grid = new Grid(new ApplyRecord);
        $grid->model()->with(['position', 'user']);


        $grid->column('position.title', '职位');
        $grid->column('user.name', '用户');
        $grid->name('真实姓名');
        $grid->phone('电话');
        $grid->gender('性别')->using(['male' => '男', 'female' => '女']);
        $grid->created_at('提交时间');
        $grid->filter(function (Grid\Filter $filter) {
            $filter->disableIdFilter();
            $filter->equal('user_id', '用户')->select(User::all()->pluck('name', 'id'));
            $filter->equal('position_id', '兼职')->select(Position::all()->pluck('title', 'id'));
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
        $show = new Show(ApplyRecord::findOrFail($id));

        $show->position('兼职信息', function (Show $position) {
            $position->title('标题');
            $position->covers('封面图片')->image();
            $position->detail_info('详细信息');
            $position->contact_man('联系人');
            $position->contact_phone('联系人电话');
            $position->quantity('招聘数量');
            $position->apply_quantity('申请人数');
            $position->salary('薪资');
            $position->work_address('工作地点');
            $position->created_at('创建时间');
            $position->updated_at('修改时间');
        });
        $show->user('用户信息', function (Show $user) {
            $user->name('姓名');
            $user->avatar('头像')->image();
            $user->gender('性别')->using(['male' => '男', 'female' => '女', 'secret' => '保密']);
            $user->phone('电话');
            $user->created_at('创建时间');
        });
        $show->name('姓名');
        $show->phone('联系电话');
        $show->gender('性别')->using(['male' => '男', 'female' => '女']);
        $show->created_at('申请时间');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ApplyRecord);

        $form->text('name', '姓名');
        $form->mobile('phone', '联系电话');
        $form->select('gender', '性别')->options(['male' => '男', 'female' => '女']);

        return $form;
    }
}
