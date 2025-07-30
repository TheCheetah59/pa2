<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dish extends Model
{
    protected $fillable = [
        'menu_id',
        'name_fr',
        'name_en',
        'description_fr',
        'description_en',
        'price',
        'image_url',
        'available',
    ];

    // Un plat appartient à un menu
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    // Un plat peut apparaître dans plusieurs lignes de commande
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Accès multilingue automatique
    public function getTranslated($field, $lang = null)
    {
        $lang = $lang ?? app()->getLocale(); // ou request()->lang
        $fieldName = "{$field}_" . (in_array($lang, ['fr', 'en']) ? $lang : 'fr');

        return $this->{$fieldName} ?? $this->{$field . '_fr'};
    }
}

