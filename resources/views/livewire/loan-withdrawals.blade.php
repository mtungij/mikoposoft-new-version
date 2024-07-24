<div>
    <div class="mb-3">
        {{ $this->form }}
    </div>
    <br>
    <div class="grid grid-cols-1 md:grid-cols-3">
        <div>
            <div class="primary-bg-color text-white text-center">
                Guarantors Information
            </div>
            <table class="w-full table-auto border border-collapsible dark:border-gray-700" wire:lazy>
                <tbody>
                    <tr>
                        <th class="text-left p-4 border dark:border-gray-700">Name</th>
                        <td class="text-left p-4 border dark:border-gray-700">{{ $loan?->guarantors->first()->name ?? "-" }}</td>
                    </tr>
                    <tr>
                        <th class="text-left p-4 border dark:border-gray-700">Phone Number</th>
                        <td class="text-left p-4 border dark:border-gray-700">{{ $loan?->guarantors->first()->phone ?? "-" }}</td>
                    </tr>
                    <tr>
                        <th class="text-left p-4 border dark:border-gray-700">Relationship</th>
                        <td class="text-left p-4 border dark:border-gray-700">{{ $loan?->guarantors->first()->relationship ?? "-" }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div>
            <div class="primary-bg-color text-white text-center">
                Photo
            </div>
            <div class="flex justify-end">
                @if ($loan?->customer?->img_url)
                <img src="{{asset('/storage/'. $loan?->customer?->img_url)}}" alt="avatar1" class="w-fit h-fit">
                @else
                <img src="{{asset('images/avatar-01.jpg')}}" alt="avatar1" class="w-fit max-h-20">
                @endif
            </div>
        </div>
        <div>
            <div class="primary-bg-color text-white text-center">
                Menu
            </div>
            <div class="p-4 space-y-3">
                <button wire.loading.attr="disabled" @disabled($customer_id == '') class="block text-center w-full secondary-bg-color p-1 rounded" wire:click="mountAction('deposit')">Deposit</button>
                <button wire.loading.attr="disabled" @disabled($customer_id == '' || $loan_end_date) class="block text-center w-full secondary-bg-color p-1 rounded" wire:click="mountAction('withdraw')">{{ $loan_end_date ? '-------': 'Withdraw'}}</button>
                <button wire.loading.attr="disabled" @disabled($customer_id == '') class="block text-center w-full secondary-bg-color p-1 rounded">Payment Satatement</button>
                <button wire.loading.attr="disabled" @disabled($customer_id == '') class="block text-center w-full secondary-bg-color p-1 rounded" wire:click="mountAction('my')">Penalt</button>
            </div>
        </div>
    </div>

    <div>
         <div class="primary-bg-color text-white text-left px-4">
            ----
        </div>
        <div class="overflow-x-auto whitespace-nowrap" wire:lazy>
            <table class="w-full table-auto border border-collapsible dark:border-gray-700">
                
                
            <th class="bg-th-color text-left">Phone Number</th>
                        <th class="bg-th-coor text-left">Withdrawal Date</th>
                        <th class="bg-th-color text-left">End Date</th>
                        <th class="bg-th-color text-right">Loan Amount</th>
                        <th class="bg-th-color text-right">Collection</th>
                        <th class="bg-th-color text-right">Paid</th>
                        <th class="bg-th-color text-right">Debt</th>
                    </tr>
                </thead>
                <tbody class="">
                    <tr>
                        <td class="custom-td text-left">{{ $loan?->customer?->phone ?? '--------' }}</td>
                        <td class="custom-td text-left">{{ $withdrawal?->date ?? 'YYYY-MM-DD'}}</td>
                        <td class="custom-td text-left">{{ $loan_end_date ?? 'YYY-MM-DD' }}</td>
                        <td class="custom-td text-right">{{ number_format($loan?->loanDetails()->first()->amount ?? 0) }}</td>
                        <td class="custom-td text-right">{{ number_format($collection ?? 0) }}</td>
                        <td class="custom-td text-right">{{ 0 }}</td>
                        <td class="custom-td text-right">{{ 0 }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div>
         <div class="primary-bg-color text-white text-left px-4">
            Loan Satatement
        </div>
        <div class="overflow-x-auto whitespace-nowrap" wire:lazy>
            <table class="w-full table-auto border border-collapsible dark:border-gray-700">
                <thead>
                    <tr>
                        <th class="bg-th-color text-left">Date</th>
                        <th class="bg-th-color text-left">Description</th>
                        <th class="bg-th-color text-right">Deposit</th>
                        <th class="bg-th-color text-right">Withdraw</th>
                        <th class="bg-th-color text-right">Balance</th>
                    </tr>
                </thead>
                <tbody>
                @php
                    $balance = $loan?->loanDetails()->first()->amount ?? 0;
                    $withdraw = 0;
                    $loanFeedesc = '';
                    $loanDetails = [];
                @endphp
                @foreach ($loan_fees as $loan_fee)
                    @php
                    switch ($loan_fee->fee_type) {
                        case 'money':
                            $balance -= $loan_fee->fee_amount;
                            $withdraw = $loan_fee->fee_amount;
                            $loanFeedesc =  $loan_fee->fee_amount;
                            break;
                        default:
                            $balance -= $loan?->loanDetails()?->first()?->amount * ($loan_fee?->fee_amount / 100);
                            $withdraw = $loan?->loanDetails()?->first()?->amount * ($loan_fee?->fee_amount / 100);
                            $loanFeedesc =  "{$loan_fee->fee_amount}%";
                            break;
                    }
                    $loanDetails[] = [
                        'date' => $loan?->updated_at ? date('Y-m-d', strtotime($loan?->updated_at)) : 'YYYY-MM-DD',
                        'description' => "System / {$loan_fee->desc} ({$loanFeedesc})",
                        'deposit' => number_format(0.00, 2),
                        'withdraw' => number_format($withdraw),
                        'balance' => number_format($balance)
                    ];
                    @endphp
                @endforeach

                @php
                usort($loanDetails, function ($a, $b) {
                    return $a['balance'] <=> $b['balance'];
                });
                $key =1;
                @endphp
                @if ($withdrawal)
                    <tr>
                        <td class="custom-td text-left">{{ date('Y-m-d', strtotime($withdrawal?->created_at)) }}</td>
                        <td class="custom-td text-left" style="text-transform: uppercase">{{ "{$withdrawal?->user->name}/ Cash withdraw /{$withdrawal?->transactionAccount?->name}" }}</td>
                        <td class="custom-td text-right">0.00</td>
                        <td class="custom-td text-right">{{ $withdrawal?->amount }}</td>
                        <td class="custom-td text-right">0.00 </td>
                    </tr>
                @endif
                @foreach ($loanDetails as $detail)
                    <tr key="{{ $key++ }}">
                        <td class="custom-td text-left">{{ $detail['date'] }}</td>
                        <td class="custom-td text-left" style="text-transform: uppercase">{{ $detail['description'] }}</td>
                        <td class="custom-td text-right">{{ $detail['deposit'] }}</td>
                        <td class="custom-td text-right">{{ $detail['withdraw'] }}</td>
                        <td class="custom-td text-right">{{ $detail['balance'] }}</td>
                    </tr>
                @endforeach
                   <tr>
                        <td class="custom-td text-left">{{ $loan?->updated_at ? date('Y-m-d', strtotime($loan?->updated_at)): 'YYYY-MM-DD' }}</td>
                        <td class="custom-td text-left" style="text-transform: uppercase">{{ $loan?->approvedBy?->name }} / {{ 'LOAN DEPOSIT'}} / {{ $loan?->loanDetails()?->first()->duration  }} {{ $loan?->loanDetails()?->first()->repayments }} </td>
                        <td class="custom-td text-right">{{ number_format($loan?->loanDetails()?->first()?->amount ?? 0.00) }}</td>
                        <td class="custom-td text-right">0.00</td>
                        <td class="custom-td text-right">{{ number_format($loan?->loanDetails()?->first()?->amount ?? 0.00) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

   <x-filament-actions::modals />
</div>