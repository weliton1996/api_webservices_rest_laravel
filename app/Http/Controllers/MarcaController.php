<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Marca;
use App\Repositories\MarcaRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarcaController extends Controller
{
    protected Marca $marca;
    protected MarcaRepository $marcaRepository;

    public function __construct(Marca $marca)
    {
        $this->marca = $marca;
        $this->marcaRepository = new MarcaRepository($marca);
    }
    /**
     * Exibir uma listagem do recurso.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->has('atributos_modelos')){
            $atributos_modelo = "modelos:id,$request->atributos_modelos";
            $this->marcaRepository->selectAtributosRegistrosRelacionados($atributos_modelo);
        } else {
            $this->marcaRepository->selectAtributosRegistrosRelacionados('modelos');
        }

        if($request->has('filtro')){
            $this->marcaRepository->filtro($request->filtro);
        }

        if($request->has('atributos')){
            $this->marcaRepository->selectAtributoPesquisa($request->atributos);
        }

        $marcas = $this->marcaRepository->getResultado();

        if($marcas->isEmpty()){
            return response()->json(['erro' => 'Nenhum resultado encontrado!'], 404);
        }

        return response()->json($marcas, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate($this->marca->rules(),$this->marca->messages());

        $imagem = $request->file('imagem');
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
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $marca = $this->marca->with('modelos')->find($id);
        if($marca === null){
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404);
        }
        return response()->json($marca,200);
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
        $marca = $this->marca->find($id);
        // dd($request->nome);
        // dd($request->file('imagem'));
        if($marca === null){
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe.'],404);
        }

        if($request->method() === 'PATCH')
        {
            $regrasDinamicas = [];

            //percorrendo todas as regras definidas no Model
            foreach($marca->rules() as $input => $rules)
            {
                //coletando apenas as regras aplicáveil aos parametros parciais da requisição
                if(array_key_exists($input, $request->all()))
                {
                    $regrasDinamicas[$input] = $rules;
                }
            }
            $request->validate($regrasDinamicas, $marca->messages());

            $marca->fill($request->all());

            if($request->file('imagem')) {
                Storage::disk('public')->delete($marca->imagem);
                $imagem = $request->file('imagem');
                $imagem_urn = $imagem->store('imagens','public');
                $marca->imagem = $imagem_urn;
            }

            $marca->save();

            return response()->json($marca,200);

        } else {
            $request->validate($marca->rules(), $marca->messages());

            if($request->file('imagem')) {
                Storage::disk('public')->delete($marca->imagem);
            }

            $imagem = $request->file('imagem');
            $imagem_urn = $imagem->store('imagens','public');

            $marca->update([
                'nome' =>  $request->nome,
                'imagem' => $imagem_urn
            ]);
        }

        return response()->json($marca,200);
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
