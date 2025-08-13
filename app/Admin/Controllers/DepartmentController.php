<?php

namespace App\Admin\Controllers;

use App\Models\Department;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class DepartmentController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Department(), function (Grid $grid) {
            $grid->scrollbarX();
            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('description');
            $grid->column('enable')->switch();
            $grid->column('has_room')
                ->if(function () {
                    return $this->has_room == 0;
                })
                ->using([
                    0 => '不需要手术室',
                ])
                ->label([
                    0 => 'danger',
                ])
                ->else()
                ->display(function () {
                    return json_decode($this->rooms, true);
                })->label();
            $grid->column('__')->display(function () {
                $doctorUrl = admin_url("/doctors?_selector%5Bdepartment_id%5D={$this->id}");
                return <<<HTML
<div>
 <button class="btn btn-sm btn-primary" onclick="Dcat.reload('$doctorUrl')">
    <i class="feather icon-users"></i> 查看医生
 </button>

</div>
HTML;

            });
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
        return Show::make($id, new Department(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('description');
            $show->field('enable');
            $show->field('has_room');
            $show->field('rooms');
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
        return Form::make(new Department(), function (Form $form) {
            $form->display('id');
            $form->text('name');
            $form->text('description');
            $form->switch('enable')->default(1);
            $form->radio('has_room')->options([
                0 => '不需要手术室',
                1 => '需要手术室',
            ])->default(0)
                ->when(1, function (Form $form) {
                    $form->tags('rooms')
                        ->rules("required_if:has_room,1")
                        ->help('请用逗号分隔手术室名称');
                });


            $form->display('created_at');
            $form->display('updated_at');
        })->saving(function (Form $form) {
            // 如果不需要手术室，则清空手术室字段为null
            if ($form->has_room == 0) {
                $form->rooms = null;
            }
        });
    }
}
