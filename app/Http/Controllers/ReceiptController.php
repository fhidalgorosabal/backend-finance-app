<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\ResponseApi;

class ReceiptController extends Controller
{
    use ResponseApi;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $receipts = Receipt::join('concepts', 'receipts.concept_id', '=', 'concepts.id')
                        ->join('currencies', 'receipts.currency_id', '=', 'currencies.id')
                        ->select(
                            'receipts.id',
                            'receipts.amount',
                            'receipts.date',
                            'concepts.description as concept',
                            'currencies.initials as currency',
                            'receipts.actual_amount',
                        )->get();
        return $this->responseData($receipts, 'Listado de los comprobantes');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'date' => 'required|date',
                'concept_id' => 'required',
                'description' => 'nullable|string',
                'amount' => 'required|numeric',
                'currency_id' => 'required',
                'actual_amount' => 'required|numeric'
            ]);

            $receipt = Receipt::create([
                'date' => $validatedData['date'],
                'concept_id' => $validatedData['concept_id'],
                'description' => $validatedData['description'],
                'amount' => $validatedData['amount'],
                'currency_id' => $validatedData['currency_id'],
                'actual_amount' => $validatedData['actual_amount'],
            ]);

            if ($receipt) {
                return $this->responseData($receipt, 'Se ha creado el comprobante correctamente.', 201);
            }
        } catch (\Exception $e) {
            return $this->responseError($e, 'No se pudo crear el comprobante.');
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
        try {
            $validatedData = $request->validate([
                'date' => 'required|date',
                'concept_id' => 'required',
                'description' => 'nullable|string',
                'amount' => 'required|numeric',
                'currency_id' => 'required',
                'actual_amount' => 'required|numeric'
            ]);

            $receipt = Receipt::findOrFail($id);
            $updated = $receipt->update([
                'date' => $validatedData['date'],
                'concept_id' => $validatedData['concept_id'],
                'description' => $validatedData['description'],
                'amount' => $validatedData['amount'],
                'currency_id' => $validatedData['currency_id'],
                'actual_amount' => $validatedData['actual_amount'],
            ]);

            if ($updated) {
                return $this->responseData($receipt, 'Se ha actualizado el comprobante correctamente.');
            }
        } catch (\Exception $e) {
            return $this->responseError($e, 'No se pudo actualizar el comprobante.');
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
}
