<?php

namespace App\Http\Controllers;

use App\Models\Carro;
use Illuminate\Http\Request;
use App\Repositories\CarroRepository;

class CarroController extends Controller
{
    protected Carro $carro;
    protected CarroRepository $carroRepository;

    public function __construct(Carro $carro)
    {
        $this->carro = $carro;
        $this->carroRepository = new CarroRepository($carro);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->has('atributos_modelo')){
            $atributos_modelo = "modelo:id,$request->atributos_modelo";
            $this->carroRepository->selectAtributosRegistrosRelacionados($atributos_modelo);
        } else {
            $this->carroRepository->selectAtributosRegistrosRelacionados('modelo');
        }

        if($request->has('filtro')){
            $this->carroRepository->filtro($request->filtro);
        }

        if($request->has('atributos')){
            $this->carroRepository->selectAtributoPesquisa($request->atributo);
        }

        $carros = $this->carroRepository->getResultado();

        if($carros->isEmpty()){
            return response()->json(['erro' => 'Nenhum resultado encontrado!'], 404);
        }

        return response()->json($carros, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->carro->rules(),$this->carro->messages());

        $carro = $this->carro->create($request->all());

        return response()->json($carro,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $carro = $this->carro->with('modelo')->find($id);
        if($carro === null){
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404);
        }
        return response()->json($carro,200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $carro = $this->carro->find($id);
        if($carro === null){
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe.'],404);
        }

        if($request->method() === 'PATCH')
        {
            $regrasDinamicas = [];

            //percorrendo todas as regras definidas no Model
            foreach($carro->rules() as $input => $rules)
            {
                //coletando apenas as regras aplicáveil aos parametros parciais da requisição
                if(array_key_exists($input, $request->all()))
                {
                    $regrasDinamicas[$input] = $rules;
                }
            }
            $request->validate($regrasDinamicas, $carro->messages());

            $carro->fill($request->all());

            $carro->save();

            return response()->json($carro,200);

        } else {
            $request->validate($carro->rules(), $carro->messages());


            $carro->update($request->all());
        }

        return response()->json($carro,200);
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
        $carro = $this->carro->find($id);

        if($carro === null){
            return response()->json(['erro' => 'Impossível realizar a exclusão. O recurso solicitado não existe.'],404);
        }

        $carro->delete();
        return response()->json(['msg' => 'O carro foi removida com sucesso!'],200);
    }
}
