<?php

namespace App\Http\Controllers;

use App\Models\Locacao;
use Illuminate\Http\Request;
use App\Repositories\LocacaoRepository;

class LocacaoController extends Controller
{
    protected Locacao $locacao;
    protected LocacaoRepository $locacaoRepository;

    public function __construct(Locacao $locacao)
    {
        $this->locacao = $locacao;
        $this->locacaoRepository = new LocacaoRepository($locacao);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $atributosRelacionados = [];

        // Verifica se há atributos para 'carro' e 'cliente'
        if ($request->has('atributos_carro')) {
            $atributos_carro = 'carro:id,' . $request->atributos_carro;
            $atributosRelacionados[] = $atributos_carro;
        } else {
            $atributosRelacionados[] = 'carro';
        }

        if ($request->has('atributos_cliente')) {
            $atributos_cliente = 'cliente:id,' . $request->atributos_cliente;
            $atributosRelacionados[] = $atributos_cliente;
        } else {
            $atributosRelacionados[] = 'cliente';
        }

        // Configura os atributos relacionados no repositório
        if(isset($atributosRelacionados)) {
            $this->locacaoRepository->selectAtributosRegistrosRelacionados($atributosRelacionados);
        } else {
            $this->locacaoRepository->selectAtributosRegistrosRelacionados(['cliente','carro']);
        }

        if($request->has('filtro')){
            $this->locacaoRepository->filtro($request->filtro);
        }

        if($request->has('atributos')){
            $this->locacaoRepository->selectAtributoPesquisa($request->atributos);
        }

        $locacao = $this->locacaoRepository->getResultado();

        if($locacao->isEmpty()){
            return response()->json(['erro' => 'Nenhum resultado encontrado!'], 404);
        }

        return response()->json($locacao, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->locacao->rules(),$this->locacao->messages());

        $locacao = $this->locacao->create($request->all());

        return response()->json($locacao,201);
    }

     /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $locacao = $this->locacao->with(['clientes','carros'])->find($id);
        if($locacao === null){
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404);
        }
        return response()->json($locacao,200);
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
        $locacao = $this->locacao->find($id);
        if($locacao === null){
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe.'],404);
        }

        if($request->method() === 'PATCH')
        {
            $regrasDinamicas = [];

            //percorrendo todas as regras definidas no Model
            foreach($locacao->rules() as $input => $rules)
            {
                //coletando apenas as regras aplicáveil aos parametros parciais da requisição
                if(array_key_exists($input, $request->all()))
                {
                    $regrasDinamicas[$input] = $rules;
                }
            }
            $request->validate($regrasDinamicas, $locacao->messages());

            $locacao->fill($request->all());

            $locacao->save();

            return response()->json($locacao,200);

        } else {
            $request->validate($locacao->rules(), $locacao->messages());

            $locacao->update($request->all());
        }

        return response()->json($locacao,200);
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
        $locacao = $this->locacao->find($id);

        if($locacao === null){
            return response()->json(['erro' => 'Impossível realizar a exclusão. O recurso solicitado não existe.'],404);
        }

        $locacao->delete();
        return response()->json(['msg' => 'A locacao foi removida com sucesso!'],200);
    }
}
