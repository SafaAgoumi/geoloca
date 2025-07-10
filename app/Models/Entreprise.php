<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entreprise extends Model
{
      use HasFactory;
      protected $fillable = [
        'nom_entreprise',
        'code_ice',
        'rc',
        'forme_juridique',
        'type',
        'taille_entreprise',
        'en_activite',
        'adresse',
        'ville',
        'latitude',
        'longitude',
        'secteur',
        'activite',
        'certifications',
        'email',
        'tel',
        'fax',
        'contact',
        'site_web',
        'cnss',
        'if',
        'patente'
    ];
}
