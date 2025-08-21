<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaffResource\Pages;
use App\Filament\Resources\StaffResource\RelationManagers;
use App\Filament\Resources\StaffResource\RelationManagers\AddressRelationManager;

use App\Models\Staff;
use App\Models\Institute;
use App\Models\Country;

use App\Enum\CitizenEnum;
use App\Enum\MarriageEnum;
use App\Enum\GenderEnum;
use App\Enum\NationalityEnum;
use App\Enum\RaceEnum;
use App\Enum\ReligionEnum;
use App\Enum\StaffTypeEnum;
use App\Enum\StatusEnum;

use Filament\Navigation\NavigationItem;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\MultiSelect;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\TrashedFilter;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class StaffResource extends Resource
{


    protected static ?string $model = Staff::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationItems(): array
    {
        $user = auth()->user();
        $items = [];

        // Admin/staff menu
        if ($user->can('view_any_staff')) {
            $items[] = NavigationItem::make('Staff')
                ->icon(static::$navigationIcon)
                ->group(static::getNavigationGroup())
                ->sort(static::getNavigationSort())
                ->url(static::getUrl('index'));
        }

        // Staff "My Profile" menu (everyone with a staff record sees this)
        $staff = Staff::where('email', $user->email)->first();

        if ($staff) {
            $items[] = NavigationItem::make('Profile')
                ->icon('heroicon-o-user-circle')
                ->url(StaffResource::getUrl('view', ['record' => $staff->getKey()]))
                ->group('My Account') // <-- different group
                ->sort(1);
        }

        return $items;
    }




    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make('Basic Information')
                    ->description('Personal details of the staff')
                    ->columns(2) // 2-column grid
                    ->schema([
                        Forms\Components\TextInput::make('full_name')
                            ->label('Full Name')
                            ->required(),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required(),

                        Forms\Components\TextInput::make('phone_number')
                            ->tel()
                            ->required()
                            ->numeric()
                            ->rule('digits_between:10,11'),

                        Forms\Components\TextInput::make('nric')
                            ->required()
                            ->maxLength(12),

                        Forms\Components\Select::make('nationality')
                            ->label('Nationality')
                            ->options(Country::pluck('name', 'name')->toArray())
                            ->required()
                            ->searchable(),

                        Forms\Components\Select::make('nationality_type')
                            ->required()
                            ->options(NationalityEnum::class),

                        Forms\Components\Select::make('citizen')
                            ->label('Bumiputera')
                            ->required()
                            ->options(CitizenEnum::class),

                        Forms\Components\Select::make('marriage_status')
                            ->required()
                            ->options(MarriageEnum::class),

                        Forms\Components\Select::make('gender')
                            ->required()
                            ->options(GenderEnum::class),

                        Forms\Components\Select::make('race')
                            ->required()
                            ->options(RaceEnum::class),

                        Forms\Components\Select::make('religion')
                            ->required()
                            ->options(ReligionEnum::class),
                    ]),

                Forms\Components\Section::make('Employment & Role')
                    ->description('Institute, roles, and employment details')
                    ->columns(2)
                    ->schema([

                    Forms\Components\Select::make('institute_id')
                        ->label('Institute')
                        ->columnSpanFull()
                        ->required()
                        ->options(\App\Models\Institute::pluck('name', 'mqa_institute_id')->toArray()) // key = mqa_institute_id
                        ->default(fn () => auth()->user()->hasRole('super_admin') 
                            ? null 
                            : optional(auth()->user()->staff)->institute_id)
                        ->hidden(fn () => !auth()->user()->hasRole('super_admin')),


                    Forms\Components\Hidden::make('institute_id')
                    
                        ->default(fn () => auth()->user()->institute_id ?? auth()->user()->staff->institute_id ?? null)
                        ->disabled(fn () => auth()->user()->hasRole('super_admin')),



                    // Roles
                    Forms\Components\MultiSelect::make('user.roles')
                        ->label('Roles')
                        ->columnSpanFull()
                        ->required()
                        ->relationship('roles', 'name')
                        ->preload()
                        ->reactive()
                        ->disabled(fn () => !auth()->user()->hasAnyRole(['super_admin', 'account_admin'])),

                    // Department
                    Forms\Components\Select::make('department_id')   
                        ->label('Department')
                        ->reactive()
                        ->options(function (callable $get) {
                            $instituteId = $get('institute_id'); 
                            $options = ['' => 'N/A'];

                            if ($instituteId) {
                                $departments = \App\Models\Department::where('institute_id', $instituteId)
                                    ->pluck('name', 'code')  
                                    ->toArray();
                                $options = $options + $departments;
                            }

                            return $options;
                        })
                        ->default('')
                        ->disabled(function (callable $get) {
                            $roleIds = $get('user.roles') ?? [];
                            $roles = \Spatie\Permission\Models\Role::whereIn('id', $roleIds)->pluck('name')->toArray();

                            // Disable only if "academic_officer" is the ONLY role
                            return count($roles) === 1 && in_array('academic_officer', $roles);
                        }),

                    // Faculty
                    Forms\Components\Select::make('faculty_id')   // 👈 change field name
                        ->label('Faculty')
                        ->reactive()
                        ->options(function (callable $get) {
                            $instituteId = $get('institute_id'); 
                            $options = ['' => 'N/A'];

                            if ($instituteId) {
                                $faculties = \App\Models\Faculty::where('institute_code', $instituteId)
                                    ->pluck('name', 'code')   // 👈 key by code, not id
                                    ->toArray();
                                $options = $options + $faculties;
                            }

                            return $options;
                        }),



                        Forms\Components\TextInput::make('position'),

                        Forms\Components\Select::make('staff_type')
                            ->required()
                            ->options(StaffTypeEnum::class),

                        Forms\Components\Select::make('employment_status')
                            ->required()
                            ->options(StatusEnum::class),

                    ])


            ]);
    }


    

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Full Name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nric')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->sortable(),

                Tables\Columns\TextColumn::make('citizen'),
                Tables\Columns\TextColumn::make('marriage_status'),
                Tables\Columns\TextColumn::make('gender'),
                Tables\Columns\TextColumn::make('institute.name')
                    ->numeric()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('department.name')
                //     ->numeric()
                //     ->sortable(),
                    
                Tables\Columns\TextColumn::make('user.roles')
                    ->label('Roles')
                    ->formatStateUsing(function ($state, $record) {
                        return $record->user->roles->pluck('name')->join(', ');
                    }),



                Tables\Columns\TextColumn::make('user.status')
                    ->label('Account status'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        // Include trashed if user wants to see them
        if (request()->has('trashed')) {
            $query = $query->withTrashed(); // shows soft-deleted records
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AddressRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStaff::route('/'),
            'create' => Pages\CreateStaff::route('/create'),
            'edit' => Pages\EditStaff::route('/{record}/edit'),
            'import' => Pages\ImportStaffs::route('/import'),
            'view' => Pages\ViewStaff::route('/{record}'),
            'profile' => \App\Filament\Pages\StaffProfile::route('/{record}/profile'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes() // removes SoftDeletes global scope
            ->withTrashed();       // includes soft-deleted records
    }


    public static function getNavigationGroup(): ?string
    {
        return 'User Management';
    }
    public static function getNavigationSort(): ?int
    {
        return 0;
    }
}
