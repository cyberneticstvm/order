<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\CampType;
use App\Models\ConsultationType;
use App\Models\Department;
use App\Models\PaymentMode;
use App\Models\ProductSubcategory;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserBranch;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DumpData extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $user = User::create([
            'name' => 'Vijoy Sasidharan',
            'username' => 'admin',
            'email' => 'mail@cybernetics.me',
            'mobile' => '9188848860',
            'password' => bcrypt('stupid')
        ]);

        $branch = Branch::create([
            'name' => 'Varkala',
            'code' => 'VKLA',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        $role = Role::create(['name' => 'Administrator']);
        $permissions = Permission::pluck('id', 'id')->all();
        $role->syncPermissions($permissions);
        $user->assignRole([$role->id]);
        UserBranch::create([
            'user_id' => $user->id,
            'branch_id' => $branch->id
        ]);

        Setting::insert([
            'company_name' => 'Devi Speczone Order',
            'qr_code_text' => 'https://devieh.com',
            'consultaton_fee_waived_days' => 0,
            'appointment_starts_at' => '9:00:00',
            'appointment_ends_at' => '19:00:00',
            'per_appointment_minutes' => 15,
            'drug_license_number' => NULL,
            'branch_limit' => 1,
            'allow_sales_at_zero_qty' => 0,
            'tax_type' => 'GST',
            'currency' => '₹',
            'daily_expense_limit' => 1000.00
        ]);

        $pmodes = [
            'Cash', 'Card', 'UPI', 'Bank Transfer', 'Cheque', 'Pay Later', 'Other',
        ];
        foreach ($pmodes as $pmode) :
            PaymentMode::insert([
                'name' => $pmode,
            ]);
        endforeach;
    }
}
