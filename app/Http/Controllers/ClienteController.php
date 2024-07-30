<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Repositories\ClienteRepository;

class ClienteController extends Controller
{
    protected Cliente $cliente;
    protected ClienteRepository $clienteRepository;

    public function __construct(Cliente $cliente)
    {
        $this->cliente = $cliente;
        $this->clienteRepository = new ClienteRepository($cliente);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->has('atributos_locacoes')){
            $atributos_locacoes = "locacoes:id,$request->atributos_locacoes";
            $this->clienteRepository->selectAtributosRegistrosRelacionados($atributos_locacoes);
        } else {
            $this->clienteRepository->selectAtributosRegistrosRelacionados('locacoes');
        }

        if($request->has('filtro')){
            $this->clienteRepository->filtro($request->filtro);
        }

        if($request->has('atributos')){
            $this->clienteRepository->selectAtributoPesquisa($request->atributos);
        }

        $cliente = $this->clienteRepository->getResultado();

        if($cliente->isEmpty()){
            return response()->json(['erro' => 'Nenhum resultado encontrado!'], 404);
        }

        return response()->json($cliente, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->cliente->rules(),$this->cliente->messages());

        $cliente = $this->cliente->create($request->all());

        return response()->json($cliente,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $cliente = $this->cliente->find($id);
        if($cliente === null){
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404);
        }
        return response()->json($cliente,200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $cliente = $this->cliente->find($id);
        if($cliente === null){
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe.'],404);
        }
        $request->validate($cliente->rules(), $cliente->messages());

        $cliente->update($request->all());

        return response()->json($cliente,200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cliente = $this->cliente->find($id);

        if($cliente === null){
            return response()->json(['erro' => 'Impossível realizar a exclusão. O recurso solicitado não existe.'],404);
        }

        $cliente->delete();
        return response()->json(['msg' => 'O cliente foi removida com sucesso!'],200);
    }
}
