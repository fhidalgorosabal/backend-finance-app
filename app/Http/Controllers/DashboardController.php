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

    public function getMonthTotal(Request $request) {
        try {
            $validatedData = $request->validate([
                'type' => 'required|in:Expense,Ingress',
                'month' => 'required|numeric',
                'company_id' => 'required',
            ]);
    
            $type = $validatedData['type'];
            $month = $validatedData['month'];
    
            $totalActualAmount = $this->getTotalActualAmount($type, $month);
    
            return $this->responseData($totalActualAmount, 'Total de ' . $this->getTypeName($type));
        } catch (\Exception $e) {
            return $this->responseError($e, 'No se pudo obtener el total.');
        }
    }
    
    private function getTotalActualAmount($type, $month) {
        return Receipt::whereHas('concept', function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->whereMonth('date', $month)
            ->sum('actual_amount');
    }
}
