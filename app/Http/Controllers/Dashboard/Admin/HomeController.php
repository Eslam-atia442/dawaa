<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use App\Models\Country;
use App\Models\Region;
use App\Models\City;
use App\Models\Role;
use App\Models\Setting;
use App\Models\ActivityLog;

/**
 * @group Admin
 * @subgroup Dashboard Home
 */
class HomeController extends Controller
{
    public function __invoke(): mixed{
        // Gather statistics for all models used in sidebar
        $statistics = [
            'admins'                     => [
                'title'       => 'trans.admin.index',
                'total'       => Admin::count(),
                'active'      => Admin::where('is_active', 1)->count(),
                'inactive'    => Admin::where('is_active', 0)->count(),
                'icon'        => 'ti ti-user-bolt',
                'color'       => 'primary',
                'text_color'  => 'text-white',
                'badge_color' => 'bg-light text-primary',
                'delay'       => '0.1s',
                'route'       => 'admin.admins.index',
                'permission'  => 'read-all-admin'
            ],
            'users'                      => [
                'title'       => 'trans.user.index',
                'total'       => User::count(),
                'verified'    => User::whereNotNull('email_verified_at')->count(),
                'unverified'  => User::whereNull('email_verified_at')->count(),
                'icon'        => 'ti ti-user',
                'color'       => 'success',
                'text_color'  => 'text-white',
                'badge_color' => 'bg-light text-success',
                'delay'       => '0.2s',
                'route'       => 'admin.users.index',
                'permission'  => 'read-all-user'
            ],
            'countries'                  => [
                'title'       => 'trans.country.index',
                'total'       => Country::count(),
                'active'      => Country::where('is_active', 1)->count(),
                'inactive'    => Country::where('is_active', 0)->count(),
                'icon'        => 'ti ti-building-skyscraper',
                'color'       => 'info',
                'text_color'  => 'text-white',
                'badge_color' => 'bg-light text-info',
                'delay'       => '0.3s',
                'route'       => 'admin.countries.index',
                'permission'  => 'read-all-country'
            ],
            'regions'                    => [
                'title'       => 'trans.region.index',
                'total'       => Region::count(),
                'icon'        => 'ti ti-directions',
                'color'       => 'warning',
                'text_color'  => 'text-white',
                'badge_color' => 'bg-light text-warning',
                'delay'       => '0.4s',
                'route'       => 'admin.regions.index',
                'permission'  => 'read-all-region'
            ],
            'cities'                     => [
                'title'       => 'trans.city.index',
                'total'       => City::count(),
                'icon'        => 'ti ti-building-community',
                'color'       => 'secondary',
                'text_color'  => 'text-white',
                'badge_color' => 'bg-light text-secondary',
                'delay'       => '0.5s',
                'route'       => 'admin.cities.index',
                'permission'  => 'read-all-city'
            ],
            'roles'                      => [
                'title'       => 'trans.role.index',
                'total'       => Role::count(),
                'icon'        => 'ti ti-fingerprint',
                'color'       => 'secondary',
                'text_color'  => 'text-white',
                'badge_color' => 'bg-light text-secondary',
                'delay'       => '1.5s',
                'route'       => 'admin.roles.index',
                'permission'  => 'read-all-role'
            ],
            'settings'                   => [
                'title'       => 'trans.setting.index',
                'total'       => Setting::count(),
                'icon'        => 'ti ti-settings',
                'color'       => 'dark',
                'text_color'  => 'text-white',
                'badge_color' => 'bg-light text-dark',
                'delay'       => '1.6s',
                'route'       => 'admin.settings.index',
                'permission'  => 'read-all-setting'
            ]
        ];

        return view('dashboard.admin.home.index', compact('statistics'));
    }
}
