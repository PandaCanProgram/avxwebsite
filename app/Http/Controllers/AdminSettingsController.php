<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAdminAppearanceRequest;
use App\Http\Requests\UpdateAdminPasswordRequest;
use App\Http\Requests\UpdateWebsiteMaintenanceRequest;
use Illuminate\Contracts\Foundation\MaintenanceMode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminSettingsController extends Controller
{
    public function edit(MaintenanceMode $maintenanceMode): View
    {
        return view('admin-settings', ['maintenanceEnabled' => $maintenanceMode->active()]);
    }

    public function updatePassword(UpdateAdminPasswordRequest $request): RedirectResponse
    {
        $request->user()->forceFill([
            'password' => $request->validated('password'),
            'remember_token' => Str::random(60),
        ])->save();
        $request->session()->regenerate(true);

        return redirect()->to(route('admin.settings').'#password')->with('passwordUpdated', 'Your password has been updated.');
    }

    public function updateAppearance(UpdateAdminAppearanceRequest $request): RedirectResponse
    {
        $request->user()->forceFill(['admin_theme' => $request->validated('theme')])->save();

        return redirect()->to(route('admin.settings').'#appearance')->with('appearanceUpdated', 'Your appearance preference has been saved.');
    }

    public function updateMaintenance(UpdateWebsiteMaintenanceRequest $request, MaintenanceMode $maintenanceMode): RedirectResponse
    {
        $enabled = $request->boolean('maintenance_enabled');

        if ($enabled && ! $maintenanceMode->active()) {
            $maintenanceMode->activate(['time' => now()->timestamp, 'status' => 503, 'retry' => 60]);
        } elseif (! $enabled && $maintenanceMode->active()) {
            $maintenanceMode->deactivate();
        }

        return redirect()->to(route('admin.settings').'#maintenance')->with(
            'maintenanceUpdated',
            $enabled ? 'Maintenance mode is on. Public visitors will see the maintenance page.' : 'Maintenance mode is off. The website is live.',
        );
    }
}
