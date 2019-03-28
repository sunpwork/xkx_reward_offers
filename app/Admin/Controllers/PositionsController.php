<?php

namespace App\Admin\Controllers;

use App\Models\Category;
use App\Models\Position;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;
use Illuminate\Support\Facades\Storage;

class PositionsController extends Controller
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
        $grid = new Grid(new Position);

        $grid->model()->with(['category']);

        $grid->column('category.name', '分类');
        $grid->title('标题');
        $grid->contact_man('联系人');
        $grid->contact_phone('联系电话');
        $grid->quantity('招聘人数');
        $grid->apply_quantity('报名人数')->expand(function ($model) {
            $applyRecords = $model->applyRecords->map(function ($applyRecord) {
                return $applyRecord->only(['name', 'phone', 'created_at']);
            });
            return new Table(['姓名', '联系电话', '申请时间'], $applyRecords->toArray());
        });
        $grid->salary('薪资');
        $grid->work_address('工作地点');
        $grid->created_at('创建时间')->sortable();
        $grid->display('显示')->switch();

        $grid->filter(function (Grid\Filter $filter) {
            $filter->disableIdFilter();
            $filter->equal('id', '标题')->select(Position::all()->pluck('title', 'id'));
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
        $show = new Show(Position::findOrFail($id));

        $show->title('标题');
        $show->covers('封面图片')->image();
        $show->detail_info('详细信息');
        $show->contact_man('联系人');
        $show->contact_phone('联系人电话');
        $show->quantity('招聘数量');
        $show->apply_quantity('申请人数');
        $show->salary('薪资');
        $show->work_address('工作地点');
        $show->created_at('创建时间');
        $show->updated_at('修改时间');

        $show->applyRecords('申请记录', function (Grid $applyRecords) {
            $applyRecords->setResource('/admin/apply_records');
            $applyRecords->column('user.name', '用户');
            $applyRecords->name('姓名');
            $applyRecords->phone('联系电话');
            $applyRecords->gender('性别')->using(['male' => '男', 'female' => '女']);
            $applyRecords->created_at('申请时间');
        });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Position);

        $form->select('category_id', '分类')->options(Category::all()->pluck('name', 'id'))->required();
        $form->text('title', '标题');
        $form->image('covers', '封面')
            ->rules('mimes:jpeg,bmp,png,gif|dimensions:min_width=200,min_height=200')
            ->uniqueName();
        $form->textarea('detail_info', '详细内容')->required();
        $form->text('contact_man', '联系人')->required();
        $form->mobile('contact_phone', '联系电话')->required();
        $form->number('quantity', '招聘人数')->default(0)->required();
        $form->text('salary', '薪资')->required();
        $form->text('work_address', '工作地点')->required();
        $form->switch('display', '显示')->default(true);

        return $form;
    }
}
