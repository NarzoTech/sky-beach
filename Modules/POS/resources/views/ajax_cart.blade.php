<div class="product-table">
    <div class="table-responsive">
        <table class="table pos_pro_list_table">
            <thead class="text-center" style="background: #00a65a">
                <tr style="height: 25px; color: #fff;">
                    <th style="padding:4px 0px; margin:0px; width: 5%;">SL</th>
                    <th style="padding:4px 0px; margin:0px; width: 35%;">Name</th>
                    <th style="padding:4px 0px; margin:0px; width: 12%;">Qty</th>
                    <th style="padding:4px 0px; margin:0px; width: 8%;">Unit</th>
                    <th style="padding:4px 0px; margin:0px; width: 10%;">Price</th>
                    <th style="padding:4px 0px; margin:0px; width: 12%;">Total</th>
                    <th style="padding:4px 0px; margin:0px; width:8%;">
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
                    @endphp
                    <tr data-rowid="{{ $cart_content['rowid'] }}">
                        <td>
                            <p>{{ $i++ }}</p>
                        </td>
                        <td>
                            <p>{{ $cart_content['name'] }}</p>
                            @if (isset($cart_content['variant']))
                                <span>
                                    {{ $cart_content['variant']['attribute'] }}
                                </span>
                            @endif
                        </td>
                        <td data-rowid="{{ $cart_content['rowid'] }}" class="px-3">
                            <input min="1" type="number" value="{{ $cart_content['qty'] }}"
                                class="pos_input_qty form-control">
                        </td>
                        <td class="px-3">
                            {{ $cart_content['unit'] ?? '' }}
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
                                <a href="javascript:;" onclick="removeCartItem('{{ $cart_content['rowid'] }}')"
                                    class="d-block p-2 "><i class="fa fa-trash text-danger" aria-hidden="true"></i></a>

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
