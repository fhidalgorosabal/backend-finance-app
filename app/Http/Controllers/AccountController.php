<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\ResponseApi;

class AccountController extends Controller
{
    use ResponseApi;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accounts = Account::get();
        return $this->responseData($accounts, 'Listado de las cuentas');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'code' => 'required|string|max:20|unique:accounts',
                'description' => 'required|string|max:100|unique:accounts',
                'currency_id' => 'required'            
            ]);

            $account = Account::create([
                'code' => $validatedData['code'],
                'description' => $validatedData['description'],
                'currency_id' => $validatedData['currency_id'],
                'active' => true
            ]);

            if ($account) {
                return $this->responseData($account, 'Se ha creado la cuenta correctamente.', 201);
            }
        } catch (\Exception $e) {
            return $this->responseError($e, 'No se pudo crear la cuenta.');
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
            $account = Account::findOrFail($id);
            return $this->responseData($account, 'Detalles de la cuenta: '.$id.'.');
        } catch (\Exception $e) {
            return $this->responseError($e, 'No se pudo obtener la cuenta: '.$id.'.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAccountRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        try {
            $validatedData = $request->validate([
                'code' => 'required|string|max:20|unique:accounts,code,'.$id,
                'description' => 'required|string|max:100|unique:accounts,description,'.$id,
                'currency_id' => 'required',
                'active' => 'nullable|boolean'   
            ]);

            $account = Account::findOrFail($id);

            $updated = $account->update([
                'code' => $validatedData['code'],
                'description' => $validatedData['description'],
                'currency_id' => $validatedData['currency_id'],
                'active' => $validatedData['active'],
            ]);

            if ($updated) {
                return $this->responseData($account, 'Se ha actualizado la cuenta correctamente.');
            }

        } catch (\Exception $e) {
            return $this->responseError($e, 'No se pudo actualizar la cuenta.');
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
            $account = Account::findOrFail($id);
            $account->delete();
            return $this->responseData($account, 'Se ha eliminado la cuenta correctamente.');
        } catch (\Exception $e) {
            return $this->responseError($e, 'No se pudo eliminar la cuenta: '.$id.'.');
        }
    }
}
