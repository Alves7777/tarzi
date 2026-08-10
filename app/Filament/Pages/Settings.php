<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Support\LoginAppearance;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * Application wide settings, as opposed to the per-user UI switcher.
 *
 * The login screen is seen by visitors who are not signed in, so its appearance
 * cannot live in the panel's customisation slide-over.
 */
class Settings extends Page
{
    use HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static ?int $navigationSort = 6;

    protected string $view = 'filament.pages.settings';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('settings.title');
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('app.navigation.administration');
    }

    public function getTitle(): string
    {
        return __('settings.title');
    }

    public function getSubheading(): ?string
    {
        return __('settings.subheading');
    }

    public function mount(): void
    {
        $this->form->fill(LoginAppearance::all());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make(__('settings.login.heading'))
                    ->description(__('settings.login.description'))
                    ->icon(Heroicon::OutlinedRectangleGroup)
                    ->schema([
                        ViewField::make(LoginAppearance::LAYOUT)
                            ->label(__('settings.login.layout.label'))
                            ->helperText(__('settings.login.layout.helper'))
                            ->view('filament.pages.settings.login-layout')
                            ->live(),
                        ViewField::make(LoginAppearance::COLOR)
                            ->label(__('settings.login.color.label'))
                            ->helperText(__('settings.login.color.helper'))
                            ->view('filament.pages.settings.login-color')
                            ->live(),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make($this->getFormActions())
                        ->key('form-actions'),
                ]),
        ]);
    }

    public function save(): void
    {
        LoginAppearance::fill($this->form->getState());

        Notification::make()
            ->title(__('settings.notifications.saved'))
            ->success()
            ->send();
    }

    /**
     * Restore the form to the configured defaults. Nothing is stored until save.
     */
    public function resetToDefaults(): void
    {
        $this->form->fill([
            LoginAppearance::LAYOUT => LoginAppearance::default(LoginAppearance::LAYOUT),
            LoginAppearance::COLOR => LoginAppearance::default(LoginAppearance::COLOR),
        ]);
    }

    /**
     * @return array<Action>
     */
    public function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('settings.actions.save'))
                ->submit('save'),
            Action::make('reset')
                ->label(__('settings.actions.reset'))
                ->color('gray')
                ->action('resetToDefaults'),
        ];
    }
}
