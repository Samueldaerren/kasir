<?php

namespace App\Exports;

use App\Models\Order;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AdminOrdersExport implements FromCollection, WithHeadings, WithMapping
{
    private $fromDate;
    private $toDate;
    private $date;

    public function __construct(array $filters = [])
    {
        $this->fromDate = $filters['from_date'] ?? null;
        $this->toDate = $filters['to_date'] ?? null;
        $this->date = $filters['date'] ?? null;
    }

    public function collection()
    {
        $query = Order::with('customer');

        if ($this->fromDate && $this->toDate) {
            $query->whereDate('sale_date', '>=', Carbon::parse($this->fromDate))
                  ->whereDate('sale_date', '<=', Carbon::parse($this->toDate));
        } elseif ($this->fromDate) {
            $query->whereDate('sale_date', '>=', Carbon::parse($this->fromDate));
        } elseif ($this->toDate) {
            $query->whereDate('sale_date', '<=', Carbon::parse($this->toDate));
        } elseif ($this->date) {
            $query->whereDate('sale_date', Carbon::parse($this->date));
        }

        return $query->get();
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
