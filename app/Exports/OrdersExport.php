<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromCollection, WithHeadings
{
    protected $start_at;
    protected $end_at;

    function __construct($start_at,$end_at) {
            $this->start_at = $start_at;
            $this->end_at = $end_at;
    }
    
    public function collection()
    {
        $data = DB::table('orders_products')
            ->whereBetween('created_at', [$this->start_at,$this->end_at])
            ->get();

        return $data;
    }
    public function headings(): array
    {
        return [
            'product_id',
            'order_id'
            // Add more columns as needed
        ];
    }
    
    
}
