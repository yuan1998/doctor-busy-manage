<?php

namespace App\Admin\Controllers;

use App\Models\Department;
use App\Models\Surgery;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class SurgeryController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Surgery(), function (Grid $grid) {
            $grid->model()->with(['department']);
            $grid->scrollbarX();
            $grid->selector(function (Grid\Tools\Selector $selector) {
                $options = Department::query()
                    ->select(['id', 'name'])
                    ->pluck('name', 'id')
                    ->prepend('全部' ,'');
                $selector->select('department_id', '科室', $options);
                $selector->select('enable', '是否启用', [
                    '' => '全部',
                    0 => '禁用',
                    1 => '启用',
                ]);
            });

            $grid->column('id')->sortable();
            $grid->column('department.name', '科室');
            $grid->column('name');
            $grid->column('enable')->switch();
            $grid->column('surgery_time');
            $grid->column('created_at');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('name');
                $filter->equal('enable')->radio([
                    '' => '全部',
                    0 => '禁用',
                    1 => '启用',
                ]);

            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new Surgery(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('enable');
            $show->field('surgery_time');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Surgery(), function (Form $form) {
            $form->display('id');
            $form->text('name');
            $form->switch('enable')->default(1);
            $form->number('surgery_time')->min(1)->default(30);

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
