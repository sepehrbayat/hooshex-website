@php
    $careerPaths = [
        [
            'category' => 'تولید محتوای هوشمند',
            'difficulty' => 'متوسط',
            'level' => 'متوسط',
            'title' => 'استراتژیست محتوای هوشمند',
            'income' => 'میانگین درآمد: ۳۰ میلیون تومان',
            'jobs' => 'تعداد موقعیت شغلی در بازار: ۲۴۰',
            'image' => 'figma-images/images/content-ai-spec-vector.png',
        ],
        [
            'category' => 'بازاریابی و رشد',
            'difficulty' => 'متوسط',
            'level' => 'متوسط',
            'title' => 'کارشناس اتوماسیون بازاریابی',
            'income' => 'میانگین درآمد: ۳۰ میلیون تومان',
            'jobs' => 'تعداد موقعیت شغلی در بازار: ۲۴۰',
            'image' => 'figma-images/images/automation-ai-spec-vector.png',
        ],
        [
            'category' => 'تصویرسازی و طراحی',
            'difficulty' => 'متوسط',
            'level' => 'متوسط',
            'title' => 'کارشناس تصویرسازی هوشمند',
            'income' => 'میانگین درآمد: ۳۰ میلیون تومان',
            'jobs' => 'تعداد موقعیت شغلی در بازار: ۲۴۰',
            'image' => 'figma-images/images/image-creator-spec-vector.png',
        ],
        [
            'category' => 'تحلیل داده و هوش تجاری',
            'difficulty' => 'متوسط',
            'level' => 'متوسط',
            'title' => 'تحلیلگر داده هوشمند',
            'income' => 'میانگین درآمد: ۳۰ میلیون تومان',
            'jobs' => 'تعداد موقعیت شغلی در بازار: ۲۴۰',
            'image' => 'figma-images/images/career-path-vector.png',
        ],
    ];
@endphp

