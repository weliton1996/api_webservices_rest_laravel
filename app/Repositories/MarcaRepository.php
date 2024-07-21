<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class MarcaRepository {
    protected Model $model;

    public function __construct(Model $model) {
        $this->model = $model;
    }

    public function selectAtributosRegistrosRelacionados($atributos) {
        $this->model->with($atributos);
    }

    public function filtro($filtros) {
        $filtros = explode(';',$filtros);
        foreach($filtros as $key => $condicao){
            $parametros = explode(':',$condicao);
            $this->model->where($parametros[0], $parametros[1], $parametros[2]);
        }
    }

    public function selectAtributoPesquisa($atributos) {
        $this->model->selectRaw($atributos)->get();
    }

    public function get() {
        return $this->model->get();
    }

}
