<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locacao extends Model
{
    use HasFactory;

    protected $table = 'locacoes';

    protected $fillable = [
        'cliente_id',
        'carro_id',
        'data_inicio_periodo',
        'data_final_previsto_periodo',
        'data_final_realizado_periodo',
        'valor_diaria',
        'km_inicial',
        'km_final'
    ];

    public function rules(){
        return [
            'cliente_id' => 'required|integer|exists:clientes,id',
            'carro_id' => 'required|integer|exists:carros,id',
            'data_inicio_periodo' => 'required|date',
            'data_final_previsto_periodo' => 'required|date|after:data_inicio_periodo',
            'data_final_realizado_periodo' => 'required|date|after:data_inicio_periodo',
            'valor_diaria' => 'required|integer|min:0',
            'km_inicial' => 'required|integer|min:0',
            'km_final' => 'required|integer|min:0|gt:km_inicial',
        ];
    }
    public function messages()
    {
        return [
            'cliente_id.required' => 'O campo Cliente é obrigatório.',
            'cliente_id.integer' => 'O campo Cliente deve ser um número inteiro.',
            'cliente_id.exists' => 'O Cliente selecionado não existe.',

            'carro_id.required' => 'O campo Carro é obrigatório.',
            'carro_id.integer' => 'O campo Carro deve ser um número inteiro.',
            'carro_id.exists' => 'O Carro selecionado não existe.',

            'data_inicio_periodo.required' => 'A data de início do período é obrigatória.',
            'data_inicio_periodo.date' => 'A data de início do período deve ser uma data válida.',

            'data_final_previsto_periodo.required' => 'A data final prevista é obrigatória.',
            'data_final_previsto_periodo.date' => 'A data final prevista deve ser uma data válida.',
            'data_final_previsto_periodo.after' => 'A data final prevista deve ser após a data de início do período.',

            'data_final_realizado_periodo.required' => 'A data final realizada é obrigatória.',
            'data_final_realizado_periodo.date' => 'A data final realizada deve ser uma data válida.',
            'data_final_realizado_periodo.after' => 'A data final realizada deve ser após a data de início do período.',

            'valor_diaria.required' => 'O valor da diária é obrigatório.',
            'valor_diaria.integer' => 'O valor da diária deve ser um número inteiro.',
            'valor_diaria.min' => 'O valor da diária não pode ser negativo.',

            'km_inicial.required' => 'O km inicial é obrigatório.',
            'km_inicial.integer' => 'O km inicial deve ser um número inteiro.',
            'km_inicial.min' => 'O km inicial não pode ser negativo.',

            'km_final.required' => 'O km final é obrigatório.',
            'km_final.integer' => 'O km final deve ser um número inteiro.',
            'km_final.min' => 'O km final não pode ser negativo.',
            'km_final.gt' => 'O km final deve ser maior que o km inicial.',
        ];
    }

    public function carro() {
        return $this->belongsTo('App\Models\Carro', 'carro_id');
    }

    public function cliente() {
        return $this->belongsTo('App\Models\Cliente', 'cliente_id');
    }

}
