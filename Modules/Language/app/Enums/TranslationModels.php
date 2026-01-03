<?php

namespace Modules\Language\app\Enums;

enum TranslationModels: string
{
    /**
     * whenever update new case also update getAll() method
     * to return all values in array
     */
    case Blog = "Modules\Blog\app\Models\BlogTranslation";
    case BlogCategory = "Modules\Blog\app\Models\BlogCategoryTranslation";
    case MenuBuilder = "Modules\MenuBuilder\app\Models\MenuTranslation";
    case Testimonial = "Modules\Testimonial\app\Models\TestimonialTranslation";
    case Faq = "Modules\Faq\app\Models\FaqTranslation";
    case IngredientCategory = "Modules\Ingredient\app\Models\IngredientCategoryTranslation";
    case IngredientBrand = "Modules\Ingredient\app\Models\IngredientBrandTranslation";
    case Ingredient = "Modules\Ingredient\app\Models\IngredientTranslation";

    public static function getAll(): array
    {
        return [
            self::Blog->value,
            self::BlogCategory->value,
            self::MenuBuilder->value,
            self::Testimonial->value,
            self::Faq->value,
            self::IngredientCategory->value,
            self::IngredientBrand->value,
            self::Ingredient->value,
        ];
    }

    public static function igonreColumns(): array
    {
        return [
            'id',
            'lang_code',
            'created_at',
            'updated_at',
            'deleted_at',
        ];
    }
}
