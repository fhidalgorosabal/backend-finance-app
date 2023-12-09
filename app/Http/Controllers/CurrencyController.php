<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\ResponseApi;

class CurrencyController extends Controller
{
    use ResponseApi;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currencies = Currency::get();
        return $this->responseData($currencies, 'Listado de las monedas');
    }

    /**
     * Display a listing of the resource for company.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        try {            
            $validatedData = $request->validate([          
                'company_id' => 'required'
            ]);

            $currencies = Currency::where('company_id', $validatedData['company_id'])->get();
            return $this->responseData($currencies, 'Listado de las monedas');

        } catch (\Exception $e) {
            return $this->responseError($e, 'No se pudo obtener el listado de monedas.');
        }
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
                'initials' => 'required|string|max:3|unique:currencies',
                'description' => 'required|string|max:100|unique:currencies',
                'exchange_rate' => 'required|numeric',              
                'company_id' => 'required',
                'is_default' => 'nullable|boolean'
            ]);

            $currency = Currency::create([
                'initials' => $validatedData['initials'],
                'description' => $validatedData['description'],
                'exchange_rate' => $validatedData['exchange_rate'],
                'company_id' => $validatedData['company_id'],
                'is_default' => $validatedData['is_default'],
                'active' => true
            ]);

            if ($currency) {
                return $this->responseData($currency, 'Se ha creado la moneda correctamente.', 201);
            }
        } catch (\Exception $e) {
            return $this->responseError($e, 'No se pudo crear la moneda.');
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
            $currency = Currency::findOrFail($id);
            return $this->responseData($currency, 'Detalles de la moneda: '.$id.'.');
        } catch (\Exception $e) {
            return $this->responseError($e, 'No se pudo obtener la moneda: '.$id.'.');
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
                'initials' => 'required|string|max:3|unique:currencies,initials,'.$id,
                'description' => 'required|string|max:100|unique:currencies,description,'.$id,
                'exchange_rate' => 'required|numeric',             
                'company_id' => 'required',
                'is_default' => 'nullable|boolean',
                'active' => 'nullable|boolean'
            ]);

            $currency = Currency::findOrFail($id);
            
            $updated = $currency->update([
                'initials' => $validatedData['initials'],
                'description' => $validatedData['description'],
                'exchange_rate' => $validatedData['exchange_rate'],
                'company_id' => $validatedData['company_id'],
                'is_default' => $validatedData['is_default'],
                'active' => $validatedData['active'],
            ]);

            if ($updated) {
                return $this->responseData($currency, 'Se ha actualizado la moneda correctamente.');
            }

        } catch (\Exception $e) {
            return $this->responseError($e, 'No se pudo actualizar la moneda.');
        }
    }

    /**
     * Display the default currency.
     *
     * @return \Illuminate\Http\Response
     */
    public function defaultCurrency(int $company_id)
    {
        try {
            $currency = Currency::where('is_default', true)->where('company_id', $company_id)->first();
            return $this->responseData($currency, 'Moneda predeterminada.');
        } catch (\Exception $e) {
            return $this->responseError($e, 'No se pudo obtener la moneda predeterminada.');
        }
    }

    /**
     * Update the default currency.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postDefaultCurrency(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id' => 'required|numeric',
            ]);

            $currency = Currency::findOrFail($validatedData['id']);
            Currency::clearDefaultCurrency();
            $currency->update(['is_default' => true, 'exchange_rate' => 1]);

            return $this->responseData($currency, 'Moneda predeterminada actualizada correctamente.');
        } catch (\Exception $e) {
            return $this->responseError($e, 'No se pudo actualizar la moneda predeterminada.');
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
            $currency = Currency::findOrFail($id);
            $currency->delete();
            return $this->responseData($currency, 'Se ha eliminado la moneda correctamente.');
        } catch (\Exception $e) {
            return $this->responseError($e, 'No se pudo eliminar la moneda: '.$id.'.');
        }
    }
}
