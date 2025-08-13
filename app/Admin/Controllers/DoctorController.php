<?php

namespace App\Admin\Controllers;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\Surgery;
use Carbon\Carbon;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class DoctorController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Doctor(), function (Grid $grid) {

            $grid->model()->with(['surgery', 'department'])->orderBy('id', 'desc');

            $grid->selector(function (Grid\Tools\Selector $selector) {
                $options = Department::query()->select(['id', 'name'])
                    ->pluck('name', 'id')
                    ->prepend('全部', '');

                $selector->select('department_id', '科室', $options);
                $selector->select('enable', '是否启用', [
                    '' => '全部',
                    0 => '禁用',
                    1 => '启用',
                ]);
            });


            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('enable')->switch();
            $grid->column('status')->using(Doctor::STATUS_MAP);
            $grid->column('surgery.name', '手术项目')->display(function ($surgery) {
                if ($surgery && $this->status == Doctor::STATUS_IN_SURGERY) {
                    return join("<br/>", ["项目名称: $surgery", "手术时间: $this->time_range"]);
                }
                return "无手术";
            });
//            $grid->column('start_date');
            $grid->column('department.name', '科室');
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
        return Show::make($id, new Doctor(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('enable');
            $show->field('status');
            $show->field('surgery_id');
            $show->field('start_date');
            $show->field('end_date');
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
        return Form::make(new Doctor(), function (Form $form) {
            $form->display('id');
            $form->text('name');
            $form->switch('enable')->default(1);
            $form->radio('status')->options(Doctor::STATUS_MAP)->default(0);
            $form->select('surgery_id')->options(Surgery::query()->select(['id', 'name'])->pluck('name', 'id'));
            $form->hidden('start_date');
            $form->hidden('end_date');
//            $form->datetimeRange('start_date','end_date','手术时间');

            $form->display('created_at');
            $form->display('updated_at');
            $form->saving(function (Form $form) {

                if ($form->status == Doctor::STATUS_IN_SURGERY) {
                    $surgery_id = $form->input("surgery_id");
                    $surgery = Surgery::find($surgery_id);
                    if (!$surgery) {
                        return $form->response()->error("手术项目不存在");
                    }
                    $form->start_date = now();
                    $form->end_date = now()->addMinutes($surgery->surgery_time);
//                    $form->input('start_date', $now);
//                    $form->input('end_date', $now->addMinutes($surgery->surgery_time));
                } else {
                    $form->surgery_id = null;
                    $form->start_date = null;
                    $form->end_date = null;
                }
            });
        });
    }
}
