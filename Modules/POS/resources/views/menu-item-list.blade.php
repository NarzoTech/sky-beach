@foreach ($menuItems as $menuItem)
    <div class="product_list_item cursor-pointer"
        @if ($menuItem->variants()->count() > 0) onclick="load_product_model({{ $menuItem->id }})" @else onclick="addMenuItemToCart({{ $menuItem->id }})" @endif>
        <div class="d-flex align-items-center">
            <div class="product_list_img">
                <img src="{{ $menuItem->image_url }}" alt="{{ $menuItem->name }}">
            </div>
            <div class="product_list_info">
                <h6>{{ $menuItem->name }}</h6>
                <p class="mb-0">{{ $menuItem->sku }}</p>
                <p class="mb-0 text-primary">{{ currency($menuItem->base_price) }}</p>
            </div>
        </div>
    </div>
@endforeach
