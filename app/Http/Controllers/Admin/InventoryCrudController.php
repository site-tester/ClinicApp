<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\InventoryRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class InventoryCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class InventoryCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Inventory::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/inventory');
        CRUD::setEntityNameStrings('inventory', 'inventories');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->data['breadcrumbs'] = [
            trans('backpack::base.dashboard') => backpack_url('dashboard'),
            'Inventory'                       => backpack_url('inventory'),
            'Lists'                      => false,
        ];
        CRUD::column('name');
        CRUD::column('category')->label('Category Name')->attribute('name');
        CRUD::column('quantity');
        CRUD::column('price')->type('number');

        // Add a custom button to manage stock
        $this->crud->addButtonFromView('line', 'manage_stock', 'manage_stock', 'beginning');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(InventoryRequest::class);
        CRUD::field('name')->label('Item Name');
        CRUD::field('description')->type('textarea');
        CRUD::field([
            'label' => 'Category',
            'type' => 'select',
            'name' => 'inventory_category_id', // foreign key attribute
            'entity' => 'category', // relation name
            'model' => "App\Models\InventoryCategory", // foreign model
            'attribute' => 'name', // foreign attribute that is shown to the user
        ]);
        CRUD::field('quantity')->type('number')->attributes(['step' => 'any']);
        CRUD::field('price')->type('number')->attributes(['step' => 'any']);
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
