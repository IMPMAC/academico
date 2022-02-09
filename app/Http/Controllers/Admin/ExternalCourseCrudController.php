<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ExternalCourseRequest as StoreRequest;
use App\Http\Requests\ExternalCourseRequest as UpdateRequest;
use App\Models\Course;
use App\Models\Level;
use App\Models\Partner;
use App\Models\Period;
use App\Models\Rhythm;
use App\Models\Room;
use App\Models\SchedulePreset;
use App\Models\Teacher;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ExternalCourseCrudController.
 * @property-read CrudPanel $crud
 */
class ExternalCourseCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation { store as traitStore; }
    use UpdateOperation { update as traitUpdate; }
    use DeleteOperation;

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:courses.edit');
    }

    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        CRUD::setModel(Course::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/externalcourse');
        CRUD::setEntityNameStrings(__('External Course'), __('External Courses'));
        CRUD::addClause('external');

        CRUD::enableExportButtons();
    }

    /*
    |--------------------------------------------------------------------------
    | CrudPanel Configuration
    |--------------------------------------------------------------------------
    */

    protected function setupListOperation()
    {
        CRUD::setColumns([
            [
                // RYTHM
                'label' => __('Partnership'),
                'type' => 'select',
                'name' => 'partner_id',
                'entity' => 'partner',
                'attribute' => 'name',
                'model' => Partner::class,
            ],

            [
                // RYTHM
                'label' => __('Rhythm'),
                'type' => 'select',
                'name' => 'rhythm_id',
                'entity' => 'rhythm',
                'attribute' => 'name',
                'model' => Rhythm::class,
            ],

            [
                // LEVEL
                'label' => __('Level'),
                'type' => 'select',
                'name' => 'level_id',
                'entity' => 'level',
                'attribute' => 'name',
                'model' => Level::class,
            ],

            [
                'name' => 'name',
                'label' => __('Name'),
            ],

            [
                'name' => 'volume',
                'label' => __('Volume'),
                'suffix' => 'h',
                'type' => 'number',
            ],

            [
                'name' => 'hourly_price',
                'label' => __('Hourly Price'),
                'prefix' => '$',
                'type' => 'number',
            ],

            [
                // TEACHER
                'label' => __('Teacher'),
                'type' => 'select',
                'name' => 'teacher_id',
                'entity' => 'teacher',
                'attribute' => 'name',
                'model' => Teacher::class,
                'searchLogic' => false,
            ],

            [
                // ROOM
                'label' => __('Room'),
                'type' => 'select',
                'name' => 'room_id',
                'entity' => 'room',
                'attribute' => 'name',
                'model' => Room::class,
            ],

            // COURSE SCHEDULED TIMES
            [
                'name' => 'times',
                'label' => __('Schedule'),
                'type' => 'model_function',
                'function_name' => 'getCourseTimesAttribute',
                'limit' => 150,
            ],

            // HEAD COUNT
            [
                'name' => 'head_count',
                'label' => __('Students'),
            ],

            // HEAD COUNT
            [
                'name' => 'new_students',
                'label' => __('Year Students'),
            ],

            [
                'name' => 'start_date',
                'label' => __('Start Date'),
                'type' => 'date',
            ],

            [
                'name' => 'end_date',
                'label' => __('End Date'),
                'type' => 'date',
            ],

        ]);

        CRUD::addFilter(
            [ // select2 filter
                'name' => 'rhythm_id',
                'type' => 'select2',
                'label' => __('Rhythm'),
            ],
            fn () => Rhythm::all()->pluck('name', 'id')->toArray(),
            function ($value) {
                CRUD::addClause('where', 'rhythm_id', $value);
            },
            function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
            }
        );

        CRUD::addFilter(
            [ // select2 filter
                'name' => 'teacher_id',
                'type' => 'select2',
                'label' => __('Teacher'),
            ],
            fn () => Teacher::all()->pluck('name', 'id')->toArray(),
            function ($value) {
                CRUD::addClause('where', 'teacher_id', $value);
            },
            function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
            }
        );

        CRUD::addFilter(
            [ // select2 filter
                'name' => 'level_id',
                'type' => 'select2',
                'label' => __('Level'),
            ],
            fn () => Level::all()->pluck('name', 'id')->toArray(),
            function ($value) {
                CRUD::addClause('where', 'level_id', $value);
            },
            function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
            }
        );

        CRUD::addFilter(
            [ // select2 filter
                'name' => 'period_id',
                'type' => 'select2',
                'label' => __('Period'),
            ],
            fn () => Period::all()->pluck('name', 'id')->toArray(),
            function ($value) {
                CRUD::addClause('where', 'period_id', $value);
            },
            function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
                $period = Period::get_default_period()->id;
                CRUD::addClause('where', 'period_id', $period);
                $this->crud->getRequest()->request->add(['period_id' => $period]); // to make the filter look active
            }
        );
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);

        CRUD::addFields([
            [
                // RYTHM
                'label' => __('Partnership'),
                'type' => 'select',
                'name' => 'partner_id',
                'entity' => 'partner',
                'attribute' => 'name',
                'model' => Partner::class,
                'tab' => __('Course info'),
            ],

            [
                // RYTHM
                'label' => __('Rhythm'),
                'type' => 'select',
                'name' => 'rhythm_id',
                'entity' => 'rhythm',
                'attribute' => 'name',
                'model' => Rhythm::class,
                'tab' => __('Course info'),
            ],

            [
                // LEVEL
                'label' => __('Level'),
                'type' => 'select',
                'name' => 'level_id',
                'entity' => 'level',
                'attribute' => 'name',
                'model' => Level::class,
                'tab' => __('Course info'),
            ],

            [
                'name' => 'name',
                'label' => __('Name'),
                'tab' => __('Course info'),
            ],

            [
                'name' => 'volume',
                'label' => __('Volume'),
                'suffix' => 'h',
                'tab' => __('Course info'),
            ],

            [
                'name' => 'hourly_price',
                'label' => __('Hourly Price'),
                'prefix' => '$',
                'tab' => __('Course info'),
            ],

            [
                // TEACHER
                'label' => __('Teacher'),
                'type' => 'select',
                'name' => 'teacher_id',
                'entity' => 'teacher',
                'attribute' => 'name',
                'model' => Teacher::class,
                'tab' => __('Course info'),
            ],

            [
                // ROOM
                'label' => __('Room'),
                'type' => 'select',
                'name' => 'room_id',
                'entity' => 'room',
                'attribute' => 'name',
                'model' => Room::class,
                'tab' => __('Course info'),
            ],

            [
                // RYTHM
                'label' => __('Campus'),
                'type' => 'hidden',
                'name' => 'campus_id',
                'value' => 2,
                'tab' => __('Course info'),
            ],

            [
                'name' => 'price',
                'type' => 'hidden',
                'value' => 0,
                'tab' => __('Course info'),
            ],

            [
                // PERIOD
                'label' => __('Period'),
                'type' => 'select',
                'name' => 'period_id',
                'entity' => 'period',
                'attribute' => 'name',
                'model' => Period::class,
                'tab' => __('Schedule'),
                'default' => Period::get_enrollments_period()->id,
            ],

            [
                'name' => 'start_date',
                'label' => __('Start Date'),
                'type' => 'date',
                'tab' => __('Schedule'),
                'default' => Period::get_enrollments_period()->start,
            ],

            [
                'name' => 'end_date',
                'label' => __('End Date'),
                'type' => 'date',
                'tab' => __('Schedule'),
                'default' => Period::get_enrollments_period()->end,
            ],

            [
                'name' => 'head_count',
                'label' => __('Head Count'),
                'type' => 'number',
                'tab' => __('Course info'),
            ],

            [
                'name' => 'new_students',
                'label' => __('Students to count in year total'),
                'type' => 'number',
                'tab' => __('Course info'),
            ],

            [   // repeatable
                'name' => 'times',
                'label' => __('Course Schedule'),
                'type' => 'repeatable',
                'subfields' => [
                    [
                        'name' => 'day',
                        'label' => __('Day'),
                        'type' => 'select_from_array',
                        'options' => [
                            0 => __('Sunday'),
                            1 => __('Monday'),
                            2 => __('Tuesday'),
                            3 => __('Wednesday'),
                            4 => __('Thursday'),
                            5 => __('Friday'),
                            6 => __('Saturday'),
                        ],
                        'allows_null' => false,
                        'default' => 1,
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ],
                    [
                        'name' => 'start',
                        'type' => 'time',
                        'label' => __('Start'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ],
                    [
                        'name' => 'end',
                        'type' => 'time',
                        'label' => __('End'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ],
                ],
                'init_rows' => 0,
                'tab' => __('Schedule'),
            ],

            [   // view
                'name' => 'custom-ajax-button',
                'type' => 'view',
                'view' => 'courses/schedule-preset-alert',
                'tab' => __('Schedule'),
            ],
        ]);

        CRUD::addField([
            'name' => 'schedulepreset',
            'label' => __('Schedule Preset'),
            'type' => 'select_from_array',
            'options' => array_column(SchedulePreset::all()->toArray(), 'name', 'presets'),
            'allows_null' => true,
            'tab' => __('Schedule'),
        ]);
    }

    protected function setupUpdateOperation()
    {
        CRUD::addFields([
            [
                // RYTHM
                'label' => __('Partnership'),
                'type' => 'select',
                'name' => 'partner_id',
                'entity' => 'partner',
                'attribute' => 'name',
                'model' => Partner::class,
                'tab' => __('Course info'),
            ],

            [
                // RYTHM
                'label' => __('Rhythm'),
                'type' => 'select',
                'name' => 'rhythm_id',
                'entity' => 'rhythm',
                'attribute' => 'name',
                'model' => Rhythm::class,
                'tab' => __('Course info'),
            ],

            [
                // LEVEL
                'label' => __('Level'),
                'type' => 'select',
                'name' => 'level_id',
                'entity' => 'level',
                'attribute' => 'name',
                'model' => Level::class,
                'tab' => __('Course info'),
            ],

            [
                'name' => 'name',
                'label' => __('Name'),
                'tab' => __('Course info'),
            ],

            [
                'name' => 'volume',
                'label' => __('Volume'),
                'suffix' => 'h',
                'tab' => __('Course info'),
            ],

            [
                'name' => 'hourly_price',
                'label' => __('Hourly Price'),
                'prefix' => '$',
                'tab' => __('Course info'),
            ],

            [
                // TEACHER
                'label' => __('Teacher'),
                'type' => 'select',
                'name' => 'teacher_id',
                'entity' => 'teacher',
                'attribute' => 'name',
                'model' => Teacher::class,
                'tab' => __('Course info'),
            ],

            [
                // ROOM
                'label' => __('Room'),
                'type' => 'select',
                'name' => 'room_id',
                'entity' => 'room',
                'attribute' => 'name',
                'model' => Room::class,
                'tab' => __('Course info'),
            ],

            [
                // RYTHM
                'label' => __('Campus'),
                'type' => 'hidden',
                'name' => 'campus_id',
                'value' => 2,
                'tab' => __('Course info'),
            ],

            [
                'name' => 'price',
                'type' => 'hidden',
                'value' => 0,
                'tab' => __('Course info'),
            ],

            [
                // PERIOD
                'label' => __('Period'),
                'type' => 'select',
                'name' => 'period_id',
                'entity' => 'period',
                'attribute' => 'name',
                'model' => Period::class,
                'tab' => __('Schedule'),
                'default' => Period::get_enrollments_period()->id,
            ],

            [
                'name' => 'start_date',
                'label' => __('Start Date'),
                'type' => 'date',
                'tab' => __('Schedule'),
                'default' => Period::get_enrollments_period()->start,
            ],

            [
                'name' => 'end_date',
                'label' => __('End Date'),
                'type' => 'date',
                'tab' => __('Schedule'),
                'default' => Period::get_enrollments_period()->end,
            ],

            [
                'name' => 'head_count',
                'label' => __('Head Count'),
                'type' => 'number',
                'tab' => __('Course info'),
            ],

            [
                'name' => 'new_students',
                'label' => __('Students to count in year total'),
                'type' => 'number',
                'tab' => __('Course info'),
            ],

            [   // repeatable
                'name' => 'times',
                'label' => __('Course Schedule'),
                'type' => 'repeatable',
                'subfields' => [
                    [
                        'name' => 'day',
                        'label' => __('Day'),
                        'type' => 'select_from_array',
                        'options' => [
                            0 => __('Sunday'),
                            1 => __('Monday'),
                            2 => __('Tuesday'),
                            3 => __('Wednesday'),
                            4 => __('Thursday'),
                            5 => __('Friday'),
                            6 => __('Saturday'),
                        ],
                        'allows_null' => false,
                        'default' => 1,
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ],
                    [
                        'name' => 'start',
                        'type' => 'time',
                        'label' => __('Start'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ],
                    [
                        'name' => 'end',
                        'type' => 'time',
                        'label' => __('End'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ],
                ],
                'init_rows' => 0,
                'tab' => __('Schedule'),
            ],
        ]);

        CRUD::setValidation(UpdateRequest::class);
    }

    public function update()
    {
        $course = $this->crud->getCurrentEntry();
        $newCourseTimes = collect(json_decode($this->crud->getRequest()->input('times'), null, 512, JSON_THROW_ON_ERROR));
        $course->saveCourseTimes($newCourseTimes);

        // update model
        return $this->traitUpdate();
    }

    public function store()
    {
        // if a schedule preset was applied, use it
        if ($this->crud->getRequest()->input('schedulepreset') !== null) {
            $courseTimes = collect(json_decode($this->crud->getRequest()->input('schedulepreset'), null, 512, JSON_THROW_ON_ERROR));
        } else {
            // otherwise, use any user-defined course times
            $courseTimes = collect(json_decode($this->crud->getRequest()->input('times'), null, 512, JSON_THROW_ON_ERROR));
        }

        $response = $this->traitStore();
        $course = $this->crud->getCurrentEntry();

        // apply course times to the parent.
        $course->saveCourseTimes($courseTimes);

        return $response;
    }
}
