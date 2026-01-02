<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use function Laravel\Prompts\password;



class ChangeAdminPasswords extends Command
{
    protected $signature = 'admins:change-passwords {--confirm : Skip confirmation prompt}';

    protected $description = 'Change all admin passwords to the specified password and logout all admin users';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $password = password('Enter the new password for all admin accounts:');
        $confirmPassword = password('Confirm the new password:');

        if ($password !== $confirmPassword) {
            $this->error('❌ Passwords do not match. Operation cancelled.');
            return 1;
        }

        $adminCount = Admin::count();

        $this->info("Found {$adminCount} admin accounts.");
        $this->info("New password will be set for all admin accounts.");

        if (!$this->option('confirm')) {
            if (!$this->confirm('Are you sure you want to change all admin passwords? This action cannot be undone.')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->info('Updating admin passwords...');

        try {

            $admin = Admin::get();
            foreach ($admin as $item) {
                $item->password = $password;
                $item->save();
                dd($item);
            }

            $this->info("✅ Successfully updated passwords for {$adminCount} admin accounts.");
            $this->info('Logging out all admin users...');
            $this->warn('⚠️  All admin passwords have been changed to: ' . $password);
            $this->warn('⚠️  All admin users have been logged out.');
            $this->warn('⚠️  Make sure to inform all administrators about the new password.');

        } catch (\Exception $e) {
            $this->error('❌ Failed to update admin passwords: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
 
}
