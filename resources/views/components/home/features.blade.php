@props([
    'features' => [
        [
            'title' => 'دوره های متنوع',
            'description' => 'بیش از 1۰ دوره آموزشی کاربردی',
            'icon' => 'figma-images/images/various-courses-vector.png',
        ],
        [
            'title' => 'مسیر شغلی',
            'description' => 'طراحی مسیر شغلی',
            'icon' => 'figma-images/images/career-path-vector.png',
        ],
        [
            'title' => 'سوپر اپلیکیشن',
            'description' => 'بهترین هوش‌مصنوعی فارسی',
            'icon' => 'figma-images/images/super-app-vector.png',
        ],
        [
            'title' => 'توسعه هوش‌مصنوعی',
            'description' => 'اتوماسیون فرآیند با هوش‌مصنوعی',
            'icon' => 'figma-images/images/dev-with-ai-vector.png',
        ],
    ],
])

<section id="section-features" class="features-section">
    <div class="features-grid">
        @foreach ($features as $feature)
            <div class="feature-card">
                <img 
                    src="{{ asset($feature['icon']) }}" 
                    alt="{{ $feature['title'] }}" 
                    class="feature-icon" 
                    width="99" 
                    height="99"
                />
                <h3 class="feature-title">
                    {{ $feature['title'] }}
                </h3>
                <p class="feature-description">
                    {{ $feature['description'] }}
                </p>
            </div>
        @endforeach
    </div>
</section>
