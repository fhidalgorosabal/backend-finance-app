<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Traits\ResponseApi;
use App\Http\Controllers\Traits\Type;
use App\Models\Receipt;

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
}
