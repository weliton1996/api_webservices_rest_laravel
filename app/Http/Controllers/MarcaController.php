<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    protected $marca;

    public function __construct(Marca $marca)
    {
        $this->marca = $marca;
    }
    /**
     * Exibir uma listagem do recurso.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $marcas = Marca::all();
        $marca = $this->marca->all();
        return response()->json($marca,200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        // $marca =  Marca::create($request->all());
        $regras = [
            'nome' => 'required|unique:marcas',
            'imagem' => 'required'
        ];
        $feedback = [
            'required' => 'O campo :attribute é obrigatório!',
            'nome.unique' => 'O nome da marca já existe'
        ];
        $request->validate($regras,$feedback);
        $marca =  $this->marca->create($request->all());
        return response()->json($marca,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $marca = $this->marca->find($id);
        if($marca === null){
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404);
        }
        return response()->json($marca,200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $marca = $this->marca->find($id);
        if($marca === null){
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe.'],404);
        }
        $marca->update($request->all());
        return response()->json($marca,200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $marca = $this->marca->find($id);
        if($marca === null){
            return response()->json(['erro' => 'Impossível realizar a exclusão. O recurso solicitado não existe.'],404);
        }
        $marca->delete();
        return response()->json(['msg' => 'A marca foi removida com sucesso!'],200);
    }
}
