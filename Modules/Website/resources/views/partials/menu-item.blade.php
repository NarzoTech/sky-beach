{{-- Menu Item Component --}}
<div class="menu_item wow fadeInUp" data-wow-duration="1s">
    <div class="menu_img">
        <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="img-fluid w-100">
        
        @if($item->discount_percentage > 0)
            <span class="discount_badge">{{ $item->discount_percentage }}% OFF</span>
        @endif
        
        @if($item->is_new)
            <span class="new_badge">NEW</span>
        @endif
        
        @if($item->is_vegetarian)
            <span class="veg_badge">🌱 VEG</span>
        @endif
    </div>
    <div class="menu_text">
        <a class="category" href="#">{{ $item->category ?? 'Main Course' }}</a>
        <h3>
            <a href="{{ route('website.menu-details') }}">{{ $item->name }}</a>
        </h3>
        <p>{{ Str::limit(strip_tags($item->description), 100) }}</p>
        
        @if($item->is_spicy)
            <span class="spicy_indicator">🌶️ {{ ucfirst($item->spice_level ?? 'Spicy') }}</span>
        @endif
        
        <div class="menu_bottom d-flex align-items-center justify-content-between">
            <div class="menu_price">
                @if($item->discount_price)
                    <del class="old_price">{{ currency($item->price) }}</del>
                    <span class="new_price">{{ currency($item->discount_price) }}</span>
                @else
                    <span class="price">{{ currency($item->price) }}</span>
                @endif
            </div>
            <a class="cart_btn" href="#"><i class="far fa-shopping-basket"></i></a>
        </div>
        
        @if($item->preparation_time)
            <small class="prep_time">⏱️ {{ $item->preparation_time }} min</small>
        @endif
        
        @if($item->calories)
            <small class="calories">🔥 {{ $item->calories }} cal</small>
        @endif
    </div>
</div>
