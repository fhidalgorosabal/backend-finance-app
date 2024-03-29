<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\ResponseApi;
use App\Http\Controllers\Traits\Type;
use App\Http\Utils\Utils;
use App\Models\Setting;
use App\Rules\ValidateSameMonth;
use App\Rules\ValidateSameYear;

class ReceiptController extends Controller
{

    const EXPENSE = 'Expense';
    const INGRESS = 'Ingress';

    use ResponseApi;
    use Type;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $receipts = $this->getReceipts();
        return $this->responseData($receipts, 'Listado de los comprobantes');
    }

    /**
     * Display a listing of the resource for type and company.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'type' => 'required|in:Expense,Ingress',                
                'company_id' => 'required'
            ]);
            $currentMonth = Setting::where('company_id', $validatedData['company_id'])->value('current_month');

            $receipts = $this->getReceipts($validatedData['company_id'], $validatedData['type'], $currentMonth);
            return $this->responseData($receipts, 'Listado de los comprobantes de '.$this->getTypeName($validatedData['type']));

        } catch (\Exception $e) {
            return $this->responseError($e, 'No se pudo obtener el listado de comprobantes.');
        }
    }

    /**
     *  Returns a listing of the resource.
     *
     * @param  string  $companyId  Company Id or null. 
     * @param  string  $type  Receipt type or null. [ Expense,Ingress ]
     * @return \Illuminate\Http\Response
     */
    private function getReceipts($companyId = null, $type = null, $currentMonth = null)
    {

        $result = Receipt::join('concepts', 'receipts.concept_id', '=', 'concepts.id')
            ->join('currencies', 'receipts.currency_id', '=', 'currencies.id')
            ->join('accounts', 'receipts.account_id', '=', 'accounts.id')
            ->select([
                'receipts.id',
                'receipts.amount',
                'receipts.date',
                'concepts.description as concept',
                'concepts.type as type',
                'currencies.initials as currency',
                'accounts.description as account',
                'receipts.actual_amount',
            ]);
            
        if ($type) {
            $result->where('concepts.type', $type);
        }
        if ($currentMonth) {
            $result->whereMonth('receipts.date', $currentMonth);
        }
        if ($companyId) {
            $result->where('receipts.company_id', $companyId);
        }
        return $result->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $ERROR_MESSAGE = 'No se pudo crear el comprobante.';

        try {
            $validatedData = $request->validate([
                'date' => ['required', 'date', 'date_format:Y/m/d', new ValidateSameMonth, new ValidateSameYear],
                'concept_id' => 'required',
                'description' => 'nullable|string|max:150',
                'amount' => 'required|numeric',
                'currency_id' => 'required',
                'account_id' => 'required',              
                'company_id' => 'required'
            ]);

            $actualAmount = $this->calculateActualAmount($validatedData);

            if (!$this->isAmountValid($validatedData, $actualAmount)) {
                return $this->getError($this->newMessageBag('El saldo de la cuenta no es suficiente'), $ERROR_MESSAGE);
            } else if(!$this->validAccountAndCurency($validatedData)) {
                return $this->getError($this->newMessageBag('Esta cuenta no es del tipo de moneda seleccionada'), $ERROR_MESSAGE);
            } else {
                $receipt = Receipt::create([
                    'date' => $validatedData['date'],
                    'concept_id' => $validatedData['concept_id'],
                    'description' => $validatedData['description'],
                    'amount' => doubleval($validatedData['amount']),
                    'currency_id' => $validatedData['currency_id'],
                    'account_id' => $validatedData['account_id'],
                    'company_id' => $validatedData['company_id'],
                    'actual_amount' => doubleval($actualAmount),
                ]);

                if ($receipt) {
                    return $this->responseData($receipt, 'Se ha creado el comprobante correctamente.', 201);
                } else {
                    return $this->getError($this->newMessageBag('Hubo un error al crear el comprobante, inténtelo de nuevo.'), $ERROR_MESSAGE);
                }

            }
        } catch (\Exception $e) {
            return $this->responseError($e, $ERROR_MESSAGE );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        try {
            $receipt = Receipt::findOrFail($id);
            return $this->responseData($receipt, 'Detalles del comprobante: '.$id.'.');
        } catch (\Exception $e) {
            return $this->responseError($e, 'No se pudo obtener el comprobante: '.$id.'.');
        }
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $ERROR_MESSAGE = 'No se pudo actualizar el comprobante.';

        try {
            $validatedData = $request->validate([
                'date' => ['required', 'date', 'date_format:Y/m/d', new ValidateSameMonth, new ValidateSameYear],
                'concept_id' => 'required',
                'description' => 'nullable|string|max:150',
                'amount' => 'required|numeric',
                'currency_id' => 'required',
                'account_id' => 'required',              
                'company_id' => 'required'
            ]);

            $receipt = Receipt::findOrFail($id);
            
            $actualAmount = $this->calculateActualAmount($validatedData);

            if (!$this->isAmountValid($validatedData, $actualAmount)) {
                return $this->getError($this->newMessageBag('El saldo de la cuenta no es suficiente'), $ERROR_MESSAGE);
            } else if(!$this->validAccountAndCurency($validatedData)) {
                return $this->getError($this->newMessageBag('Esta cuenta no es del tipo de moneda seleccionada'), $ERROR_MESSAGE);
            } else {    
                $updated = $receipt->update([
                    'date' => $validatedData['date'],
                    'concept_id' => $validatedData['concept_id'],
                    'description' => $validatedData['description'],
                    'amount' => doubleval($validatedData['amount']),
                    'currency_id' => $validatedData['currency_id'],
                    'account_id' => $validatedData['account_id'],
                    'company_id' => $validatedData['company_id'],
                    'actual_amount' => doubleval($actualAmount),
                ]);

                if ($updated) {
                    return $this->responseData($receipt, 'Se ha actualizado el comprobante correctamente.');
                } else {
                    return $this->getError($this->newMessageBag('Hubo un error al actualizar el comprobante, inténtelo de nuevo.'), $ERROR_MESSAGE);
                }
            } 
        } catch (\Exception $e) {
            return $this->responseError($e, $ERROR_MESSAGE);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        try {
            $receipt = Receipt::findOrFail($id);
            $receipt->delete();
            return $this->responseData($receipt, 'Se ha eliminado el comprobante correctamente.');
        } catch (\Exception $e) {
            return $this->responseError($e, 'No se pudo eliminar el comprobante: '.$id.'.');
        }
    }

    /**
     * Checks if the amount is valid.
     *
     * @param  array  $data
     * @param  float  $actualAmount
     * @return bool
     */
    private function isAmountValid($data, $actualAmount)
    {
        $conceptType = Utils::conceptType($data['concept_id']);
    
        if ($conceptType === ReceiptController::EXPENSE) {
            $ingress = Utils::totalAmountOfAccount($data['account_id'], ReceiptController::INGRESS);
            $expense = Utils::totalAmountOfAccount($data['account_id'], ReceiptController::EXPENSE);
            return ($actualAmount <= ($ingress - $expense));
        }
    
        return true;
    }
    
    /**
     * Calculates the actual amount.
     *
     * @param  array  $data
     * @return float
     */
    private function calculateActualAmount($data)
    {
        return Utils::getExchangeRate($data['currency_id']) * $data['amount'];
    }

    /** 
     * Return if account is of the same type of the currency
     * 
     * @param   array  $data
     * @return  boolean
     * */ 
    private function validAccountAndCurency($data) {
        return Utils::accountOfTypeCurrency($data['account_id'], $data['currency_id']) !== 0;        
    }
    
}
