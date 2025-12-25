<?php

namespace Modules\Menu\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuCategoryTranslation extends Model
{

    protected $fillable = [
        'menu_category_id',
        'locale',
        'name',
        'description',
    ];

    public function menuCategory(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class);
    }
}
