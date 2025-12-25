<div class="pos_product_area">
    <div class="row">
        @foreach ($products as $product_index => $product)
            <div class="col-6 col-md-4 col-lg-4">
                <div class="card produt_card cursor-pointer"
                    @if ($product->has_variant) onclick="load_product_model({{ $product->id }})" @else onclick="singleAddToCart({{ $product->id }})" @endif>
                    <div class="w-100 produt_card_img">
                        <img src="{{ $product->singleImage }}" class="card-img-top" alt="Product">
                        @if ($product->is_favorite)
                            <button class="wishlist_btn remove" onclick="wishlist(event,{{ $product->id }}, 'remove')">
                                <i class="fas fa-times"></i>
                            </button>
                        @else
                            <button class="wishlist_btn" onclick="wishlist(event,{{ $product->id }}, 'add')">
                                <i class="far fa-heart"></i>
                            </button>
                        @endif
                    </div>
                    <div class="card-body">
                        <p class="card-title">
                            {{ $product->name }} <br>
                        </p>
                        <p class="price">{{ currency($product->current_price) }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{ $products->onEachSide(0)->links('pos::ajax_pagination') }}
