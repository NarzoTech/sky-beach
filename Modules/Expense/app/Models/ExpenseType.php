<?php
namespace Modules\Expense\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'parent_id'];

    /**
     * Get the parent expense type
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ExpenseType::class, 'parent_id');
    }

    /**
     * Get all child expense types
     */
    public function children(): HasMany
    {
        return $this->hasMany(ExpenseType::class, 'parent_id');
    }

    /**
     * Check if this is a parent type (has no parent)
     */
    public function isParent(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Check if this is a child type (has a parent)
     */
    public function isChild(): bool
    {
        return ! is_null($this->parent_id);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
