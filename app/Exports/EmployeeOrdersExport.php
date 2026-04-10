<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EmployeeOrdersExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Order::with('customer')->where('employee_id', Auth::id())->get();
    }

    public function headings(): array
    {
        return [
            'Order ID',
            'Sale Date',
            'Customer Name',
            'Customer Phone',
            'Total Price',
            'Total Pay',
            'Total Return',
            'Points Used',
            'Points Earned',
        ];
    }

    private function formatZeroValue($value)
    {
        return $value === 0 ? '0' : $value;
    }

    public function map($order): array
    {
        return [
            $order->id,
            $order->sale_date,
            optional($order->customer)->name,
            optional($order->customer)->phone_number,
            $this->formatZeroValue($order->total_price),
            $this->formatZeroValue($order->total_pay),
            $this->formatZeroValue($order->total_return),
            $this->formatZeroValue($order->point_used),
            $this->formatZeroValue($order->point_earned),
        ];
    }
}
