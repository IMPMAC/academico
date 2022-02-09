<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\GradeTypeRequest;
use App\Models\GradeType;
use App\Models\GradeTypeCategory;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class GradeTypeCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use FetchOperation;

    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        CRUD::setModel(GradeType::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/gradetype');
        CRUD::setEntityNameStrings(__('grade type'), __('grade types'));

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        CRUD::addColumns([
            ['name' => 'name',
                'label' => 'Name', ],
            ['name' => 'total',
                'label' => 'Total', ],
            [
                // 1-n relationship
                'label' => 'Category',
                'type' => 'select',
                'name' => 'grade_type_category_id',
                'entity' => 'category',
                'attribute' => 'name',
                'model' => GradeTypeCategory::class,
            ],
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::addFields([
            [
                'name' => 'name',
                'label' => 'Name',
                'type' => 'text',
            ],

            [
                'name' => 'total',
                'label' => 'Total',
                'type' => 'text',
            ],

            [
                'label' => 'Category',
                'type' => 'relationship',
                'name' => 'category',
                'ajax' => true,
                'inline_create' => [ // specify the entity in singular
                    'entity' => 'gradetypecategory',
                    // the entity in singular
                    // OPTIONALS
                    'force_select' => true,
                    // should the inline-created entry be immediately selected?
                    'modal_class' => 'modal-dialog modal-xl',
                    // use modal-sm, modal-lg to change width
                    'modal_route' => route('gradetypecategory-inline-create'),
                    // InlineCreate::getInlineCreateModal()
                    'create_route' => route('gradetypecategory-inline-create-save'),
                    // InlineCreate::storeInlineCreate()
                ],
            ],
        ]);

        CRUD::setRequiredFields(GradeTypeRequest::class);

        CRUD::setValidation(GradeTypeRequest::class);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
        CRUD::setRequiredFields(GradeTypeRequest::class);
    }

    protected function fetchCategory()
    {
        return $this->fetch(GradeTypeCategory::class);
    }
}
