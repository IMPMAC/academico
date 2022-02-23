<?php

namespace App\Http\Controllers\Admin;

use App\Models\Rhythm;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Validation\Rule;

class RhythmCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;

    public function setup()
    {
        CRUD::setModel(Rhythm::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/rhythm');
        CRUD::setEntityNameStrings(__('rhythm'), __('rhythms'));
        CRUD::addClause('withTrashed');
        CRUD::addButtonFromView('line', 'toggle', 'toggle', 'end');
    }

    protected function setupListOperation()
    {
        CRUD::addColumns([
            [
                'name' => 'name',
                'label' => __('Name'),
            ],
            [
                'name' => 'default_volume',
                'label' => __('Default volume'),
            ],
            [
                'name' => 'product_code',
                'label' => __('Product code'),
            ],
            [
                'name' => 'lms_id',
                'label' => __('LMS code'),
                'type' => 'text',
            ],
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'name' => [
                'required',
                'min:1',
                'max:40',
                Rule::unique($this->crud->getModel()->getTable())->ignore($this->crud->getCurrentEntry()),
            ],
            'default_volume' => 'required',
        ]);

        CRUD::addFields([
            [
                'name' => 'name',
                'label' => __('Name'),
                'type' => 'text',
            ],
            [
                'name' => 'default_volume',
                'label' => __('Default volume'),
                'type' => 'text',
            ],
            [
                'name' => 'product_code',
                'label' => __('Product Code'),
                'type' => 'text',
            ],
            [
                'name' => 'lms_id',
                'label' => __('LMS code'),
                'type' => 'text',
            ],
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
