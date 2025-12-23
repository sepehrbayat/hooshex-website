@props([])

<section id="aibot-sec" class="ai-bot-section" dir="rtl">
    {{-- Background Blur Effects --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden z-0">
        <div class="absolute left-[103px] top-[91px] h-[387px] w-[387px] rounded-full bg-accent-500 opacity-40 blur-[120px] hidden lg:block"></div>
        <div class="absolute left-[663px] top-[57px] h-[409px] w-[409px] rounded-full bg-primary-500 opacity-35 blur-[120px] hidden lg:block"></div>
    </div>

    <div class="container-responsive">
        <div class="ai-bot-container">
            {{-- Section Title --}}
            <h2 class="ai-bot-title">
                چی برام مناسبه؟
            </h2>
            
            {{-- AI Form (Pink Background) --}}
            <div class="ai-bot-pink-form">
                <div class="ai-bot-pink-form-inner">
                    <form class="flex flex-wrap items-end justify-end content-end gap-4 md:gap-[24px_16px] lg:gap-[24px_32px]" dir="rtl" style="row-gap: 24px; column-gap: 32px;">
                        {{-- Phone Number --}}
                        <div class="flex w-full md:w-[210px] xl:w-[215.33px] flex-col md:h-auto xl:h-[81px] md:min-w-0 xl:min-w-[140px] md:gap-3 xl:gap-4 xl:items-end" style="order: 0; flex-grow: 1; flex-basis: auto;">
                            <div class="flex flex-col items-end gap-3 xl:gap-3 xl:w-[226px] xl:h-[81px]">
                                <label class="text-sm font-normal leading-[21px] text-surface-light xl:w-[226px] xl:h-[21px]">شماره تماس</label>
                                <div class="flex items-center justify-end rounded-lg bg-white/10 backdrop-blur-sm w-full h-10 xl:w-[226px] xl:h-12 px-4 py-3">
                                    <input
                                        type="tel"
                                        placeholder="۰۹۱۰۰۰۰۰۰۰۰"
                                        class="w-full bg-transparent text-base leading-6 text-black/25 placeholder:text-black/25 focus:outline-none"
                                        dir="rtl"
                                    />
                                </div>
                            </div>
                        </div>

                        {{-- Last Name --}}
                        <div class="flex w-full md:w-[210px] xl:w-[215.33px] flex-col md:h-auto xl:h-[81px] md:min-w-0 xl:min-w-[140px] md:gap-3 xl:gap-4 xl:items-end" style="order: 1; flex-grow: 1; flex-basis: auto;">
                            <div class="flex flex-col items-end gap-3 xl:gap-3 xl:w-[226px] xl:h-[81px]">
                                <label class="text-sm font-normal leading-[21px] text-surface-light xl:w-[226px] xl:h-[21px]">نام خانوادگی</label>
                                <div class="flex items-center justify-end rounded-lg bg-white/10 backdrop-blur-sm w-full h-10 xl:w-[226px] xl:h-12 px-4 py-3">
                                    <input
                                        type="text"
                                        placeholder="نام خانوادگی"
                                        class="w-full bg-transparent text-base leading-6 text-black/25 placeholder:text-black/25 focus:outline-none"
                                        dir="rtl"
                                    />
                                </div>
                            </div>
                        </div>

                        {{-- First Name --}}
                        <div class="flex w-full md:w-[210px] xl:w-[215.33px] flex-col md:h-auto xl:h-[81px] md:min-w-0 xl:min-w-[140px] md:gap-3 xl:gap-4 xl:items-end" style="order: 2; flex-grow: 1; flex-basis: auto;">
                            <div class="flex flex-col items-end gap-3 xl:gap-3 xl:w-[226px] xl:h-[81px]">
                                <label class="text-sm font-normal leading-[21px] text-surface-light xl:w-[226px] xl:h-[21px]">نام</label>
                                <div class="flex items-center justify-end rounded-lg bg-white/10 backdrop-blur-sm w-full h-10 xl:w-[226px] xl:h-12 px-4 py-3">
                                    <input
                                        type="text"
                                        placeholder="نام"
                                        class="w-full bg-transparent text-base leading-6 text-black/25 placeholder:text-black/25 focus:outline-none"
                                        dir="rtl"
                                    />
                                </div>
                            </div>
                        </div>

                        {{-- Favorite (Interests) --}}
                        <div class="ai-bot-field-interests flex w-full md:w-[260px] xl:w-[328px] xl:min-w-[190px] xl:h-[88px] flex-col xl:justify-end xl:items-end md:gap-3 xl:gap-4 xl:ml-auto" style="order: 3; flex-grow: 0; flex-basis: 100%; width: 100%;">
                            <label class="text-base font-normal leading-6 text-surface-light xl:w-[328px] xl:h-6">علاقه مندی ها</label>
                            <div class="relative flex items-center justify-end rounded-lg bg-white/10 backdrop-blur-sm w-full h-10 xl:w-[328px] xl:h-12 xl:p-[10px_16px] xl:gap-1 px-4 py-3">
                                <select class="w-full appearance-none bg-transparent text-xl leading-[30px] text-surface-light focus:outline-none xl:w-[103px] xl:h-[30px]" dir="rtl">
                                    <option value="">برنامه نویسی</option>
                                    <option value="design">طراحی</option>
                                    <option value="marketing">بازاریابی</option>
                                    <option value="data">داده و تحلیل</option>
                                </select>
                                <svg class="absolute left-[10px] top-3 h-6 w-6 text-gray-300 xl:w-6 xl:h-6 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- AI Generator Chatbox (Glass) --}}
            <div class="ai-bot-glass-card">
                <div class="ai-bot-glass-card-inner">
                    <div class="glass-content overflow-hidden">
                        <div class="flex flex-col h-full items-end gap-12 md:gap-[60px] lg:gap-[46px]">
                            {{-- Question with Star Icon --}}
                            <div class="flex flex-col items-end gap-2 xl:gap-2 xl:w-[668px] xl:h-[88px] w-full">
                                <div class="flex items-center gap-4 xl:flex-row xl:justify-between xl:items-center xl:gap-[212px] xl:w-[668px] xl:h-[50px] w-full" dir="rtl">
                                    {{-- Star Icon Group --}}
                                    <div class="relative h-[50px] w-[55px] flex-shrink-0 xl:w-[54.6px] xl:mt-4">
                                        <div class="absolute left-0 top-[4.92px] h-[45.08px] w-[45.08px] rounded-sm bg-gradient-to-br from-blue-700 via-purple-600 to-purple-500"></div>
                                        <div class="absolute left-[32.07px] top-0 h-[22.54px] w-[22.54px] rounded-sm bg-gradient-to-br from-blue-700 via-purple-600 to-purple-500 opacity-50"></div>
                                        <div class="absolute left-[2.13px] top-[8.24px] h-[13.52px] w-[13.52px] rounded-sm bg-gradient-to-br from-blue-700 via-purple-600 to-purple-500 opacity-20"></div>
                                    </div>

                                    {{-- Question Text --}}
                                    <div class="flex flex-1 items-center gap-1.5 pr-4 xl:mt-4">
                                        <h4 class="text-xl md:text-[22px] lg:text-2xl font-bold leading-9 text-text-primary xl:w-[233px] xl:h-9">
                                            چه دوره ای مناسب منه؟
                                        </h4>
                                    </div>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-end xl:flex-row xl:justify-between xl:items-center xl:w-[668px] xl:h-12 w-full" dir="rtl">
                                {{-- Search Button --}}
                                <button
                                    type="button"
                                    class="btn btn-primary btn-pill flex h-12 items-center justify-center gap-2 px-6 py-[10px] text-base font-medium leading-6 text-surface-light w-full sm:w-auto xl:w-[187px] xl:h-12 xl:px-8 xl:gap-2 flex-shrink-0 xl:order-1"
                                >
                                    <span class="xl:w-[91px] xl:h-6">شروع جستجو</span>
                                    <svg class="h-5 w-5 xl:w-6 xl:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                                
                                {{-- Group: File Attach + Microphone --}}
                                <div class="group-299-buttons flex items-center gap-3 w-full sm:w-auto justify-center sm:justify-start xl:relative xl:w-[237px] xl:h-12 xl:gap-6 xl:order-2">
                                    {{-- File Attach Button --}}
                                    <button
                                        type="button"
                                        class="flex h-12 items-center justify-center gap-2 rounded-lg border-2 border-primary-600 px-4 text-base font-medium leading-6 text-primary-600 transition hover:opacity-90 flex-1 sm:flex-none xl:w-[164px] xl:h-12 xl:px-8 xl:py-[10px] xl:gap-2 whitespace-nowrap"
                                        aria-label="اتصال فایل"
                                    >
                                        <span class="xl:w-[72px] xl:h-6 whitespace-nowrap">اتصال فایل</span>
                                        <svg class="h-5 w-5 xl:w-5 xl:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                        </svg>
                                    </button>

                                    {{-- Microphone Button --}}
                                    <button
                                        type="button"
                                        class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg border-2 border-primary-600 text-primary-600 transition hover:opacity-90 xl:w-12 xl:h-12 xl:p-[10px_14px] xl:gap-2"
                                        aria-label="ضبط صدا"
                                    >
                                        <svg class="h-5 w-5 xl:w-5 xl:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
