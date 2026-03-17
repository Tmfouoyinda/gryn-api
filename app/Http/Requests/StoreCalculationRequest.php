<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCalculationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Protégé par auth:sanctum au niveau des routes
    }

    public function rules(): array
    {
        return [
            'transport'                => ['required', 'array'],
            'transport.voiture'        => ['required', 'numeric', 'min:0', 'max:99999'],
            'transport.train'          => ['required', 'numeric', 'min:0', 'max:99999'],
            'transport.bus'            => ['required', 'numeric', 'min:0', 'max:99999'],
            'transport.avion'          => ['required', 'numeric', 'min:0', 'max:99999'],
            'transport.velo'           => ['required', 'numeric', 'min:0', 'max:99999'],
            'transport.moto'           => ['required', 'numeric', 'min:0', 'max:99999'],

            'alimentation'             => ['required', 'array'],
            'alimentation.regime'      => ['required', 'string', 'in:omnivore,flexitarien,vegetarien,vegetalien,pescetarien'],
            'alimentation.kg_viande'   => ['required', 'numeric', 'min:0', 'max:200'],
            'alimentation.kg_poulet'   => ['required', 'numeric', 'min:0', 'max:200'],
            'alimentation.kg_poisson'  => ['required', 'numeric', 'min:0', 'max:200'],

            'energie'                  => ['required', 'array'],
            'energie.electricite'      => ['required', 'numeric', 'min:0', 'max:99999'],
            'energie.gaz'              => ['required', 'numeric', 'min:0', 'max:99999'],
            'energie.renouvelable'     => ['required', 'boolean'],

            'consommation'             => ['required', 'array'],
            'consommation.niveau'      => ['required', 'string', 'in:tres_peu,peu,moyen,beaucoup'],
        ];
    }

    public function messages(): array
    {
        return [
            'alimentation.regime.in'    => 'Le régime alimentaire choisi n\'est pas valide.',
            'consommation.niveau.in'    => 'Le niveau de consommation choisi n\'est pas valide.',
            '*.*.numeric'               => 'La valeur doit être un nombre.',
            '*.*.min'                   => 'La valeur ne peut pas être négative.',
        ];
    }
}
