<div class="pos_product_area">
    <div class="row">
        @foreach ($combos as $combo)
            <div class="col-6 col-md-4 col-lg-4">
                <div class="card produt_card cursor-pointer" onclick="addComboToCart({{ $combo->id }})">
                    <div class="w-100 produt_card_img">
                        <img src="{{ $combo->image_url }}" class="card-img-top" alt="{{ $combo->name }}">
                        @if ($combo->savings_percentage > 0)
                            <span class="badge bg-success position-absolute" style="top: 5px; left: 5px;">
                                {{ $combo->savings_percentage }}% OFF
                            </span>
                        @endif
                        <span class="badge bg-info position-absolute" style="top: 5px; right: 5px;">
                            <i class="fas fa-box"></i> Combo
                        </span>
                    </div>
                    <div class="card-body">
                        <p class="card-title">{{ $combo->name }}</p>
                        <p class="price">
                            {{ currency($combo->combo_price) }}
                            @if ($combo->original_price > $combo->combo_price)
                                <small class="text-muted text-decoration-line-through">{{ currency($combo->original_price) }}</small>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@if($combos->hasPages())
{{ $combos->onEachSide(0)->links('pos::ajax_pagination') }}
@endif
