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
        $grid->apply_quantity('报名数量');
        $grid->salary('薪资');
        $grid->work_address('工作地点');
        $grid->created_at('创建时间');
        $grid->display('显示')->switch();

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

        $show->id('Id');
        $show->category_id('Category id');
        $show->title('Title');
        $show->covers('Covers');
        $show->detail_info('Detail info');
        $show->contact_man('Contact man');
        $show->contact_phone('Contact phone');
        $show->quantity('Quantity');
        $show->apply_quantity('Apply quantity');
        $show->salary('Salary');
        $show->work_address('Work address');
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
        $form = new Form(new Position);

        $form->select('category_id', '分类')->options(Category::all()->pluck('name', 'id'))->required();
        $form->text('title', '标题');
        $form->image('covers', '封面')
            ->rules('mimes:jpeg,bmp,png,gif|dimensions:min_width=200,min_height=200')
            ->uniqueName()
            ->required();
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
