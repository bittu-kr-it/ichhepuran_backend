<?php

namespace App\Filament\Resources\TeamMemberResource\Pages;

use App\Filament\Resources\TeamMemberResource;
use App\Filament\Widgets\SectionHeadingWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTeamMembers extends ListRecords
{
    protected static string $resource = TeamMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SectionHeadingWidget::make([
                'key' => 'team',
                'defaultEyebrow' => 'The people behind it',
                'defaultHeading' => 'Meet Our Team',
                'label' => 'About Page — "Meet Our Team" heading',
            ]),
            SectionHeadingWidget::make([
                'key' => 'advisory-board',
                'defaultEyebrow' => 'Guiding our mission',
                'defaultHeading' => 'Advisory Board',
                'label' => 'About Page — "Advisory Board" heading',
            ]),
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 2;
    }
}
