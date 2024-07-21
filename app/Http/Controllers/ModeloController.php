<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\Modelo;
use Illuminate\Http\Request;
use PHPUnit\Framework\Constraint\IsEmpty;

class ModeloController extends Controller
{
    /**
     *
     * @var Modelo $modelo
     */
    protected Modelo $modelo;
    public function __construct(Modelo $modelo)
    {
        $this->modelo = $modelo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $modelos = [];

        if($request->has('atributos_marca')){
            $atributos_marca = $request->atributos_marca;
            $modelos = $this->modelo->with('marca:id,'.$atributos_marca);
        } else {
            $modelos = $this->modelo->with('marca');
        }

        if($request->has('filtro')){
            $filtros = explode(';',$request->filtro);
            foreach($filtros as $key => $condicao){
                $parametros = explode(':',$condicao);
                $modelos = $modelos->where($parametros[0], $parametros[1], $parametros[2]);
            }

        }

        if($request->has('atributos')){
            $atributos = $request->atributos;
            $modelos = $modelos->selectRaw($atributos)->get();
        } else {
            $modelos = $modelos->get();
        }

        if(!$modelos->count()>0){
            return response()->json(['erro' => 'Nenhum resultado encontrado!'], 404);
        }
        return response()->json($modelos,200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->modelo->rules(),$this->modelo->messages());

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos','public');

        $modelo = $this->modelo->create([
            'marca_id' =>  $request->marca_id,
            'nome' =>  $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag'=> $request->air_bag,
            'abs'=> $request->abs,
        ]);

        return response()->json($modelo,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $modelo = $this->modelo->with('marca')->find($id);
        if($modelo === null){
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404);
        }
        return response()->json($modelo,200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $modelo = $this->modelo->find($id);

        if($modelo === null){
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe.'],404);
        }

        if($request->method() === 'PATCH')
        {
            $regrasDinamicas = [];

            //percorrendo todas as regras definidas no Model
            foreach($modelo->rules() as $input => $rules)
            {
                //coletando apenas as regras aplicável aos parametros parciais da requisição
                if(array_key_exists($input, $request->all()))
                {
                    $regrasDinamicas[$input] = $rules;
                }
            }
            $request->validate($regrasDinamicas, $modelo->messages());

            $modelo->fill($request->all());

            if($request->file('imagem')) {
                Storage::disk('public')->delete($modelo->imagem);

                $imagem = $request->file('imagem');
                $imagem_urn = $imagem->store('imagens/modelos','public');
                $modelo->imagem = $imagem_urn;
            }

            $modelo->save();

        } else {
            $request->validate($modelo->rules(), $modelo->messages());

            $modelo->update($request->all());

            if($request->file('imagem')) {
                Storage::disk('public')->delete($modelo->imagem);

                $imagem = $request->file('imagem');
                $imagem_urn = $imagem->store('imagens/modelos','public');
                $modelo->imagem = $imagem_urn;
            }
        }

        return response()->json($modelo,200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, int $id)
    {
       $modelo = $this->modelo->find($id);

        if($modelo === null){
            return response()->json(['erro' => 'Impossível realizar a exclusão. O recurso solicitado não existe.'],404);
        }

        //remove o arquivo salvo no storage
        Storage::disk('public')->delete($modelo->imagem);

        $modelo->delete();
        return response()->json(['msg' => 'A modelo foi removida com sucesso!'],200);
    }
}
