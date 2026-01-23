<?php

namespace Modules\CMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteSectionTranslation extends Model
{
    protected $fillable = [
        'site_section_id',
        'lang_code',
        'title',
        'subtitle',
        'description',
        'content',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    /**
     * Get the site section
     */
    public function siteSection(): BelongsTo
    {
        return $this->belongsTo(SiteSection::class);
    }
}
