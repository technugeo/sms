<?php

namespace App\Filament\Resources\StaffResource\Pages;

use App\Filament\Resources\StaffResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EditStaff extends EditRecord
{
    protected static string $resource = StaffResource::class;

    protected ?array $originalData = null;

    public function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($record) {
                    \DB::table('audit_log')->insert([
                        'action_by'  => auth()->user()->email ?? 'system',
                        'action_type'=> 'delete',
                        'module'     => 'student', // or 'staff'
                        'record_id'  => $record->id,
                        'old_data'   => json_encode($record->toArray()),
                        'new_data'   => json_encode([]),
                        'notes'      => 'Soft-deleted record',
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'date_time'  => now(),
                    ]);
                }),
            Actions\ForceDeleteAction::make() // similarly attach before/after callback
                ->before(function ($record) {
                    \DB::table('audit_log')->insert([
                        'action_by'  => auth()->user()->email ?? 'system',
                        'action_type'=> 'force_delete',
                        'module'     => 'student', // or 'staff'
                        'record_id'  => $record->id,
                        'old_data'   => json_encode($record->toArray()),
                        'new_data'   => json_encode([]),
                        'notes'      => 'Permanently deleted record',
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'date_time'  => now(),
                    ]);
                }),
            Actions\RestoreAction::make() // optional, you can log restore too
                ->after(function ($record) {
                    \DB::table('audit_log')->insert([
                        'action_by'  => auth()->user()->email ?? 'system',
                        'action_type'=> 'restore',
                        'module'     => 'student',
                        'record_id'  => $record->id,
                        'old_data'   => json_encode([]),
                        'new_data'   => json_encode($record->toArray()),
                        'notes'      => 'Restored record',
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'date_time'  => now(),
                    ]);
                }),
        ];
    }


    /**
     * Pre-fill the form with user name and email.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = $this->record->user;

        $data['full_name'] = $user?->name ?? '';
        $data['email'] = $user?->email ?? '';

        return $data;
    }

    /**
     * Capture original data and update user fields before saving staff.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $staff = $this->record;
        $user = $staff->user;

        // 1️⃣ Capture original data BEFORE updating anything
        $this->originalData = array_merge(
            $staff->getOriginal(),
            [
                'name' => $user?->name ?? null,
                'email' => $user?->email ?? null,
                'access_level' => $user?->roles->pluck('name')->toArray() ?? [],
            ]
        );

        // 2️⃣ Update related user AFTER capturing original data
        if ($user) {
            $user->update([
                'name'  => $data['full_name'],
                'email' => $data['email'],
            ]);

            if (!empty($data['access_level'])) {
                $user->syncRoles([$data['access_level']]);
            }
        }

        // 3️⃣ Remove user fields before saving staff
        unset($data['full_name'], $data['email'], $data['access_level']);

        // 4️⃣ Track updater
        $data['updated_by'] = auth()->user()->email ?? 'system';

        return $data;
    }


    /**
     * After save, log the audit
     */
    protected function afterSave(): void
    {
        $staff = $this->record->fresh();
        $user = $staff->user->fresh();

        // Prepare new data snapshot
        $newData = array_merge(
            $staff->toArray(),
            [
                'name' => $user->name,
                'email' => $user->email,
                'access_level' => $user->roles->pluck('name')->toArray(),
            ]
        );

        DB::table('audit_log')->insert([
            'action_by'  => auth()->user()->email ?? 'system',
            'action_type'=> 'update',
            'module'     => 'staff',
            'record_id'  => $staff->id,
            'old_data'   => json_encode($this->originalData),
            'new_data'   => json_encode($newData),
            'notes'      => 'Staff ' . ($staff->full_name ?? $staff->id) . ' updated.',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'date_time'  => now(),
        ]);
    }
}
