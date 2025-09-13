<?php
namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PatientCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PatientCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {create as traitCreate;}
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
        CRUD::setModel(\App\Models\PatientProfile::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/patient');
        CRUD::setEntityNameStrings('patient', 'patients');

        CRUD::addClause('whereHas', 'user.roles', function ($query) {
            $query->where('name', 'Patient');
        });
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {

        CRUD::setOperationSetting('showEntryCount', false);

        // Name column from the related User model
        CRUD::addColumn([
            'name'      => 'user_name',
            'label'     => 'Name',
            // 'type'      => 'relationship',
            'entity'    => 'user',
            'attribute' => 'name',
        ]);

        // Email column from the related User model
        CRUD::addColumn([
            'name'      => 'user_email',
            'label'     => 'Email',
            // 'type'      => 'relationship',
            'entity'    => 'user',
            'attribute' => 'email',
        ]);

        // Gender column from the primary PatientProfile model
        CRUD::addColumn([
            'name'  => 'gender',
            'label' => 'Gender',
            'type'  => 'text',
        ]);

        // Phone column from the primary PatientProfile model
        CRUD::addColumn([
            'name'  => 'phone',
            'label' => 'Phone',
            'type'  => 'text',
        ]);
        // Address column from the primary PatientProfile model
        CRUD::addColumn([
            'name'  => 'address',
            'label' => 'Address',
            'type'  => 'text',
        ]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        // CRUD::setValidation(PatientRequest::class);
        // CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */
        CRUD::field([
            'label'     => 'Registered User',
            'type'      => 'select',
            'name'      => 'user_id',
            'entity'    => 'user',
            'model'     => "App\Models\User",
            'attribute' => 'name',

            'options'   => (function ($query) {
                $users = $query->role('Patient', 'web')->doesntHave('patientProfile')->get();
                return $users->pluck('name', 'id');
            }),
            'hint'      => 'Select a registered user that does not have a profile yet',
        ]);

        // CRUD::field([
        //     'name'         => 'image_path',
        //     'label'        => 'Profile Image',
        //     'type'         => 'image',
        //     'crop'         => true,       // set to true to allow cropping, false to disable
        //     'aspect_ratio' => 1,          // omit or set to 0 to allow any aspect ratio
        //     'disk'         => 'public',   // in case you need to show images from a different disk
        //     'prefix'       => 'storage/', // in case your stored files have a common prefix
        // ]);

// Fields for the PatientProfile table

// Dropdown for Gender using select_from_array
        CRUD::field([
            'name'    => 'gender',
            'label'   => 'Gender',
            'type'    => 'select_from_array',
            'options' => [
                'Male'   => 'Male',
                'Female' => 'Female',
                'Other'  => 'Other',
            ],
        ]);

        CRUD::field([
            'name'  => 'phone',
            'label' => 'Phone Number',
            'type'  => 'text',
        ]);

        CRUD::field([
            'name'  => 'address',
            'label' => 'Address',
            'type'  => 'text',
        ]);

        CRUD::field([
            'name'  => 'birth_date',
            'label' => 'Birth Date',
            'type'  => 'date',
        ]);

        CRUD::field([
            'name'  => 'emergency_contact_name',
            'label' => 'Emergency Contact Name',
            'type'  => 'text',
        ]);

        CRUD::field([
            'name'  => 'emergency_contact_phone',
            'label' => 'Emergency Contact Phone',
            'type'  => 'text',
        ]);

        CRUD::field([
            'name'  => 'emergency_contact_relationship',
            'label' => 'Emergency Contact Relationship',
            'type'  => 'text',
        ]);

// Dropdown for PhilHealth Membership using select_from_array
        CRUD::field([
            'name'    => 'philhealth_membership',
            'label'   => 'PhilHealth Membership',
            'type'    => 'select_from_array',
            'options' => [
                'None'      => 'None',
                'Member'    => 'Member',
                'Dependent' => 'Dependent',
            ],
        ]);

        CRUD::field([
            'name'  => 'philhealth_number',
            'label' => 'PhilHealth Number',
            'type'  => 'text',
        ]);

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

    public function create()
    {
        $response = $this->traitCreate();
        $entry    = $this->crud->getCurrentEntry();

        if ($entry) {
            $entry->assignRole('Patient');
        }

        return $response;
    }
}
