<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ModeloRepository {
    private Builder $model;

    public function __construct(Model $model) {
        $this->model = $model->newQuery();
    }

    public function selectAtributosRegistrosRelacionados($atributos) {
        $this->model->with($atributos);
    }

    public function filtro($filtros) {
        $filtros = explode(';',$filtros);
        foreach($filtros as $key => $condicao){
            $parametro = explode(':',$condicao);
            $this->model->where($parametro[0], $parametro[1], $parametro[2]);
        }
    }

    public function selectAtributoPesquisa($atributos) {
        $this->model->selectRaw($atributos);
    }

    public function getResultado() {
        return $this->model->get();
    }

}
