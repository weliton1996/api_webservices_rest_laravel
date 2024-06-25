<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
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
        if($marca->isEmpty())return response()->json(['erro'=> 'Nenhum resultado encontrado!'],422);

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
       /* teste de validate tradicional
            dd($request->all());
            $marca =  Marca::create($request->all());
            $regras = [
                'nome' => 'required|unique:marcas',
                'imagem' => 'required'
            ];
            $feedback = [
                'required' => 'O campo :attribute é obrigatório!',
                'nome.unique' => 'O nome da marca já existe'
            ];
            $request->validate($regras,$feedback);
       */

        $request->validate($this->marca->rules(),$this->marca->feedback());
        /*  testes de request de image
            dd($request->nome);
            dd($request->get('nome'));
            dd($request->input('nome'));
            dd($request->imagem);
            dd($request->file('imagem')->getClientOriginalName());
        */

        $imagem = $request->file('imagem');
        // $imagem->store('path', local|public);
        $imagem_urn = $imagem->store('imagens','public');

        $marca = $this->marca->create([
            'nome' =>  $request->nome,
            'imagem' => $imagem_urn
        ]);

        return response()->json($marca,201);
    }

    /**
     * Display the specified resource.
     *
     * @param Integer $id
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
        // dd($request->nome);
        // dd($request->file('imagem'));
        if($marca === null){
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe.'],404);
        }

        if($request->method() === 'PATCH')
        {
            $regrasDinamicas = array();

            //percorrendo todas as regras definidas no Model
            foreach($marca->rules() as $input => $rules)
            {
                //coletando apenas as regras aplicáveil aos parametros parciais da requisição
                if(array_key_exists($input, $request->all()))
                {
                    $regrasDinamicas[$input] = $rules;
                }
            }
            $request->validate($regrasDinamicas, $marca->feedback());
        } else
        {
            $request->validate($marca->rules(), $marca->feedback());
        }

        //remove o arquivo antigo salvo no storage
        if($request->file('imagem')) {
            Storage::disk('public')->delete($marca->imagem);
        }

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens','public');

        $marca->update([
            'nome' =>  $request->nome,
            'imagem' => $imagem_urn
        ]);
        return response()->json($marca,200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param  Integer $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, int $id)
    {
        $marca = $this->marca->find($id);

        if($marca === null){
            return response()->json(['erro' => 'Impossível realizar a exclusão. O recurso solicitado não existe.'],404);
        }

        //remove o arquivo salvo no storage
        Storage::disk('public')->delete($marca->imagem);

        $marca->delete();
        return response()->json(['msg' => 'A marca foi removida com sucesso!'],200);
    }
}
