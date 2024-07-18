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
            <table class="w-full table-auto border border-collapsible dark:border-gray-700">
                <tbody>
                    <tr>
                        <th class="text-left p-4 border dark:border-gray-700">Name</th>
                        <td class="text-left p-4 border dark:border-gray-700">His Name</td>
                    </tr>
                    <tr>
                        <th class="text-left p-4 border dark:border-gray-700">Phone Number</th>
                        <td class="text-left p-4 border dark:border-gray-700">098765422</td>
                    </tr>
                    <tr>
                        <th class="text-left p-4 border dark:border-gray-700">Relationship</th>
                        <td class="text-left p-4 border dark:border-gray-700">Relalaj</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div>
             <div class="primary-bg-color text-white text-center">
                Photo
            </div>
            <div class="flex justify-end">
                <img src="{{asset('images/avatar-02.jpg')}}" alt="avatar1" class="max-w-full max-h-20">
            </div>
        </div>
        <div>
             <div class="primary-bg-color text-white text-center">
                Menu
            </div>
            <div class="p-4 space-y-3">
                <a href="" class="block text-center w-full secondary-bg-color p-1 rounded">Deposit</a>
                <a href="" class="block text-center w-full secondary-bg-color p-1 rounded">Withdraw</a>
                <a href="" class="block text-center w-full secondary-bg-color p-1 rounded">Payment Satatement</a>
                <a href="" class="block text-center w-full secondary-bg-color p-1 rounded">Penalt</a>
                {{ $this->createAction }}
            </div>
        </div>

    </div>

    <div>
         <div class="primary-bg-color text-white text-left px-4">
            ----
        </div>
        <div class="overflow-x-auto whitespace-nowrap">
            <table class="w-full table-auto border border-collapsible dark:border-gray-700">
                <thead>
                    <tr>
                        <th class="bg-th-color text-left">Phone Number</th>
                        <th class="bg-th-color text-left">Withdrawal Date</th>
                        <th class="bg-th-color text-left">End Date</th>
                        <th class="bg-th-color text-right">Loan Amount</th>
                        <th class="bg-th-color text-right">Collection</th>
                        <th class="bg-th-color text-right">Paid</th>
                        <th class="bg-th-color text-right">Debt</th>
                    </tr>
                </thead>
                <tbody class="">
                    <tr>
                        <td class="custom-td text-left">0677888898</td>
                        <td class="custom-td text-left">2024-03-01</td>
                        <td class="custom-td text-left">2024-03-01</td>
                        <td class="custom-td text-right">10,000</td>
                        <td class="custom-td text-right">10,000</td>
                        <td class="custom-td text-right">10,000</td>
                        <td class="custom-td text-right">10,000</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div>
         <div class="primary-bg-color text-white text-left px-4">
            Loan Satatement
        </div>
        <div class="overflow-x-auto whitespace-nowrap">
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
                    <tr>
                        <td class="custom-td text-left">2024-03-01</td>
                        <td class="custom-td text-left">Loan Disbursement</td>
                        <td class="custom-td text-right">-</td>
                        <td class="custom-td text-right">10,000</td>
                        <td class="custom-td text-right">10,000</td>
                    </tr>
                    <tr>
                        <td class="custom-td text-left">2024-03-15</td>
                        <td class="custom-td text-left">Repayment</td>
                        <td class="custom-td text-right">500</td>
                        <td class="custom-td text-right">-</td>
                        <td class="custom-td text-right">9,500</td>
                    </tr>
                    <tr>
                        <td class="custom-td text-left">2024-04-01</td>
                        <td class="custom-td text-left">Repayment</td>
                        <td class="custom-td text-right">500</td>
                        <td class="custom-td text-right">-</td>
                        <td class="custom-td text-right">9,000</td>
                    </tr>
                    <tr>
                        <td class="custom-td text-left">2024-04-15</td>
                        <td class="custom-td text-left">Late Fee</td>
                        <td class="custom-td text-right">-</td>
                        <td class="custom-td text-right">50</td>
                        <td class="custom-td text-right">9,050</td>
                    </tr>
                    <tr>
                        <td class="custom-td text-left">2024-05-01</td>
                        <td class="custom-td text-left">Repayment</td>
                        <td class="custom-td text-right">550</td>
                        <td class="custom-td text-right">-</td>
                        <td class="custom-td text-right">8,500</td>
                    </tr>
                    <tr>
                        <td class="custom-td text-left">2024-05-15</td>
                        <td class="custom-td text-left">Repayment</td>
                        <td class="custom-td text-right">500</td>
                        <td class="custom-td text-right">-</td>
                        <td class="custom-td text-right">8,000</td>
                    </tr>
                    <tr>
                        <td class="custom-td text-left">2024-06-01</td>
                        <td class="custom-td text-left">Repayment</td>
                        <td class="custom-td text-right">500</td>
                        <td class="custom-td text-right">-</td>
                        <td class="custom-td text-right">7,500</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

   <x-filament-actions::modals />
</div>