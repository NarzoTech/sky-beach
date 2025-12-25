{{-- Chef Card Component --}}
<div class="chef_item wow fadeInUp" data-wow-duration="1s">
    <div class="chef_img">
        @if($chef->image)
            <img src="{{ asset($chef->image) }}" alt="{{ $chef->name }}" class="img-fluid w-100">
        @else
            <img src="{{ asset('website/images/chef_1.jpg') }}" alt="{{ $chef->name }}" class="img-fluid w-100">
        @endif
        
        @if($chef->is_featured)
            <span class="featured_badge">⭐ Featured</span>
        @endif
    </div>
    <div class="chef_text">
        <h3>{{ $chef->name }}</h3>
        <p class="designation">{{ $chef->designation }}</p>
        
        @if($chef->specialization)
            <p class="specialization">{{ $chef->specialization }}</p>
        @endif
        
        @if($chef->experience_years)
            <p class="experience">{{ $chef->experience_years }}+ years experience</p>
        @endif
        
        @if($chef->bio)
            <p class="bio">{{ Str::limit($chef->bio, 120) }}</p>
        @endif
        
        <ul class="social_links">
            @if($chef->facebook)
                <li><a href="{{ $chef->facebook }}" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
            @endif
            @if($chef->twitter)
                <li><a href="{{ $chef->twitter }}" target="_blank"><i class="fab fa-twitter"></i></a></li>
            @endif
            @if($chef->instagram)
                <li><a href="{{ $chef->instagram }}" target="_blank"><i class="fab fa-instagram"></i></a></li>
            @endif
            @if($chef->linkedin)
                <li><a href="{{ $chef->linkedin }}" target="_blank"><i class="fab fa-linkedin-in"></i></a></li>
            @endif
        </ul>
    </div>
</div>
