<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Traits\ResponseApi;
use App\Http\Controllers\Traits\Type;
use Illuminate\Support\Facades\DB;
use App\Models\Receipt;
use App\Models\Concept;
use App\Models\Setting;

class DashboardController extends Controller
{
    use ResponseApi;
    use Type;

    /**
     * Get month total
     *  
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getMonthTotal(Request $request) {
        try {
            $validatedData = $request->validate([
                'type' => 'required|in:Expense,Ingress',
                'month' => 'required|numeric',
                'company_id' => 'required',
            ]);
    
            $totalActualAmount = $this->getTotalActualAmount($validatedData);
    
            return $this->responseData($totalActualAmount, 'Total de ' . $this->getTypeName($validatedData['type']));
        } catch (\Exception $e) {
            return $this->responseError($e, 'No se pudo obtener el total.');
        }
    }
    
    /**
     * Get total actual amount
     *  
     * @param   array  $data
     * @return  float
     */
    private function getTotalActualAmount($data) {    
        $type = $data['type'];
        $month = $data['month'];
        $companyId = $data['company_id'];
        return Receipt::whereHas('concept', function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->whereMonth('date', $month)
            ->where('company_id', $companyId)
            ->sum('actual_amount');
    }
    
    /**
     * Get month concepts
     *  
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getMonthConcepts(Request $request) {
        try {
            $validatedData = $request->validate([
                'type' => 'required|in:Expense,Ingress',
                'month' => 'required|numeric',
                'company_id' => 'required',
            ]);

            $result = Concept::select('concepts.id', 'concepts.description as concept_description', 'concepts.type', DB::raw('SUM(receipts.actual_amount) as total_amount'))
                ->join('receipts', 'concepts.id', '=', 'receipts.concept_id')
                ->where('concepts.type', $validatedData['type'])
                ->where('receipts.company_id', $validatedData['company_id'])
                ->whereMonth('receipts.date', '=', $validatedData['month'])
                ->groupBy('concepts.id', 'concepts.description', 'concepts.type')
                ->get();
            return $this->responseData($result, 'Conceptos mensuales de ' . $this->getTypeName($validatedData['type']));    
        } catch (\Exception $e) {
            return $this->responseError($e, 'No se lograron obtener los conceptos mensuales.');
        }        
    }

    /**
     * Get monthly ingress and expenses.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getIngressAndExpenseByMonth(Request $request)
    {
        try {
            $validatedData = $request->validate(['company_id' => 'required']);

            $settings = Setting::where('company_id', $validatedData['company_id'])->firstOrFail();
            $currentMonth = (int) $settings->current_month;
            $months = range(1, $currentMonth);
            $result = $this->getQueryResults($validatedData['company_id'], $months);
            $formattedResult = $this->formatResults($result);

            return $this->responseData($formattedResult, 'Totales mensuales de ingresos y gastos');
        } catch (\Exception $e) {
            return $this->responseError($e, 'No se lograron obtener los totales mensuales.');
        }
    }

    /**
     * Get totals by type and month.
     *
     * @param  int  $companyId
     * @param  array  $months
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getQueryResults($companyId, $months)
    {
        return Concept::select(
            DB::raw("CASE WHEN concepts.type = 'Ingress' THEN 'Ingress' ELSE 'Expense' END as type"),
            DB::raw('SUM(receipts.actual_amount) as total_amount'),
            DB::raw('EXTRACT(MONTH FROM receipts.date) as month')
        )
            ->leftJoin('receipts', 'concepts.id', '=', 'receipts.concept_id')
            ->where('receipts.company_id', $companyId)
            ->whereIn(DB::raw('EXTRACT(MONTH FROM receipts.date)'), $months)
            ->groupBy('type', 'month')
            ->get();
    }


    /**
     * Format results.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $result
     * @return array
     */
    private function formatResults($result)
    {
        $formattedResult = $result->groupBy('type')->map(function ($typeGroup) {
            $type = $typeGroup->first()->type;
            $allMonths = $typeGroup->pluck('month')->unique()->sort()->values()->toArray();
            $values = array_map(function ($month) use ($typeGroup) {
                $total = $typeGroup->where('month', $month)->first();
                return $total ? $total->total_amount : 0;
            }, $allMonths);
    
            return [
                'type' => $type,
                'values' => $values,
            ];
        })->values()->toArray();
    
        return $formattedResult;
    }
}
