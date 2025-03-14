<?php

namespace App\Admin\Controllers;

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
            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('enable')->switch();
            $grid->column('surgery_time');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');

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
