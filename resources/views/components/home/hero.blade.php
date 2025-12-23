@props([
    'title' => "هوشِکس؛\nمرجع هوش مصنوعی فارسی",
    'searchPlaceholder' => 'دنبال چه دوره ای میکردی ؟',
    'searchAction' => route('ai-tools.index'),
])

<section id="section-hero" class="hero-container">
    <div class="hero-inner">
        <div class="hero-content">
            {{-- Text and Search Section --}}
            <div dir="rtl" class="hero-text-section">
                {{-- Title --}}
                <h1 class="hero-title">
                    {{ $title }}
                </h1>
                
                {{-- Search Input --}}
                <form class="hero-search-form" method="GET" action="{{ $searchAction }}">
                    <div class="hero-search-input-container" dir="rtl">
                        <input
                            type="text"
                            name="q"
                            aria-label="جستجوی دوره"
                            class="form-control input-search flex-1 border-none bg-transparent text-sm md:text-base lg:text-xl text-text-muted placeholder:text-text-muted focus:outline-none focus:ring-0"
                            placeholder="{{ $searchPlaceholder }}"
                            dir="rtl"
                        />
                        <button type="submit" aria-label="جستجو" class="flex h-5 w-5 md:h-[19px] md:w-[19px] lg:h-7 lg:w-7 items-center justify-center text-primary-600 transition hover:opacity-80">
                            <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="w-full h-full">
                                <path d="M12.9167 20.4167C17.1 20.4167 20.5 17.0167 20.5 12.8333C20.5 8.65 17.1 5.25 12.9167 5.25C8.73333 5.25 5.33333 8.65 5.33333 12.8333C5.33333 17.0167 8.73333 20.4167 12.9167 20.4167Z" stroke="rgba(51, 33, 140, 0.3)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M22.6667 22.6667L18.3333 18.3333" stroke="rgba(51, 33, 140, 0.3)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Hero Images Section --}}
            <div class="hero-images-container" dir="ltr">
                <div class="hero-image-wrapper">
                    {{-- Background Blur Ellipse --}}
                    <div class="hero-blur-effect" aria-hidden="true"></div>
                    
                    {{-- Main 3D Image - Desktop --}}
                    <img 
                        src="{{ asset('figma-images/images/ai-vector-hero-section.png') }}" 
                        alt="تصویر سه‌بعدی مرجع فارسی هوشکس" 
                        loading="lazy" 
                        class="hero-image-desktop"
                        width="487" 
                        height="465" 
                    />
                    
                    {{-- Tablet Image --}}
                    <img 
                        src="{{ asset('figma-images/images/ai-vector-hero-section.png') }}" 
                        alt="تصویر سه‌بعدی مرجع فارسی هوشکس" 
                        loading="lazy" 
                        class="hero-image-tablet"
                        width="549" 
                        height="242" 
                    />
                    
                    {{-- Mobile Image --}}
                    <img 
                        src="{{ asset('figma-images/images/ai-vector-hero-section.png') }}" 
                        alt="تصویر سه‌بعدی مرجع فارسی هوشکس" 
                        loading="lazy" 
                        class="hero-image-mobile"
                        width="227" 
                        height="217" 
                    />
                </div>
            </div>
        </div>
    </div>
</section>
