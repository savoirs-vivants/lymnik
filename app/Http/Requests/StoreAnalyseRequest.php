<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreAnalyseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'latitude'        => ['required', 'numeric', 'between:-90,90'],
            'longitude'       => ['required', 'numeric', 'between:-180,180'],
            'type'            => ['required', 'in:bandelette,photometre,les_deux'],
            'image'           => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'note'            => ['nullable', 'string', 'max:1000'],
            'cours_d_eau_id' => 'nullable|integer|exists:cours_d_eaus,id',
            'ville'          => ['nullable', 'string', 'max:100'],
            'redirect_to'    => ['nullable', 'string', 'max:255'],

            'mesures.bandelette.nitrates'      => ['nullable', 'numeric', 'min:0'],
            'mesures.bandelette.nitrites'      => ['nullable', 'numeric', 'min:0'],
            'mesures.bandelette.ph'            => ['nullable', 'numeric', 'between:0,14'],
            'mesures.bandelette.chlore'        => ['nullable', 'numeric', 'min:0'],
            'mesures.bandelette.durete_totale' => ['nullable', 'numeric', 'min:0'],
            'mesures.bandelette.durete_carb'   => ['nullable', 'numeric', 'min:0'],

            'mesures.photometre.phosphate' => ['nullable', 'numeric', 'min:0'],
            'mesures.photometre.nitrate'   => ['nullable', 'numeric', 'min:0'],
            'mesures.photometre.ammoniaque'  => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'latitude.required'       => 'La localisation GPS est requise.',
            'longitude.required'      => 'La localisation GPS est requise.',
            'latitude.between'        => 'Latitude invalide.',
            'longitude.between'       => 'Longitude invalide.',
            'type.required'           => 'Veuillez sélectionner un type d\'analyse.',
            'type.in'                 => 'Type d\'analyse invalide.',
            'image.image'             => 'Le fichier doit être une image.',
            'image.max'               => 'L\'image ne peut pas dépasser 5 Mo.',
        ];
    }
}
