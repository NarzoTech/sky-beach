<form id="modal_add_to_cart_form" method="POST">
    @csrf

    <input type="hidden" name="menu_item_id" value="{{ $menuItem->id }}">
    <input type="hidden" name="serviceType" value="menu_item">
    <input type="hidden" name="price" value="{{ $menuItem->base_price }}" id="modal_price">
    <input type="hidden" name="variant_price" value="{{ $menuItem->base_price }}" id="modal_variant_price">
    <input type="hidden" name="variant_id" value="" id="modal_variant_id">
    <input type="hidden" name="variant_sku" value="{{ $menuItem->sku }}" id="modal_variant_sku">

    <div class="row justify-content-center mb-3">
        <div class="col-md-12">
            <div class="card shadow-none">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 col-lg-3 col-xl-3 mb-4 mb-lg-0">
                            <div class="bg-image hover-zoom ripple rounded ripple-surface">
                                <img src="{{ $menuItem->image_url }}" class="w-100" alt="{{ $menuItem->name }}" />
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6 col-xl-6">
                            <h5>{{ $menuItem->name }}</h5>

                            @if($menuItem->is_vegetarian)
                                <span class="badge bg-success mb-2"><i class="fas fa-leaf"></i> {{ __('Vegetarian') }}</span>
                            @endif
                            @if($menuItem->is_vegan)
                                <span class="badge bg-success mb-2"><i class="fas fa-seedling"></i> {{ __('Vegan') }}</span>
                            @endif
                            @if($menuItem->is_spicy)
                                <span class="badge bg-danger mb-2"><i class="fas fa-pepper-hot"></i> {{ __('Spicy') }}</span>
                            @endif

                            <p class="text-truncate mb-4 mb-md-0">
                                {!! $menuItem->short_description !!}
                            </p>

                            @if($variants && count($variants) > 0)
                            <div class="mt-3">
                                <label class="form-label">{{ __('Select Variant') }}</label>
                                <select name="variant_id" id="variant_select" class="form-control">
                                    <option value="" data-price="{{ $menuItem->base_price }}" data-sku="{{ $menuItem->sku }}">
                                        {{ __('Regular') }} - {{ currency($menuItem->base_price) }}
                                    </option>
                                    @foreach ($variants as $variant)
                                        <option value="{{ $variant->id }}"
                                            data-price="{{ $menuItem->base_price + ($variant->price_adjustment ?? 0) }}"
                                            data-sku="{{ $variant->sku ?? $menuItem->sku }}">
                                            {{ $variant->name }} - {{ currency($menuItem->base_price + ($variant->price_adjustment ?? 0)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            @if($menuItem->activeAddons && count($menuItem->activeAddons) > 0)
                            <div class="mt-3">
                                <label class="form-label">{{ __('Add-ons') }}</label>
                                @foreach ($menuItem->activeAddons as $addon)
                                <div class="form-check">
                                    <input class="form-check-input addon-checkbox" type="checkbox"
                                        name="addons[]" value="{{ $addon->id }}"
                                        data-price="{{ $addon->price }}"
                                        id="addon_{{ $addon->id }}">
                                    <label class="form-check-label" for="addon_{{ $addon->id }}">
                                        {{ $addon->name }} (+{{ currency($addon->price) }})
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            @endif

                        </div>
                        <div class="col-md-6 col-lg-3 col-xl-3 border-sm-start-none border-start">
                            <div class="d-flex flex-row align-items-center mb-1">
                                <h4 class="mb-1 me-1 productPrice">{{ currency($menuItem->base_price) }}</h4>
                            </div>

                            @if($menuItem->preparation_time)
                            <p class="text-muted mb-2">
                                <i class="fas fa-clock"></i> {{ $menuItem->preparation_time }} {{ __('min') }}
                            </p>
                            @endif

                            @if($menuItem->calories)
                            <p class="text-muted mb-2">
                                <i class="fas fa-fire"></i> {{ $menuItem->calories }} {{ __('cal') }}
                            </p>
                            @endif

                            <div class="d-flex flex-column mt-4">
                                <input type="number" class="form-control" min='1' placeholder="{{ __('Quantity') }}" value="1" name="qty" id="modal_qty">
                            </div>

                            <div class="d-flex flex-column mt-4">
                                <button class="btn btn-outline-primary btn-sm mt-2" type="button" id="modal_add_to_cart">
                                    {{ __('Add to cart') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


<script>
    (function($) {
        "use strict";
        $(document).ready(function() {
            const basePrice = {{ $menuItem->base_price }};

            function updatePrice() {
                let price = basePrice;

                // Add variant price adjustment
                const selectedVariant = $('#variant_select option:selected');
                if (selectedVariant.length && selectedVariant.data('price')) {
                    price = parseFloat(selectedVariant.data('price'));
                }

                // Add addon prices
                $('.addon-checkbox:checked').each(function() {
                    price += parseFloat($(this).data('price') || 0);
                });

                // Update display
                $('.productPrice').text(formatCurrency(price));
                $('#modal_variant_price').val(price);
                $('#modal_price').val(price);
            }

            $('#variant_select').on('change', function() {
                const selected = $(this).find('option:selected');
                $('#modal_variant_id').val($(this).val());
                $('#modal_variant_sku').val(selected.data('sku'));
                updatePrice();
            });

            $('.addon-checkbox').on('change', function() {
                updatePrice();
            });

            $("#modal_add_to_cart").on("click", function(e) {
                e.preventDefault();

                $.ajax({
                    type: 'get',
                    data: $('#modal_add_to_cart_form').serialize(),
                    url: "{{ url('/admin/pos/add-to-cart') }}",
                    success: function(response) {
                        $(".shopping-card-body").html(response)
                        toastr.success("{{ __('Item added successfully') }}")
                        calculateTotalFee();
                        $("#cartModal").modal('hide');
                    },
                    error: function(response) {
                        if (response.status == 500) {
                            toastr.error("{{ __('Server error occurred') }}")
                        }
                        if (response.status == 403) {
                            toastr.error(response.responseJSON.message)
                        }
                    }
                });
            });
        });

        function formatCurrency(amount) {
            return '{{ currency_icon() }}' + parseFloat(amount).toFixed(2);
        }
    })(jQuery);
</script>
