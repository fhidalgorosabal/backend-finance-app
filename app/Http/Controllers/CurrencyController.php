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
                'is_default' => 'nullable|boolean'
            ]);

            $currency = Currency::create([
                'initials' => $validatedData['initials'],
                'description' => $validatedData['description'],
                'exchange_rate' => $validatedData['exchange_rate'],
                'is_default' => $validatedData['is_default'],
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
                'initials' => 'required|string|max:255|unique:currencies,initials,'.$id,
                'description' => 'required|string|max:100|unique:currencies,description,'.$id,
                'exchange_rate' => 'required|numeric',
                'is_default' => 'nullable|boolean'
            ]);

            $currency = Currency::findOrFail($id);
            $updated = $currency->update([
                'initials' => $validatedData['initials'],
                'description' => $validatedData['description'],
                'exchange_rate' => $validatedData['exchange_rate'],
                'is_default' => $validatedData['is_default'],
            ]);

            if ($updated) {
                return $this->responseData($currency, 'Se ha actualizado la moneda correctamente.', 201);
            }

        } catch (\Exception $e) {
            return $this->responseError($e, 'No se pudo actualizar la moneda.');
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
