{{-- Blog Card Component --}}
<div class="blog_item wow fadeInUp" data-wow-duration="1s">
    <div class="blog_img">
        <img src="{{ $blog->image_url }}" alt="{{ $blog->title }}" class="img-fluid w-100">
        
        @if($blog->is_featured)
            <span class="featured_badge">⭐ Featured</span>
        @endif
    </div>
    <div class="blog_text">
        <ul class="blog_meta">
            <li>
                <i class="far fa-calendar-alt"></i> 
                {{ $blog->published_at ? $blog->published_at->format('F d, Y') : $blog->created_at->format('F d, Y') }}
            </li>
            @if($blog->author)
                <li><i class="far fa-user"></i> {{ $blog->author }}</li>
            @endif
            <li><i class="far fa-eye"></i> {{ $blog->views }} views</li>
        </ul>
        
        <h3>
            <a href="{{ route('website.blog-details', $blog->slug) }}">{{ $blog->title }}</a>
        </h3>
        
        @if($blog->short_description)
            <p>{{ Str::limit($blog->short_description, 150) }}</p>
        @else
            <p>{{ Str::limit(strip_tags($blog->description), 150) }}</p>
        @endif
        
        @if($blog->tags)
            <div class="blog_tags">
                @foreach(explode(',', $blog->tags) as $tag)
                    <span class="tag">{{ trim($tag) }}</span>
                @endforeach
            </div>
        @endif
        
        <a class="read_more" href="{{ route('website.blog-details', $blog->slug) }}">
            Read More <i class="far fa-long-arrow-right"></i>
        </a>
    </div>
</div>