<section id="paths" class="pt-20 pb-8 md:pt-16 md:pb-8 lg:py-8 w-full bg-[#FCF1FB]">
    <div class="mx-auto w-full px-4 md:px-8 md:max-w-full lg:max-w-[1440px]">
        {{-- Section Header - Pixel Perfect from Figma --}}
        <div class="mb-8 flex flex-col items-center gap-4 md:items-start w-full max-w-full md:max-w-none">
            <div class="flex w-full items-center justify-center gap-4 md:justify-between">
                <h2 class="text-[24px] md:text-[28px] lg:text-[32px] font-bold leading-[48px] text-[#22165E]" style="font-family: 'Vazirmatn', sans-serif; font-weight: 700;">
                    مسیر‌های شغلی هوشمند
                </h2>
                <a href="#courses" wire:navigate class="flex items-center gap-2 text-[16px] font-normal leading-[24px] text-[#22165E] transition hover:text-primary-600">
                    <span class="relative flex h-3.5 w-3.5 items-center justify-center">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M9.25 2.3335L4.66667 7.00016L9.25 11.6668" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span>همه مسیرها </span>
                </a>
            </div>
            <p class="w-full text-center text-[18px] md:text-[20px] font-normal leading-[30px] text-[#999999] md:text-right" style="font-family: 'Vazirmatn', sans-serif; font-weight: 400;">
                مسیر حرفه‌ای خود را انتخاب کنید و با دوره‌های تخصصی ما، مهارت‌های مورد نیاز بازار کار را بیاموزید.
            </p>
        </div>

        {{-- Cards Grid - Pixel Perfect from Figma --}}
        <div class="w-full max-w-full md:max-w-none grid grid-cols-1 gap-6 md:grid-cols-2 md:gap-5 lg:grid-cols-4 lg:gap-6 justify-items-center md:justify-items-stretch">
            @foreach ($careerPaths as $path)
                <div class="group flex flex-col overflow-hidden rounded-[8px] bg-[rgba(119,95,238,0.1)] w-full max-w-[320px] md:max-w-[288px] mx-auto md:mx-0 min-h-[411px]">
                    {{-- Card Header with badges - Pixel Perfect --}}
                    <div class="flex flex-row-reverse items-center justify-between px-3 pt-[18px]" dir="rtl">
                        <span class="text-[10px] font-normal leading-[15px] text-[#442cbb]" style="font-family: 'Vazirmatn', sans-serif; font-weight: 400;">{{ $path['category'] }}</span>
                        <span class="inline-flex items-center justify-center rounded-[8px] bg-[#eb55c8] px-2.5 py-1 text-[10px] font-normal leading-[15px] text-[#fcf1fb]" style="font-family: 'Vazirmatn', sans-serif; font-weight: 400;">
                            {{ $path['level'] ?? $path['difficulty'] }}
                        </span>
                    </div>

                    {{-- Card Image - Pixel Perfect from Figma --}}
                    <div class="flex items-center justify-center" style="margin-top: 17px;">
                        <img
                            src="{{ asset($path['image']) }}"
                            alt="{{ $path['title'] }}"
                            loading="lazy"
                            class="h-[172px] w-[175px] object-contain transition duration-200 group-hover:scale-105"
                            width="175"
                            height="172"
                        />
                    </div>

                    {{-- Card Content - Pixel Perfect --}}
                    <div class="flex flex-1 flex-col gap-3 px-5 pb-5">
                        <h3 class="text-center text-[16px] font-black leading-[24px] text-[#1a1a1a] mb-2" style="font-family: 'Vazirmatn', sans-serif; font-weight: 900;">{{ $path['title'] }}</h3>

                        <div class="flex w-full flex-col items-center gap-2">
                            {{-- Income Info - Pixel Perfect --}}
                            <div class="inline-flex items-center gap-2 mx-auto" dir="rtl">
                                <div class="flex h-[30px] w-[30px] shrink-0 items-center justify-center rounded-[5px] bg-[#eb55c8]">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M8.66699 6H4.66699" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M14.6665 7.31327V8.68665C14.6665 9.05331 14.3731 9.35328 13.9998 9.36662H12.6931C11.9731 9.36662 11.3132 8.83995 11.2532 8.11995C11.2132 7.69995 11.3731 7.30662 11.6531 7.03328C11.8998 6.77995 12.2398 6.6333 12.6131 6.6333H13.9998C14.3731 6.64663 14.6665 6.94661 14.6665 7.31327Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M11.653 7.03349C11.373 7.30682 11.213 7.70016 11.253 8.12016C11.313 8.84016 11.973 9.36682 12.693 9.36682H13.9997V10.3335C13.9997 12.3335 12.6663 13.6668 10.6663 13.6668H4.66634C2.66634 13.6668 1.33301 12.3335 1.33301 10.3335V5.66683C1.33301 3.8535 2.42634 2.58683 4.12634 2.37349C4.29968 2.34683 4.47967 2.3335 4.66634 2.3335H10.6663C10.8397 2.3335 11.0063 2.34015 11.1663 2.36682C12.8863 2.56682 13.9997 3.84016 13.9997 5.66683V6.6335H12.613C12.2397 6.6335 11.8997 6.78015 11.653 7.03349Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <p class="text-right text-[14px] font-medium leading-[21px] text-[#606060]" style="font-family: 'Vazirmatn', sans-serif; font-weight: 500;">{{ $path['income'] }}</p>
                            </div>

                            {{-- Jobs Info - Pixel Perfect --}}
                            <div class="inline-flex items-center gap-2 mx-auto" dir="rtl">
                                <div class="flex h-[30px] w-[30px] shrink-0 items-center justify-center rounded-[5px] bg-[#eb55c8]">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M4.98633 12.2332L7.05299 13.8331C7.31966 14.0998 7.91966 14.2331 8.31966 14.2331H10.853C11.653 14.2331 12.5197 13.6331 12.7197 12.8331L14.3197 7.96648C14.653 7.03315 14.053 6.23315 13.053 6.23315H10.3863C9.98633 6.23315 9.65299 5.89982 9.71966 5.43315L10.053 3.29982C10.1863 2.69982 9.78633 2.03315 9.18633 1.83315C8.65299 1.63315 7.98633 1.89982 7.71966 2.29982L4.98633 6.36648" stroke="white" stroke-width="1.5" stroke-miterlimit="10"/>
                                        <path d="M1.58691 12.2331V5.69977C1.58691 4.76644 1.98691 4.43311 2.92025 4.43311H3.58691C4.52025 4.43311 4.92025 4.76644 4.92025 5.69977V12.2331C4.92025 13.1664 4.52025 13.4998 3.58691 13.4998H2.92025C1.98691 13.4998 1.58691 13.1664 1.58691 12.2331Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <p class="text-right text-[14px] font-medium leading-[21px] text-[#606060]" style="font-family: 'Vazirmatn', sans-serif; font-weight: 500;">{{ $path['jobs'] }}</p>
                            </div>
                        </div>

                        {{-- Price and CTA Button - Pixel Perfect --}}
                        <div class="mt-auto flex flex-col items-center w-full pt-2">
                            <p class="text-[10px] font-medium leading-[15px] text-[#999999] mb-2 text-center" style="font-family: 'Vazirmatn', sans-serif; font-weight: 500;">4,700,000 تومان</p>
                            <div class="flex items-center justify-center w-full">
                                <a
                                    href="{{ route('ai-tools.index') }}"
                                    wire:navigate
                                    class="inline-flex h-[32px] items-center justify-center rounded-[16px] border-2 border-[#775fee] px-6 py-[10px] text-[12px] font-medium leading-[18px] text-[#775fee] transition duration-200 hover:bg-primary-50 whitespace-nowrap"
                                    style="font-family: 'Vazirmatn', sans-serif; font-weight: 500; width: 137px;"
                                >
                                    مشاهده جزئیات
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
