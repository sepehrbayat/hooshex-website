@props([
    'text' => 'ما هر روز کلی مطلب آموزشی جالب در اینستاگراممون منتشر میکنیم !',
    'buttonText' => 'ورود به اینستاگرام',
    'instagramUrl' => 'https://instagram.com',
])

<x-ui.section id="instagram" background="transparent">
    {{-- Desktop Layout --}}
    <div class="relative hidden lg:flex items-center justify-between" style="min-height: 209px;">
        <div class="flex items-center justify-center gap-4" style="padding: 0; margin: 0;">
            {{-- Instagram Logo --}}
            <div class="flex items-center justify-center flex-shrink-0" style="width: 145px; height: 101px;">
                <img 
                    src="{{ asset('figma-images/images/image-18.png') }}" 
                    alt="لوگوی اینستاگرام هوشکس" 
                    class="h-full w-full object-contain" 
                    loading="lazy"
                    width="145"
                    height="101"
                />
            </div>
            
            {{-- Text --}}
            <div class="flex items-center justify-center">
                <h3 class="text-2xl font-bold leading-[46px] text-text-primary text-center" style="display: flex; align-items: center; justify-content: center; max-width: 400px;">
                    {{ $text }}
                </h3>
            </div>
        </div>
        
        {{-- Button Container --}}
        <div class="flex flex-col items-center justify-center" style="width: 280px; height: 81px;">
            <a 
                href="{{ $instagramUrl }}" 
                target="_blank" 
                rel="noopener noreferrer" 
                class="btn btn-secondary btn-pill whitespace-nowrap"
            >
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="scale-x-[-1] flex-shrink-0">
                    <path d="M7.5 15L12.5 10L7.5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>{{ $buttonText }}</span>
            </a>
        </div>
    </div>
    
    {{-- Tablet Layout --}}
    <div class="relative hidden md:flex lg:hidden items-end justify-center gap-10" style="min-height: 123px;">
        <div class="flex flex-col items-end gap-6" style="width: 219px; height: 104px;">
            <h3 class="text-base font-black leading-6 text-text-secondary text-right" style="width: 219px; height: 48px; display: flex; align-items: center;">
                {{ $text }}
            </h3>
            
            <a 
                href="{{ $instagramUrl }}" 
                target="_blank" 
                rel="noopener noreferrer" 
                class="btn btn-secondary btn-sm"
                style="width: 158px;"
            >
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="scale-x-[-1]">
                    <path d="M7.5 15L12.5 10L7.5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>{{ $buttonText }}</span>
            </a>
        </div>
        
        {{-- Instagram Logo - Tablet --}}
        <div class="flex items-center justify-center flex-shrink-0">
            <img 
                src="{{ asset('figma-images/images/image-18.png') }}" 
                alt="لوگوی اینستاگرام هوشکس" 
                class="h-[99px] w-[97px] object-contain" 
                loading="lazy"
                width="97"
                height="99"
            />
        </div>
    </div>
    
    {{-- Mobile Layout --}}
    <div class="relative flex md:hidden flex-row items-end justify-center gap-[22.45px] mx-auto" style="width: 310.01px; min-height: 89px; padding: 0;">
        <div class="flex flex-col items-end gap-[13.47px] flex-shrink-0" style="width: 197.56px; height: 77.47px; flex: none; order: 0;">
            <h3 class="text-right" style="font-size: 13.4702px; line-height: 20px; color: #2D2D2D; width: 197.56px; height: 40px; display: flex; align-items: center; justify-content: flex-end; flex: none; order: 0; flex-grow: 0;">
                {{ $text }}
            </h3>
            
            <a 
                href="{{ $instagramUrl }}" 
                target="_blank" 
                rel="noopener noreferrer" 
                class="flex items-center justify-center gap-[6px] rounded-md border-[1.5px] border-accent-500 text-accent-500 transition hover:opacity-90"
                style="box-sizing: border-box; font-size: 9px; line-height: 14px; text-align: right; width: 119px; height: 24px; padding: 7.5px 18px; flex: none; order: 1; flex-grow: 0;"
            >
                <svg width="12" height="12" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="scale-x-[-1]" style="flex: none; order: 0; flex-grow: 0;">
                    <path d="M7.5 15L12.5 10L7.5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span style="flex: none; order: 1; flex-grow: 0;">اینستاگرام</span>
            </a>
        </div>
        
        {{-- Instagram Logo - Mobile --}}
        <div class="flex items-center justify-center flex-shrink-0" style="width: 90px; height: 89px; flex: none; order: 1;">
            <img 
                src="{{ asset('figma-images/images/image-18.png') }}" 
                alt="لوگوی اینستاگرام هوشکس" 
                class="h-full w-full object-contain" 
                loading="lazy"
                width="90"
                height="89"
            />
        </div>
    </div>
</x-ui.section>

