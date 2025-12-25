<style>
    .dropdown-item.qty-zero {
        background-color: #f8d7da;
        /* Red for zero quantity */
        color: #842029;
    }

    .dropdown-item.qty-low {
        background-color: #fff3cd;
        /* Yellow for low quantity */
        color: #664d03;
    }

    .dropdown-item.qty-available {
        background-color: #d1e7dd;
        /* Green for available quantity */
        color: #0f5132;
    }

    .dropdown-item span {
        float: right;
    }

    #itemList,
    #favoriteItemList {
        width: 100%;
    }
</style>

@if ($products->count() > 0)
    @foreach ($products as $product)
        <li>
            <a class="dropdown-item {{ $product->stock <= 0 ? 'qty-zero' : ($product->stock < ($product->stock_alert ?? 5) ? 'qty-low' : 'qty-available') }}"
                href="javascript:;"
                @if ($product->has_variant) onclick="load_product_model({{ $product->id }})" @else onclick="singleAddToCart({{ $product->id }})" @endif>{{ $product->name }}
                ({{ $product->barcode }})
                <span>Qty:
                    {{ $product->stock }}</span>
            </a>
        </li>
    @endforeach
@endif
