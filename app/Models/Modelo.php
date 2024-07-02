<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    use HasFactory;
    protected $fillable = ['marca_id','nome', 'imagem','numero_portas', 'lugares', 'air_bag', 'abs'];

    public function rules(){
        return [
            'marca_id' => 'exists:marcas,id',
            'nome' => 'required|unique:modelos,nome,'.$this->id.'|min:3',
            'imagem' => 'required|file|mimes:png,jpeg,jpg',
            'numro_portas' => 'required|integer|digits_between:1,5',
            'lugares' => 'required|integer|digits_between:1,20',
            'air_bag' => 'required|boolean',
            'abs' => 'required|boolean',
        ];
    }

    public function messages() {
        return [
            'marca_id.exists' => 'A marca selecionada é inválida.',
            'nome.required' => 'O campo nome é obrigatório.',
            'nome.unique' => 'O nome já está em uso.',
            'nome.min' => 'O nome deve ter pelo menos 3 caracteres.',
            'imagem.required' => 'O campo imagem é obrigatório.',
            'imagem.file' => 'O campo imagem deve ser um arquivo.',
            'imagem.mimes' => 'A imagem deve ser dos tipos: png, jpeg, jpg.',
            'numro_portas.required' => 'O campo número de portas é obrigatório.',
            'numro_portas.integer' => 'O campo número de portas deve ser um número inteiro.',
            'numro_portas.digits_between' => 'O campo número de portas deve ter entre 1 e 5 dígitos.',
            'lugares.required' => 'O campo lugares é obrigatório.',
            'lugares.integer' => 'O campo lugares deve ser um número inteiro.',
            'lugares.digits_between' => 'O campo lugares deve ter entre 1 e 20 dígitos.',
            'air_bag.required' => 'O campo air bag é obrigatório.',
            'air_bag.boolean' => 'O campo air bag deve ser verdadeiro ou falso.',
            'abs.required' => 'O campo ABS é obrigatório.',
            'abs.boolean' => 'O campo ABS deve ser verdadeiro ou falso.',
        ];
    }
}
