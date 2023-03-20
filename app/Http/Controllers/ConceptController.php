<?php

namespace App\Http\Controllers;

use App\Models\Concept;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\ResponseApi;
use App\Http\Controllers\Traits\Type;

class ConceptController extends Controller
{
    use ResponseApi;
    use Type;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $concepts = $this->getConcepts();
        return $this->responseData($concepts, 'Listado de los conceptos');
    }

    /**
     * Display a listing of the resource for type.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'type' => 'required|in:Expense,Ingress'
            ]);

            $concepts = $this->getConcepts($validatedData['type']);
            return $this->responseData($concepts, 'Listado de los conceptos de '.$this->getTypeName($validatedData['type']));

        } catch (\Exception $e) {
            return $this->responseError($e, 'No se pudo obtener el listado de conceptos.');
        }
    }

    /**
     *  Returns a listing of the resource.
     *
     * @param  string  $type  Receipt type or null. [ Expense,Ingress ]
     * @return \Illuminate\Http\Response
     */
    private function getConcepts($type = null)
    {
        if ($type) {
            return Concept::where('type', '=', $type)->get();
        } else {
            return Concept::get();
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
                'description' => 'required|string|max:255|unique:concepts',
                'type' => 'required|in:Expense,Ingress'
            ]);

            $concept = Concept::create([
                'description' => $validatedData['description'],
                'type' => $validatedData['type']
            ]);

            if ($concept) {
                return $this->responseData($concept, 'Se ha creado el concepto correctamente.', 201);
            }
        } catch (\Exception $e) {
            return $this->responseError($e, 'No se pudo crear el concepto.');
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
            $concept = Concept::findOrFail($id);
            return $this->responseData($concept, 'Detalles del concepto: '.$id.'.');
        } catch (\Exception $e) {
            return $this->responseError($e, 'No se pudo obtener el concepto: '.$id.'.');
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
                'description' => 'required|string|max:255|unique:concepts,description,'.$id,
                'type' => 'required|in:Expense,Ingress'
            ]);

            $concept = Concept::findOrFail($id);
            $updated = $concept->update([
                'description' => $validatedData['description'],
                'type' => $validatedData['type']
            ]);

            if ($updated) {
                return $this->responseData($concept, 'Se ha actualizado el concepto correctamente.');
            }

        } catch (\Exception $e) {
            return $this->responseError($e, 'No se pudo actualizar el concepto.');
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
            $concept = Concept::findOrFail($id);
            $concept->delete();
            return $this->responseData($concept, 'Se ha eliminado el concepto correctamente.');
        } catch (\Exception $e) {
            return $this->responseError($e, 'No se pudo eliminar el concepto: '.$id.'.');
        }
    }
}
