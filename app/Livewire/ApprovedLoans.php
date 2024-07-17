<?php

namespace App\Livewire;

use App\Filament\Resources\LoanResource;
use App\Models\Loan;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ApprovedLoans extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static string $view = 'filament.pages.approved-loan-details';
    
    public function table(Table $table): Table
    {
        return $table
            ->query(Loan::query())
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with(['loanDetails'])->where([
                    ['company_id', auth()->user()->company_id],
                    ['status', 'approved']
                ]);
            })
            ->columns([
                TextColumn::make('customer.full_name')
                    ->searchable(),
                TextColumn::make('customer.phone')
                    ->label(__('Phone Number')),
                TextColumn::make('customer.branch.name')
                    ->label(__('Branch')),
                TextColumn::make('loanDetails.amount')
                    ->label('Loan Amount')
                    ->alignRight()
                    ->numeric(),
                TextColumn::make('loanDetails.duration')
                    ->label('Duration'),
                TextColumn::make('loanDetails.repayments')
                    ->label('Repayments'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (Loan $record) => match ($record->status) {
                        'pending' => 'warning',
                        'approved' => 'success',
                    }),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                ViewAction::make()
                   ->url(fn (Loan $record): string => LoanResource::getUrl('view', ['record' => $record])),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
            ]);
    }

    public function render()
    {
        return view('livewire.approved-loans');
    }
}
