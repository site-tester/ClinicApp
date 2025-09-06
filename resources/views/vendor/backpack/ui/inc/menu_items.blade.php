{{-- This file is used for menu items by any Backpack v6 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i>
        {{ trans('backpack::base.dashboard') }}</a></li>

<x-backpack::menu-dropdown title="Users" icon="la la-users">
    <x-backpack::menu-dropdown-header title="Authentication" />
    <x-backpack::menu-dropdown-item title="Users" icon="la la-user" :link="backpack_url('user')" />
    <x-backpack::menu-dropdown-item title="Roles" icon="la la-group" :link="backpack_url('role')" />
    <x-backpack::menu-dropdown-item title="Permissions" icon="la la-key" :link="backpack_url('permission')" />
</x-backpack::menu-dropdown>

<x-backpack::menu-item title='Content Management' icon='la la-file-o' :link="backpack_url('page')" />

<x-backpack::menu-item title="Patients" icon="la la-question" :link="backpack_url('patient')" />
{{-- <x-backpack::menu-item title="Inventories" icon="la la-question" :link="backpack_url('inventory')" /> --}}
{{-- <x-backpack::menu-item title="Inventory categories" icon="la la-question" :link="backpack_url('inventory-category')" /> --}}

<x-backpack::menu-dropdown title="Inventory" icon="la la-boxes">
    <x-backpack::menu-dropdown-header title="Inventory Management" />
    <x-backpack::menu-dropdown-item title="Inventory" icon="la la-box-open" :link="backpack_url('inventory')" />
    <x-backpack::menu-dropdown-item title="Category" icon="la la-tags" :link="backpack_url('inventory-category')" />
    <x-backpack::menu-dropdown-item title="Stock History" icon="la la-history" :link="backpack_url('inventory-movements')" />
</x-backpack::menu-dropdown>

<x-backpack::menu-item title="Appointments" icon="la la-question" :link="backpack_url('appointment')" />