<div class="row">
    @php
        $officeCount = $addresses->where('type', 'office')->count();
        $homeCount = $addresses->where('type', 'home')->count();
    @endphp
    @foreach ($addresses as $address)
        <div class="col-md-6">
            <div class="wsus__checkout_single_address">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="address_id" value="{{ $address->id }}"
                        class="form-check-input address_id" id="home-{{ $address->id }}"
                        @if ($address->default_address == 'yes') checked @endif>
                    <label class="form-check-label" for="{{ $address->type }}">
                        @if ($address->type == 'home')
                            <span class="icon"><i class="fas fa-home"></i> {{ __('home') }}
                                {{ $homeCount > 1 ? $homeCount-- : '' }}</span>
                        @else
                            <span class="icon"><i class="far fa-car-building"></i> {{ __('office') }}
                                {{ $officeCount > 1 ? $officeCount-- : '' }}</span>
                        @endif
                        @if ($address->default)
                            <span class="icon">{{ __('Default') }}</span>
                        @endif
                        <span class="address">{{ $address->address }}</span>
                    </label>
                </div>
            </div>
        </div>
    @endforeach
</div>


<script>
    $(document).ready(function() {
        $("input[name='address_id']").on("change", function() {
            let delivery_id = $("input[name='address_id']:checked").val();
            let deliveryCharge = $("input[name='address_id']:checked").data('delivery-charge');
            $("#order_address_id").val(delivery_id);
        });
    })
</script>
