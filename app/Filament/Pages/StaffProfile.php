<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;

use App\Models\Country;
use App\Models\Department;
use App\Enum\CitizenEnum;
use App\Enum\MarriageEnum;
use App\Enum\GenderEnum;
use App\Enum\NationalityEnum;
use App\Enum\RaceEnum;
use App\Enum\ReligionEnum;
use App\Enum\StaffTypeEnum;
use App\Enum\StatusEnum;

class StaffProfile extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'My Profile';
    protected static ?string $slug = 'staff-profile';
    protected static string $view = 'filament.pages.staff-profile';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['SA', 'AA', 'NAO','AO']);
    }

    public function mount(): void
    {
        $user = Auth::user();

        $staff = \App\Models\Staff::where('email', $user->email)->first();

        if ($staff) {
            $this->form->fill($staff->toArray());
        } else {
            if ($user->hasRole('SA')) {
                $this->form->fill([
                    'name'  => $user->name,
                    'email' => $user->email,
                ]);
            } else {
                abort(403, 'Staff profile not found.');
            }
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2) // two columns
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Full Name')
                            ->required(),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required(),

                        Forms\Components\TextInput::make('phone_number')
                            ->tel()
                            ->numeric()
                            ->required()
                            ->rule('digits_between:10,11'),

                        Forms\Components\TextInput::make('nric')
                            ->required()
                            ->maxLength(12),

                        Forms\Components\Select::make('nationality')
                            ->label('Nationality')
                            ->options(\App\Models\Country::pluck('name', 'name')->toArray())
                            ->required()
                            ->searchable(),

                        Forms\Components\Select::make('nationality_type')
                            ->required()
                            ->options(\App\Enum\NationalityEnum::class),

                        Forms\Components\Select::make('citizen')
                            ->label('Bumiputera')
                            ->required()
                            ->options(\App\Enum\CitizenEnum::class),

                        Forms\Components\Select::make('marriage_status')
                            ->required()
                            ->options(\App\Enum\MarriageEnum::class),

                        Forms\Components\Select::make('gender')
                            ->required()
                            ->options(\App\Enum\GenderEnum::class),

                        Forms\Components\Select::make('race')
                            ->required()
                            ->options(\App\Enum\RaceEnum::class),

                        Forms\Components\Select::make('religion')
                            ->required()
                            ->options(\App\Enum\ReligionEnum::class),

                        Forms\Components\Select::make('institute_id')
                            ->label('Institute')
                            ->options(\App\Models\Institute::pluck('name', 'id')->toArray())
                            ->reactive(),

                        Forms\Components\Select::make('department_id')
                            ->label('Department')
                            ->reactive()
                            ->options(function (callable $get) {
                                $instituteId = $get('institute_id');
                                if (! $instituteId) {
                                    return [];
                                }
                                return \App\Models\Department::where('institute_id', $instituteId)
                                    ->pluck('name', 'id')
                                    ->toArray();
                            }),

                        Forms\Components\Select::make('staff_type')
                            ->required()
                            ->options(\App\Enum\StaffTypeEnum::class),

                        Forms\Components\TextInput::make('position'),

                        Forms\Components\Select::make('employment_status')
                            ->options(\App\Enum\StatusEnum::class),
                    ]),
            ])
            ->statePath('data');
    }


    public function save(): void
    {
        $user = Auth::user();

        $staff = \App\Models\Staff::where('email', $user->email)->first();

        if ($staff) {
            $staff->update($this->form->getState());
        } else {
            if ($user->hasRole('SA')) {
                \App\Models\Staff::create($this->form->getState());
            } else {
                abort(403, 'Not allowed to create profile.');
            }
        }

        $this->notify('success', 'Profile saved successfully.');
    }

    
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
