<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;

class CustomViewsController extends Controller
{
    public function loanDetails(Loan $loan)
    {
        return view("filament.pages.loan-request-detail");
    }
}
