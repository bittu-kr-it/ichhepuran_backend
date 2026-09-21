<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use BackedEnum;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

/**
 * A singleton settings page (not a Resource — there is only ever one
 * SiteSetting row) for everything global/site-wide: org info, contact
 * details, nav links, social links, and the donate button destination.
 * This is what finally replaces the old static site's footer/header
 * content that could only be changed by editing HTML.
 */
class ManageSiteSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Site Settings';
    protected static ?string $slug = 'site-settings';
    protected string $view = 'filament.pages.manage-site-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(SiteSetting::current()->toArray());
    }

    public function form(Schema $schema): Schema
    {
        // Bind the record on the schema every request (not just in mount) —
        // this is what core EditRecord does, and it's what makes the
        // SpatieMediaLibraryFileUpload both SHOW the existing file and SAVE
        // a new one. Binding only in mount()/save() left the field blank.
        return $schema
            ->model(SiteSetting::current())
            ->components([
            Section::make('Branding')
                ->schema([
                    Forms\Components\SpatieMediaLibraryFileUpload::make('logo')
                        ->collection('logo')
                        ->image()
                        ->maxSize(10240)
                        ->helperText('Upload the organization logo used in the header/footer. Max file size: 10 MB.'),
                    Forms\Components\TextInput::make('logo_alt')
                        ->label('Logo alt text')
                        ->helperText('Accessible description of the brand logo.'),
                    Forms\Components\TextInput::make('org_name')->required(),
                    Forms\Components\Textarea::make('tagline')->required()->rows(2),
                ]),

            Section::make('Contact')
                ->schema([
                    // Plain text, not ->tel() — it's displayed as-is on the
                    // site (not a tel: link), and the client lists more than
                    // one number, e.g. "+91 9147708511 | +91 8902339686",
                    // which the tel() format rule rejects.
                    Forms\Components\TextInput::make('phone')
                        ->required()
                        ->helperText('Shown in the footer and on the contact page. Separate multiple numbers with " | ".'),
                    Forms\Components\TextInput::make('email')->email()->required(),
                    Forms\Components\Textarea::make('address')->required()->rows(2),
                ]),

            Section::make('Navigation')
                ->schema([
                    Forms\Components\Repeater::make('nav_links')
                        ->schema([
                            Forms\Components\TextInput::make('label')->required(),
                            Forms\Components\TextInput::make('href')->required(),
                        ])
                        ->columns(2)
                        ->reorderable()
                        ->helperText('Controls the header + footer nav — add, remove, or reorder pages here without touching code.'),
                ]),

            Section::make('Social & Donate')
                ->schema([
                    Forms\Components\Repeater::make('social_links')
                        ->schema([
                            Forms\Components\TextInput::make('label')->required(),
                            Forms\Components\TextInput::make('href')->required()->url(),
                            Forms\Components\Select::make('icon')
                                ->label('Icon')
                                ->options(SiteSetting::SOCIAL_ICON_OPTIONS)
                                ->native(false)
                                ->required()
                                ->default('Website'),
                        ])
                        ->columns(3)
                        ->reorderable(),
                    Forms\Components\TextInput::make('donate_href')
                        ->required()
                        ->helperText('Where every "Donate Now" button on the site links to.'),
                ]),

            Section::make('Floating Buttons')
                ->description('Small round buttons that float over every page — bottom-right corner.')
                ->schema([
                    Forms\Components\Toggle::make('call_enabled')
                        ->label('Show floating Call button')
                        ->live()
                        ->default(false),
                    Forms\Components\TextInput::make('call_number')
                        ->label('Call number')
                        ->helperText('Include country code, e.g. +919147708511. Used as-is in the tel: link.')
                        ->visible(fn (Get $get) => $get('call_enabled'))
                        ->required(fn (Get $get) => $get('call_enabled')),

                    Forms\Components\Toggle::make('whatsapp_enabled')
                        ->label('Show floating WhatsApp button')
                        ->live()
                        ->default(false),
                    Forms\Components\TextInput::make('whatsapp_number')
                        ->label('WhatsApp number')
                        ->helperText('Include country code, digits only, e.g. 919147708511.')
                        ->visible(fn (Get $get) => $get('whatsapp_enabled'))
                        ->required(fn (Get $get) => $get('whatsapp_enabled')),
                    Forms\Components\TextInput::make('whatsapp_message')
                        ->label('Pre-filled message')
                        ->helperText('Optional. Shown as the starting text when a visitor opens the chat.')
                        ->visible(fn (Get $get) => $get('whatsapp_enabled')),

                    Forms\Components\Toggle::make('ebook_enabled')
                        ->label('Show floating "Download e-book" button')
                        ->live()
                        ->default(false),
                    Forms\Components\TextInput::make('ebook_label')
                        ->label('Button label')
                        ->helperText('Optional. Defaults to "Download e-book".')
                        ->visible(fn (Get $get) => $get('ebook_enabled')),
                    Forms\Components\SpatieMediaLibraryFileUpload::make('ebook')
                        ->collection('ebook')
                        ->acceptedFileTypes(['application/pdf'])
                        ->maxSize(204800)
                        ->helperText('Upload the e-book as a PDF. Max file size: 200 MB.')
                        ->visible(fn (Get $get) => $get('ebook_enabled')),
                ]),
        ])->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        unset($state['logo'], $state['ebook']);
        SiteSetting::current()->update($state);
        $this->form->saveRelationships();

        Notification::make()
            ->title('Site settings saved')
            ->success()
            ->send();
    }
}
