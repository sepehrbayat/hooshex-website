<section id="super-app" class="relative overflow-hidden py-16 md:py-20 lg:py-24 w-full bg-[#FCF1FB]">

    <div class="relative mx-auto w-full px-4 md:px-8 md:max-w-full lg:max-w-[1201px]">
        <div class="flex flex-col-reverse items-center gap-12 md:gap-16 lg:grid lg:grid-cols-2 lg:items-start lg:gap-16 w-full max-w-full md:max-w-none">
            {{-- Text - Pixel Perfect from Figma --}}
            <div class="flex w-full max-w-xl flex-col items-center space-y-6 text-center md:max-w-2xl md:space-y-8 lg:order-2 lg:items-start lg:text-right lg:pr-4 lg:justify-self-end lg:self-center lg:max-w-[576px]" dir="rtl">
                <h2 class="text-[28px] md:text-[30px] lg:text-[32px] font-bold leading-[61px] text-[#22165E] whitespace-pre-line" style="font-family: 'Vazirmatn', sans-serif; font-weight: 700;">
                    بهترین هوش مصنوعی فارسی؛ 
<span class="text-[#EB55C8]">سوپر وب اپلیکیشن هوشِکس</span>
                </h2>

                <div class="space-y-4 text-[18px] md:text-[19px] lg:text-[20px] leading-[37.48px] text-[#22165E]" style="font-family: 'Vazirmatn', sans-serif; font-weight: 400;">
                    <p>
                        توی دنیای ابزارهای هوش مصنوعی،اگه دنبال بهترین هوش مصنوعی فارسی میگردی کم پیش میاد یه پلتفرم واقعاً فارسی‌زبان پیدا کنی که همه‌چیز رو یک‌جا داشته باشه. هوشکس دقیقاً برای همین ساخته شده: یه سوپر وب اپ فارسی که کمک می‌کنه راحت‌تر محتوا بسازی، طراحی کنی، و حتی کارهای سئو و تحلیل رو سریع‌تر پیش ببری.
                    </p>
                </div>

                <div class="flex w-full flex-wrap items-center justify-center gap-3 pt-2 lg:justify-start">
                    <a
                        href="{{ route('ai-tools.index') }}"
                        wire:navigate
                        class="group inline-flex items-center justify-center gap-3 rounded-[32px] bg-[#eb55c8] px-6 py-3 text-[16px] font-medium leading-[24px] text-[#f5f5f5] transition duration-200 hover:opacity-90 w-full max-w-[333px]"
                        style="font-family: 'Vazirmatn', sans-serif; font-weight: 500; min-height: 48px;"
                    >
                        <span>ورود به بهترین هوش مصنوعی فارسی</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-6 w-6 transition-transform duration-200 group-hover:-translate-x-1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Visual - Pixel Perfect from Figma --}}
            <div class="relative flex w-full items-center justify-center pt-0 md:pt-8 lg:order-1 lg:pt-0">
                <div class="relative mx-auto flex max-w-xs items-center justify-center md:max-w-md lg:max-w-[578px] lg:h-[694px]">
                    {{-- Background Blur Effects - Pixel Perfect --}}
                    <div class="pointer-events-none absolute left-[200px] top-[90px] h-[274px] w-[274px] rounded-full bg-[#33218c] opacity-100 blur-[100px] hidden lg:block"></div>
                    <div class="pointer-events-none absolute right-[0px] top-[237px] h-[274px] w-[274px] rounded-full bg-[#eb55c8] opacity-100 blur-[100px] hidden lg:block"></div>
                    
                    <img
                        src="{{ asset('figma-images/images/image-13.png') }}"
                        alt="نمایی از سوپر اپلیکیشن هوشکس"
                        loading="lazy"
                        class="relative z-10 w-full max-w-[300px] h-auto md:max-w-[400px] lg:w-[578px] lg:h-[694px] object-contain drop-shadow-2xl animate-float"
                        width="578"
                        height="694"
                    />
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        @media (prefers-reduced-motion: reduce) {
            .animate-float {
                animation: none;
            }
        }
    </style>
</section>

