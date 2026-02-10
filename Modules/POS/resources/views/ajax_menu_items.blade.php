<div class="pos_product_area">
    <div class="row">
        @foreach ($menuItems as $menuItem)
            <div class="col-6 col-md-4 col-lg-4">
                <div class="card produt_card cursor-pointer"
                    @if ($menuItem->variants()->count() > 0) onclick="load_product_model({{ $menuItem->id }})" @else onclick="addMenuItemToCart({{ $menuItem->id }})" @endif>
                    <div class="w-100 produt_card_img">
                        <img src="{{ $menuItem->image_url }}" class="card-img-top" alt="{{ $menuItem->name }}">
                        @if ($menuItem->is_featured)
                            <span class="badge bg-warning position-absolute" style="top: 5px; left: 5px;">
                                <i class="fas fa-star"></i>
                            </span>
                        @endif
                        @if ($menuItem->is_spicy)
                            <span class="badge bg-danger position-absolute" style="top: 5px; right: 5px;">
                                <i class="fas fa-pepper-hot"></i>
                            </span>
                        @endif
                    </div>
                    <div class="card-body">
                        <p class="card-title">
                            {{ $menuItem->name }}
                            @if ($menuItem->is_vegetarian)
                                <i class="fas fa-leaf text-success" title="Vegetarian"></i>
                            @endif
                        </p>
                        <p class="price">
                            @if($menuItem->discount_price && $menuItem->discount_price < $menuItem->base_price)
                                <small class="text-muted text-decoration-line-through">{{ currency($menuItem->base_price) }}</small>
                                {{ currency($menuItem->final_price) }}
                            @else
                                {{ currency($menuItem->base_price) }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{ $menuItems->onEachSide(0)->links('pos::ajax_pagination') }}
