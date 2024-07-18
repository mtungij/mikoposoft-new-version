<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class LoanWithdrawal extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.loan-withdrawal';


    public function getTitle(): string|Htmlable
    {
        return '';
    }
}
