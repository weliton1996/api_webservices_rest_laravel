<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carro extends Model
{
    use HasFactory;
    protected $fillable = ['modelo_id','placa', 'disponivel','km'];

    public function rules(){
        return [
            'modelo_id' => 'exists:modelos,id',
            'placa' => 'required|unique:carros,placa',
            'disponivel' => 'required|boolean',
            'km' => 'required|integer',
        ];
    }

    public function messages() {
        return [
            'modelo_id.exists' => 'O modelo selecionado não existe.',
            'placa.required' => 'A placa é obrigatória.',
            'placa.unique' => 'Esta placa já está cadastrada.',
            'disponivel.required' => 'A disponibilidade é obrigatória.',
            'disponivel.boolean' => 'O campo de disponibilidade deve ser verdadeiro ou falso.',
            'km.required' => 'O campo de quilometragem é obrigatório.',
            'km.integer' => 'O campo de quilometragem deve ser um número inteiro.',
        ];
    }

    public function marca() {
        return $this->belongsTo('App\Models\Marca');
    }

    public function modelo() {
        return $this->belongsTo('App\Models\Modelo');
    }
}
