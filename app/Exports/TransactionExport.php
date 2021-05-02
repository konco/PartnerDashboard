<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;
use Helper;
use DB;

class TransactionExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
	protected $transactions = '';
	protected $data;

	public function __construct($data)
    {
        $this->data = $data;
        $this->transactions = Helper::transaction_table('transactions');
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
    	$request = $this->data;
    	$input_from_date = empty($request->fromDate) ? $request->exportFromDate : $request->fromDate;
        $input_to_date = empty($request->toDate) ? $request->exportEndDate : $request->toDate;
        $input_search = empty($request->search) ? $request->exportSearch : $request->search;
        $input_status = empty($request->status) ? $request->exportStatus : $request->status;

        if (!empty($input_from_date) && !empty($input_to_date)) {
        	$from = Carbon::createFromFormat('m/d/Y', $input_from_date)->startOfDay();
        	$to = Carbon::createFromFormat('m/d/Y', $input_to_date)->endOfDay();
        }else{
        	$from = Carbon::createFromFormat('m/d/Y', date('m/d/Y'))->startOfDay();
        	$to = Carbon::createFromFormat('m/d/Y', date('m/d/Y'))->endOfDay();
        }

        $query = DB::table($this->transactions.' as trx')
        	->whereBetween('trx.created_at', [$from, $to])
        	->where(function ($query_inside) use ($request) {
                if(!empty($request->exportSearch)){
                    $query_inside->where('trx.trx_no', 'like', '%' . $request->exportSearch . '%');
                    $query_inside->orWhere('trx.reference_no', 'like', '%' . $request->exportSearch . '%');
                    $query_inside->orWhere('trx.customer_data', 'like', '%' . $request->exportSearch . '%');
                }

                if (!empty($request->exportStatus)) {
                    $query_inside->where('trx.status', $request->exportStatus);
                }
            })
        	->orderBy('trx.id', 'DESC');

        $data = $query->get();

		return $data;
    }

    public function map($transaction): array
    {
        return [
            $transaction->created_at,
            $transaction->trx_no,
            $transaction->partner_trx_no,
            $transaction->status,
            $transaction->sku_code,
            $transaction->brand_name,
            $transaction->product_name,
            number_format($transaction->price, 2, ',', '.'),
            $transaction->reference_no,
        ];
    }

    public function headings(): array
    {
        return [
        	'Date',
            'Trx_no',
            'Partner_Trx_no',
            'Status',
            'SKU',
            'Brand Name',
            'Product Name',
            'Price',
            'Ref_no',
        ];
    }

    public function styles(Worksheet $sheet)
	{
	    return [
	       // Style the first row as bold text.
	       1    => ['font' => ['bold' => true]],
	    ];
	}
}
