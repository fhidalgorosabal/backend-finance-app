<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\ResponseApi;

class BankController extends Controller
{
    use ResponseApi;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $banks = Bank::get();
        return $this->responseData($banks, 'Listado de los bancos');
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
                'swift' => 'required|string|max:25',
                'bank_name' => 'required|string|max:100',
                'cis' => 'required|string|max:10|unique:banks',
                'branch_name' => 'nullable|string|max:100|unique:banks',
                'address' => 'nullable|string|max:200|unique:banks',
                'phone_number' => 'nullable|string|max:15|unique:banks',
                'email' => 'nullable|email|max:100|unique:banks'    
            ]);

            $bank = Bank::create([
                'swift' => $validatedData['swift'],
                'bank_name' => $validatedData['bank_name'],
                'cis' => $validatedData['cis'],
                'branch_name' => $validatedData['branch_name'],
                'address' => $validatedData['address'],
                'phone_number' => $validatedData['phone_number'],
                'email' => $validatedData['email'],
                'active' => true
            ]);

            if ($bank) {
                return $this->responseData($bank, 'Se ha creado el banco correctamente.', 201);
            }
        } catch (\Exception $e) {
            return $this->responseError($e, 'No se pudo crear el banco.');
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
            $bank = Bank::findOrFail($id);
            return $this->responseData($bank, 'Detalles del banco: '.$id.'.');
        } catch (\Exception $e) {
            return $this->responseError($e, 'No se pudo obtener el banco: '.$id.'.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        try {
            $validatedData = $request->validate([
                'swift' => 'required|string|max:25',
                'bank_name' => 'required|string|max:100',
                'cis' => 'required|string|max:10|unique:banks,cis,'.$id,
                'branch_name' => 'nullable|string|max:100|unique:banks,branch_name,'.$id,
                'address' => 'nullable|string|max:200|unique:banks,address,'.$id,
                'phone_number' => 'nullable|string|max:15|unique:banks,phone_number,'.$id,
                'email' => 'nullable|email|max:100|unique:banks,email,'.$id,   
                'active' => 'nullable|boolean'   
            ]);

            $bank = Bank::findOrFail($id);

            $updated = $bank->update([
                'swift' => $validatedData['swift'],
                'bank_name' => $validatedData['bank_name'],
                'cis' => $validatedData['cis'],
                'branch_name' => $validatedData['branch_name'],
                'address' => $validatedData['address'],
                'phone_number' => $validatedData['phone_number'],
                'email' => $validatedData['email'],
                'active' => $validatedData['active']
            ]);

            if ($updated) {
                return $this->responseData($bank, 'Se ha actualizado el banco correctamente.');
            }

        } catch (\Exception $e) {
            return $this->responseError($e, 'No se pudo actualizar el banco.');
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
            $bank = Bank::findOrFail($id);
            $bank->delete();
            return $this->responseData($bank, 'Se ha eliminado el banco correctamente.');
        } catch (\Exception $e) {
            return $this->responseError($e, 'No se pudo eliminar el banco: '.$id.'.');
        }
    }
}
