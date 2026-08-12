<?php

namespace App\Filament\Resources\Advertisements\Tables;

use App\Domain\Ads\Enums\AdMediaType;
use App\Domain\Ads\Enums\AdvertisementStatus;
use App\Models\Advertisement;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class AdvertisementsTable
{
    public static function configure(Table $table, bool $forAdvertiser = false): Table
    {
        return $table
            ->columns([
                ImageColumn::make('preview')
                    ->label('Prévia')
                    ->getStateUsing(fn (Advertisement $record): ?string => $record->media_type === AdMediaType::Image
                        ? $record->mediaUrl()
                        : null)
                    ->checkFileExistence(false)
                    ->height(48),
                TextColumn::make('advertiser.name')
                    ->label('Anunciante')
                    ->searchable()
                    ->hidden($forAdvertiser),
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (AdvertisementStatus $state): string => $state->label())
                    ->color(fn (AdvertisementStatus $state): string => $state->color()),
                TextColumn::make('media_type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (AdMediaType $state): string => $state->label()),
                TextColumn::make('placements.placement')
                    ->label('Áreas')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->limitList(3),
                IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->hidden($forAdvertiser),
                TextColumn::make('updated_at')
                    ->label('Atualizado')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(AdvertisementStatus::class)
                    ->hidden($forAdvertiser),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (Advertisement $record): bool => $forAdvertiser
                        ? $record->status->isEditableByAdvertiser()
                        : true),
                Action::make('approve')
                    ->label(__('signage.admin.approve'))
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Advertisement $record): bool => ! $forAdvertiser && $record->status === AdvertisementStatus::Pending)
                    ->action(fn (Advertisement $record) => self::approve($record)),
                Action::make('reject')
                    ->label(__('signage.admin.reject'))
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->visible(fn (Advertisement $record): bool => ! $forAdvertiser && $record->status === AdvertisementStatus::Pending)
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label(__('signage.admin.reject_reason'))
                            ->required(),
                    ])
                    ->action(function (Advertisement $record, array $data): void {
                        $record->update([
                            'status' => AdvertisementStatus::Rejected,
                            'reviewed_at' => now(),
                            'reviewed_by' => Auth::id(),
                            'rejection_reason' => $data['rejection_reason'],
                        ]);

                        Notification::make()
                            ->title(__('signage.admin.rejected'))
                            ->danger()
                            ->send();
                    }),
                Action::make('submit')
                    ->label(__('signage.advertiser_panel.submit_for_review'))
                    ->icon(Heroicon::OutlinedPaperAirplane)
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalDescription(__('signage.advertiser_panel.submit_confirm'))
                    ->visible(fn (Advertisement $record): bool => $forAdvertiser && $record->status->isEditableByAdvertiser())
                    ->action(function (Advertisement $record): void {
                        $record->update([
                            'status' => AdvertisementStatus::Pending,
                            'submitted_at' => now(),
                            'rejection_reason' => null,
                        ]);

                        Notification::make()
                            ->title(__('signage.advertiser_panel.submitted'))
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function approve(Advertisement $record): void
    {
        $record->update([
            'status' => AdvertisementStatus::Approved,
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
            'rejection_reason' => null,
            'is_active' => true,
        ]);

        Notification::make()
            ->title(__('signage.admin.approved'))
            ->success()
            ->send();
    }
}
