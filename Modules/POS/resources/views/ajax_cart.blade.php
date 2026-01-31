<div class="product-table">
    <div class="table-responsive">
        <table class="table pos_pro_list_table">
            <thead class="text-center" style="background: #00a65a">
                <tr style="height: 25px; color: #fff;">
                    <th style="padding:4px 0px; margin:0px; width: 5%;">SL</th>
                    <th style="padding:4px 0px; margin:0px; width: 43%;">Name</th>
                    <th style="padding:4px 0px; margin:0px; width: 12%;">Qty</th>
                    <th style="padding:4px 0px; margin:0px; width: 12%;">Price</th>
                    <th style="padding:4px 0px; margin:0px; width: 13%;">Total</th>
                    <th style="padding:4px 0px; margin:0px; width: 15%;">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody>
                @php
                    $cumalitive_sub_total = $cumalitive_sub_total ?? 0;

                    // set rowid
                    foreach ($cart_contents as $key => $cart_content) {
                        $cart_contents[$key]['rowid'] = $key;
                    }
                    $i = 1;
                @endphp
                @foreach ($cart_contents as $cart_index => $cart_content)
                    @php
                        if (!isset($cart_content['rowid'])) {
                            // set rowid
                            $cart_content['rowid'] = rand(1000000, 9999999);
                            $cart_contents[$cart_index]['rowid'] = $cart_content['rowid'];
                            // put it in session
                            session()->put('cart', $cart_contents);
                        }
                        // Check if menu item has addons
                        $hasAddons = false;
                        if (($cart_content['type'] ?? '') === 'menu_item') {
                            $menuItem = \Modules\Menu\app\Models\MenuItem::find($cart_content['id']);
                            $hasAddons = $menuItem && $menuItem->activeAddons()->count() > 0;
                        }
                        // Load combo items if this is a combo
                        $comboItems = [];
                        if (($cart_content['type'] ?? '') === 'combo') {
                            $combo = \Modules\Menu\app\Models\Combo::with('items.menuItem', 'items.variant')->find($cart_content['id']);
                            if ($combo) {
                                $comboItems = $combo->items;
                            }
                        }
                    @endphp
                    <tr data-rowid="{{ $cart_content['rowid'] }}">
                        <td>
                            <p>{{ $i++ }}</p>
                        </td>
                        <td>
                            <p class="mb-0">
                                {{ $cart_content['name'] }}
                                @if(($cart_content['type'] ?? '') === 'combo')
                                    <span class="badge bg-info ms-1" style="font-size: 9px;">{{ __('Combo') }}</span>
                                @endif
                            </p>
                            @if (isset($cart_content['variant']))
                                <small class="text-muted">
                                    {{ $cart_content['variant']['attribute'] }}
                                </small>
                            @endif
                            {{-- Show combo items --}}
                            @if(count($comboItems) > 0)
                                <div class="combo-items mt-1 ps-2" style="border-left: 2px solid #17a2b8;">
                                    @foreach($comboItems as $comboItem)
                                        <div class="combo-item text-muted" style="font-size: 11px;">
                                            <i class="fas fa-check text-success me-1"></i>
                                            {{ $comboItem->menuItem->name ?? 'Item' }}
                                            @if($comboItem->quantity > 1)
                                                <span class="text-primary">x{{ $comboItem->quantity }}</span>
                                            @endif
                                            @if($comboItem->variant)
                                                <small class="text-info">({{ $comboItem->variant->name }})</small>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            @if (!empty($cart_content['addons']))
                                <div class="addon-items mt-1">
                                    @foreach ($cart_content['addons'] as $addonIndex => $addon)
                                    <div class="addon-item d-flex align-items-center justify-content-between bg-light rounded px-2 py-1 mb-1" style="font-size: 11px;">
                                        <span class="text-info">
                                            <i class="fas fa-plus-circle me-1"></i>{{ $addon['name'] }}
                                            <span class="text-success">({{ currency($addon['price']) }})</span>
                                        </span>
                                        <div class="d-flex align-items-center gap-1">
                                            <input type="number" min="1" value="{{ $addon['qty'] ?? 1 }}"
                                                class="form-control form-control-sm addon-qty-input"
                                                style="width: 50px; height: 22px; font-size: 11px; padding: 2px 4px;"
                                                onchange="updateAddonQty('{{ $cart_content['rowid'] }}', {{ $addon['id'] }}, this.value)">
                                            <a href="javascript:;" onclick="removeAddon('{{ $cart_content['rowid'] }}', {{ $addon['id'] }})"
                                                class="text-danger" title="{{ __('Remove') }}">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td data-rowid="{{ $cart_content['rowid'] }}" class="px-3">
                            <input min="1" type="number" value="{{ $cart_content['qty'] }}"
                                class="pos_input_qty form-control">
                        </td>
                        <td class="price">
                            <span>
                                {{ currency($cart_content['price']) }}
                            </span>
                            <input type="text" value="{{ $cart_content['price'] }}" name="table_price[]"
                                style="width:100px" data-rowid="{{ $cart_content['rowid'] }}" class="d-none">
                        </td>
                        @php
                            $sub_total = $cart_content['sub_total'];
                            $cumalitive_sub_total += $sub_total;
                        @endphp

                        <td class="row_total">{{ currency($sub_total) }}</td>

                        <td class="text-center">
                            <div class="d-flex align-items-center gap-1 justify-content-center">
                                @if($hasAddons)
                                <a href="javascript:;" onclick="openAddonModal('{{ $cart_content['rowid'] }}', {{ $cart_content['id'] }})"
                                    class="d-block p-1" title="{{ __('Add-ons') }}">
                                    <i class="fas fa-plus-circle text-info"></i>
                                </a>
                                @endif
                                <a href="javascript:;" onclick="removeCartItem('{{ $cart_content['rowid'] }}')"
                                    class="d-block p-1"><i class="fa fa-trash text-danger" aria-hidden="true"></i></a>

                                <a href="javascript:;"
                                    class="edit-btn {{ $cart_content['source'] == '2' ? '' : 'd-none' }}"
                                    data-purchase="{{ $cart_content['purchase_price'] }}"
                                    data-selling="{{ $cart_content['selling_price'] }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
