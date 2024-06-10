<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public static function internalRules()
    {
        return [
            'livro_id' => ['required', 'integer'],
            'titulo' => ['required', 'string', 'max:255'],
            'pagina' => ['required', 'integer'],
            'indice_pai_id' => ['nullable', 'integer'],
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = self::internalRules();
        $rules['indice_pai_id'][] = Rule::exists('indices', 'livro_id');
        $rules['titulo'] = Rule::unique('livros', 'titulo');

        return $rules;
    }
}
