<?php

namespace Database\Seeders;

use App\Models\Role;
use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Log;
use Spatie\Permission\Models\Permission;

class InsertPermissionKeysAndAssignToRoles extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('permissions')->truncate();
        $actions = [
            'list'   => 'list',
            'view'   => 'view',
            'create' => 'create',
            'update' => 'update',
            'delete' => 'delete',
        ];

        $resource = [
            'users' => 'User',
            'roles' => 'Role',
        ];

        $permission_names = [];
        foreach($resource as $key => $value){
            $parent = Permission::firstOrCreate(
                ['name' => 'admin.access.'.$key],
                ['guard_name' => 'web',
                    'description' => $value,

            ]);
            // dd($parent);
            foreach($actions as $c_key => $actions_value){
                $permission_name = "admin.{$key}.{$c_key}";
                $permission = Permission::firstOrCreate(
                ['name'         => $permission_name],
                    ['guard_name'   => 'web',
                             'description' => "$actions_value",
                            'parent_id'     => $parent->id
                            ]
                    );
                $permission_names[] = $permission_name;
                // dd($permission_names);
            }
        }
        if(!empty($permission_names)){
            // dd($permission_names);
            $permission_ids = Permission::whereNotNull('parent_id')
                ->whereIn('name', $permission_names)
                ->pluck('id')
                ->toArray();
            if (!empty($permission_ids)) {
                $admin_role = Role::where('name', 'Administrator')->first();
                if ($admin_role) {
                    try {
                        // Sync all permissions (overwrite old ones)
                        $admin_role->syncPermissions($permission_ids);

                        Log::info('✅ Administrator permissions synced successfully.');
                    } catch (Exception $e) {
                        Log::error('❌ Failed to assign permissions to Administrator: ' . $e->getMessage());
                    }
                }
            }
        }
    }
}
