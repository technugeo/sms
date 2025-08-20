<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;

use App\Models\Student;
use App\Models\Country;
use App\Models\StudentGuardian;

use App\Enum\CitizenEnum;
use App\Enum\MarriageEnum;
use App\Enum\AcademicEnum;
use App\Enum\NationalityEnum;
use App\Enum\GenderEnum;
use App\Enum\RaceEnum;
use App\Enum\ReligionEnum;
use App\Enum\IntakeEnum;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;

use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Navigation\NavigationItem;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Mail;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    /**
     * Controls sidebar menu visibility
     */
    public static function getNavigationItems(): array
    {
        $user = auth()->user();
        $items = [];

        // Admin/staff menu
        if ($user->can('view_any_student')) {
            $items[] = NavigationItem::make('Students')
                ->icon(static::$navigationIcon)
                ->group(static::getNavigationGroup())
                ->sort(static::getNavigationSort())
                ->url(static::getUrl('index'));
        }

        // Student menu
        if ($user->can('view_on_student_profile')) {
            $student = Student::where('email', $user->email)->first();

            if ($student) {
                $items[] = NavigationItem::make('My Profile')
                    ->icon('heroicon-o-user-circle')
                    ->url(StudentResource::getUrl('view', ['record' => $student->getKey()]))
                    ->group(static::getNavigationGroup())
                    ->sort(1);
            }
        }

        return $items;
    }




    public static function getNavigationGroup(): ?string
    {
        return 'User Management';
    }

    public static function getNavigationSort(): ?int
    {
        return 0;
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('current_course')
                    ->label('Course')
                    ->options(function () {
                        return \App\Models\Course::all()->mapWithKeys(fn($course) => [
                            $course->prog_code => "{$course->prog_code} - {$course->prog_name}"
                        ])->toArray();
                    })
                    ->required()
                    ->reactive()
                    ->columnSpanFull(),

                Forms\Components\Select::make('intake_month')
                    ->required()
                    ->options(IntakeEnum::class),

                Forms\Components\TextInput::make('intake_year')
                    ->required()
                    ->maxLength(4),

                Forms\Components\TextInput::make('full_name')->required(),

                Forms\Components\TextInput::make('nric')
                    ->maxLength(12),

                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->required()
                    ->afterStateHydrated(fn($component, $record, $state) => $record ? $component->state($record->email) : null)
                    ->dehydrateStateUsing(fn($state) => $state),

                Forms\Components\TextInput::make('phone_number')
                    ->tel()
                    ->required()
                    ->numeric(),

                Forms\Components\Select::make('nationality')
                    ->label('Nationality')
                    ->options(Country::pluck('name', 'name')->toArray())
                    ->required()
                    ->searchable(),

                Forms\Components\TextInput::make('passport_no'),

                Forms\Components\Select::make('gender')
                    ->required()
                    ->options(GenderEnum::class),

                Forms\Components\Select::make('marriage_status')
                    ->required()
                    ->options(MarriageEnum::class),

                Forms\Components\Select::make('race')
                    ->required()
                    ->options(RaceEnum::class),

                Forms\Components\Select::make('religion')
                    ->required()
                    ->options(ReligionEnum::class),

                Forms\Components\Select::make('citizen')
                    ->label('Bumiputera')
                    ->required()
                    ->options(CitizenEnum::class),

                Forms\Components\Select::make('nationality_type')
                    ->required()
                    ->options(NationalityEnum::class),

                Forms\Components\Select::make('academic_status')
                    ->required()
                    ->options(AcademicEnum::class),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('matric_id')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('full_name'),
                Tables\Columns\TextColumn::make('nric')->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->disabled(fn() => Filament::auth()->user()?->hasRole('student')),
                Tables\Columns\TextColumn::make('phone_number')->searchable(),
                Tables\Columns\TextColumn::make('passport_no')->searchable(),
                Tables\Columns\TextColumn::make('citizen'),
                Tables\Columns\TextColumn::make('marriage_status'),
                Tables\Columns\TextColumn::make('course')
                    ->label('Course')
                    ->formatStateUsing(fn($record) => $record->course ? $record->course->prog_code . ' - ' . $record->course->prog_name : null),
                Tables\Columns\TextColumn::make('user.status')->label('Account status'),
                Tables\Columns\TextColumn::make('academic_status'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('updateAcademicStatus')
                    ->label('Update Status')
                    ->icon('heroicon-m-adjustments-vertical')
                    ->form([
                        Forms\Components\Select::make('academic_status')
                            ->label('Academic Status')
                            ->options(AcademicEnum::class)
                            ->required(),
                    ])
                    ->action(fn(array $data, $record) => self::updateAcademicStatus($record, $data['academic_status']))
                    ->modalHeading('Update Academic Status')
                    ->modalButton('Update')
                    ->requiresConfirmation(false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static function updateAcademicStatus($record, $status)
    {
        $record->update(['academic_status' => $status]);

        if ($status === AcademicEnum::SUSPENDED->value && $record->user) {
            $record->user->update(['status' => 'Suspended']);
        }

        if ($status === AcademicEnum::REGISTERED->value && $record->user) {
            $user = $record->user;
            $link = url('/login');
            $tempPassword = \DB::table('password_reset_tokens')
                ->where('user_id', $user->id)
                ->where('is_active', 'yes')
                ->latest('created_at')
                ->value('password') ?? 'YourTempPassword123';

            try {
                Mail::html("
                    <p>Hello <strong>{$user->name}</strong>,</p>

                    <p>Thank you for registering with <strong>Food Institute of Malaysia</strong>.<br>
                    Your student account has been successfully created.</p>

                    <p><strong>Please find your login details below:</strong><br>
                    <strong>Student Name:</strong> {$user->name}<br>
                    <strong>User ID (Email):</strong> {$user->email}<br>
                    <strong>Temporary Password:</strong> {$tempPassword}<br>
                    
                    <p style=\"text-align: center;\">
                        <a href=\"{$link}\" 
                        style=\"display: inline-block; padding: 10px 20px; font-size: 16px; color: #ffffff; background-color: #007bff; text-decoration: none; border-radius: 5px;\">
                        Click here to Login
                        </a>
                    </p>

                    <p><strong>Important:</strong><br>
                    You will be required to update your password immediately after your first login.<br>
                    Do not share your login credentials with anyone.</p>

                    <p>Thank you,<br>
                    NuSmart Support Team</p>
                    ", function ($message) use ($user) {
                        $message->to($user->email)
                                ->subject('Student - Account Credentials');
                    });
            } catch (\Exception $e) {
                \Log::error("Failed to send registration email to {$user->email}: " . $e->getMessage());
            }
        }

        Notification::make()
            ->title('Academic status updated')
            ->success()
            ->body("{$record->full_name}'s academic status was changed successfully.")
            ->send();
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
            \App\Filament\Resources\StudentResource\RelationManagers\LocalAddressRelationManager::class,
            \App\Filament\Resources\StudentResource\RelationManagers\ForeignAddressRelationManager::class,
            \App\Filament\Resources\StudentResource\RelationManagers\GuardianRelationManager::class,
            \App\Filament\Resources\StudentResource\RelationManagers\EmergencyContactRelationManager::class,
            \App\Filament\Resources\StudentResource\RelationManagers\StudentEduhistoryRelationManager::class,
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
            'import' => Pages\ImportStudents::route('/import'),
            'view' => Pages\ViewStudent::route('/{record}'),
            'profile' => \App\Filament\Pages\StudentProfile::route('/{record}/profile'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->with('course')
            ->withTrashed();
    }
}
