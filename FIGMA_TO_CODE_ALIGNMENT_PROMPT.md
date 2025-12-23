# پرامپت جامع تطبیق بخش‌های وب‌سایت با طراحی Figma

این پرامپت برای استفاده در Cursor AI طراحی شده است تا بخش‌های مختلف وب‌سایت را با دقت 100% با طراحی Figma همسان کند.

## دستورالعمل کلی

```
از طریق Figma MCP به Figma متصل شو و بخش [نام بخش] را به طور کامل همسان کن با طراحی Figma. 
از تمام 40+ ابزار Figma MCP استفاده کن تا اطلاعات دقیق را استخراج کنی.
```

## مراحل کار

### مرحله 1: اتصال و استخراج اطلاعات از Figma

1. **اتصال به Figma:**
   - از `mcp_TalkToFigma_get_document_info` برای دریافت اطلاعات کلی سند استفاده کن
   - از `mcp_TalkToFigma_get_selection` برای بررسی انتخاب فعلی استفاده کن
   - از `mcp_TalkToFigma_read_my_design` برای دریافت جزئیات کامل طراحی انتخاب شده

2. **یافتن بخش مورد نظر:**
   - از `mcp_TalkToFigma_scan_text_nodes` برای یافتن متن‌های خاص (مثلاً "بلاگ هوشکس")
   - از `mcp_TalkToFigma_scan_nodes_by_types` برای یافتن Frame/Component/Group های مرتبط
   - از `mcp_TalkToFigma_get_nodes_info` برای دریافت اطلاعات چند node همزمان

3. **استخراج جزئیات:**
   - برای هر element مهم، از `mcp_TalkToFigma_get_node_info` استفاده کن
   - تمام اطلاعات را استخراج کن: ابعاد، رنگ‌ها، فاصله‌ها، تایپوگرافی، positioning

### مرحله 2: تحلیل و مقایسه

1. **خواندن کد فعلی:**
   - فایل Blade مربوطه را بخوان (`resources/views/livewire/home.blade.php`)
   - بخش مربوطه را پیدا کن
   - CSS فایل‌های استخراج شده از Figma را بررسی کن (`desktop-figma.css`, `mobile-figma.css`, `tablet-figma.css`)

2. **مقایسه:**
   - ابعاد (width, height) را مقایسه کن
   - رنگ‌ها (background, text, border) را مقایسه کن
   - فاصله‌ها (padding, margin, gap) را مقایسه کن
   - تایپوگرافی (font-size, line-height, font-weight) را مقایسه کن
   - Positioning (absolute, relative, left, top, right, bottom) را مقایسه کن
   - Border-radius, shadows, opacity را مقایسه کن

### مرحله 3: اعمال تغییرات

1. **به‌روزرسانی HTML/Blade:**
   - کلاس‌های Tailwind را به‌روزرسانی کن
   - استایل‌های inline را به‌روزرسانی کن
   - ساختار HTML را در صورت نیاز تغییر بده

2. **تطبیق RTL:**
   - در RTL، `justify-start` = راست، `justify-end` = چپ
   - از `dir="rtl"` استفاده کن
   - `text-right` برای متن فارسی

3. **Responsive Design:**
   - برای mobile: از `md:` و `lg:` استفاده کن
   - ابعاد و فاصله‌ها را برای هر breakpoint تطبیق بده

### مرحله 4: بررسی و تست

1. **Linting:**
   - از `read_lints` استفاده کن تا خطاهای syntax را پیدا کنی

2. **تست بصری:**
   - در صورت امکان، از browser automation استفاده کن
   - با طراحی Figma مقایسه کن

## چک‌لیست تطبیق

برای هر بخش، این موارد را بررسی کن:

### Container/Frame
- [ ] width دقیق (px)
- [ ] height دقیق (px)
- [ ] background color/opacity
- [ ] border-radius
- [ ] padding/margin
- [ ] gap (برای flex/grid)
- [ ] flex-direction
- [ ] align-items
- [ ] justify-content
- [ ] position (relative/absolute)

### Typography
- [ ] font-family (Vazirmatn)
- [ ] font-size (px)
- [ ] font-weight
- [ ] line-height
- [ ] text-align
- [ ] text-transform
- [ ] color
- [ ] font-feature-settings
- [ ] font-variation-settings

### Images
- [ ] width (px)
- [ ] height (px)
- [ ] position (absolute: left, top)
- [ ] border-radius
- [ ] object-fit

### Buttons/Badges
- [ ] width (px)
- [ ] height (px)
- [ ] padding (px)
- [ ] background color
- [ ] border-radius
- [ ] font-size
- [ ] line-height
- [ ] position (absolute: left, top)

### Icons
- [ ] width (px)
- [ ] height (px)
- [ ] position (absolute: left, top)
- [ ] color/fill

### Spacing Elements
- [ ] width (px)
- [ ] height (px)
- [ ] background color/opacity
- [ ] position

## مثال: تطبیق بخش بلاگ

### اطلاعات استخراج شده از Figma:

```
blog container:
- width: 1216px
- height: 472px
- align-items: center
- flex-direction: column

blog-carousel:
- width: 1216px
- height: 460px
- gap: 47px
- align-items: flex-end
- flex-direction: column

Title "بلاگ هوشکس":
- width: 1216px
- height: 48px
- font-size: 32px
- line-height: 48px
- color: #22165E
- text-align: right

blog-frame:
- width: 1216px
- height: 316px

Blog Card:
- width: 292px
- height: 332px
- background: rgba(119, 95, 238, 0.1)
- border-radius: 8px

Image:
- width: 260px
- height: 186px
- left: 16px
- top: 16px
- border-radius: 8px

Badge:
- width: 79px
- height: 23.62px
- left: 197px
- top: 218px
- padding: 8px 13px
- background: #EB55C8
- border-radius: 8px
- font-size: 10px
- line-height: 15px
- color: #FCF1FB

Content Container:
- width: 260px
- height: 58px
- left: 16px
- top: 258px
- gap: 8px
- align-items: flex-end

Title:
- width: 260px
- height: 32px
- font-size: 12px
- line-height: 18px
- color: #22165E

Meta Info:
- width: 216px
- height: 18px
- gap: 8px
- flex-direction: row

Reading Time:
- width: 101px
- height: 18px
- font-size: 12px
- line-height: 18px
- color: #666666

Dot Separator:
- width: 14px
- height: 14px
- background: rgba(51, 33, 140, 0.3)

Date:
- width: 63px
- height: 18px
- font-size: 12px
- line-height: 18px
- color: #666666
```

### کد تطبیق یافته:

```blade
{{-- Blog Section - Pixel Perfect from Figma --}}
<section id="blog" class="bg-transparent py-12 md:py-16 lg:py-20" dir="rtl">
    <div class="mx-auto w-full max-w-[1216px] px-4 md:px-8 lg:px-0">
        {{-- Section Title - Pixel Perfect --}}
        <div class="mb-[47px] flex items-center justify-end">
            <h2 class="text-[28px] md:text-[30px] lg:text-[32px] font-bold leading-[48px] text-[#22165E] text-right" style="font-family: 'Vazirmatn', sans-serif; font-weight: 700; text-transform: capitalize; font-feature-settings: 'ss04' on, 'ss03' on, 'ss02' on, 'ss01' on; font-variation-settings: 'DOTS' 7; width: 1216px; height: 48px; display: flex; align-items: center; text-align: right;">بلاگ هوشکس</h2>
        </div>
        
        {{-- Blog Cards Container - Pixel Perfect --}}
        <div class="relative flex flex-col items-center gap-5 md:flex-row md:items-start md:justify-center md:gap-5" style="width: 1216px; height: 316px;">
            @foreach ($blogs as $index => $blog)
                <div class="relative flex flex-col rounded-[8px] bg-[rgba(119,95,238,0.1)]" style="width: 292px; height: 332px;">
                    {{-- Image - Pixel Perfect --}}
                    <div class="absolute left-4 top-4 h-[186px] w-[260px] overflow-hidden rounded-[8px]">
                        <img src="..." alt="..." class="h-full w-full object-cover" width="260" height="186" />
                    </div>
                    
                    {{-- Badge - Pixel Perfect --}}
                    <div class="absolute z-10 flex items-center justify-center rounded-[8px] bg-[#eb55c8]" style="left: 197px; top: 218px; width: 79px; height: 23.62px; padding: 8px 13px; gap: 8px;">
                        <span class="text-[10px] font-normal leading-[15px] text-[#fcf1fb] text-right" style="...">جدید</span>
                    </div>
                    
                    {{-- Content - Pixel Perfect --}}
                    <div class="absolute flex flex-col items-end" style="left: 16px; top: 258px; width: 260px; height: 58px; gap: 8px;">
                        <h3 class="text-[12px] font-bold leading-[18px] text-[#22165E] text-right" style="...">{{ $blog['title'] }}</h3>
                        <div class="flex flex-row items-center" style="width: 216px; height: 18px; gap: 8px;">
                            <span class="text-[12px] font-normal leading-[18px] text-[#666666] capitalize" style="width: 101px; height: 18px;">٥ دقیقه زمان مطالعه</span>
                            <span class="rounded-full" style="width: 14px; height: 14px; background: rgba(51, 33, 140, 0.3);"></span>
                            <span class="text-[12px] font-normal leading-[18px] text-[#666666] capitalize" style="width: 63px; height: 18px;">١٤ آبان ١٤٠٤</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
```

## نکات مهم

1. **دقت در ابعاد:** همیشه از مقادیر دقیق px استفاده کن، نه مقادیر تقریبی
2. **RTL Support:** همیشه RTL را در نظر بگیر و از `dir="rtl"` و `text-right` استفاده کن
3. **Font Settings:** همیشه `font-feature-settings` و `font-variation-settings` را اضافه کن
4. **Responsive:** برای mobile/tablet از breakpoint های Tailwind استفاده کن
5. **Comments:** در کد، کامنت‌های "Pixel Perfect from Figma" اضافه کن
6. **Inline Styles:** برای مقادیر دقیق px که در Tailwind نیستند، از inline styles استفاده کن

## ابزارهای Figma MCP که باید استفاده شوند

1. `mcp_TalkToFigma_get_document_info` - اطلاعات کلی سند
2. `mcp_TalkToFigma_get_selection` - انتخاب فعلی
3. `mcp_TalkToFigma_read_my_design` - جزئیات کامل طراحی
4. `mcp_TalkToFigma_get_node_info` - اطلاعات یک node خاص
5. `mcp_TalkToFigma_get_nodes_info` - اطلاعات چند node
6. `mcp_TalkToFigma_scan_text_nodes` - جستجوی متن
7. `mcp_TalkToFigma_scan_nodes_by_types` - جستجوی بر اساس نوع
8. `mcp_TalkToFigma_get_styles` - استایل‌های سند
9. `mcp_TalkToFigma_get_local_components` - کامپوننت‌های محلی
10. و سایر ابزارهای مرتبط...

## خروجی مورد انتظار

بعد از تطبیق، باید:
- کد 100% با طراحی Figma همسان باشد
- تمام ابعاد، رنگ‌ها، فاصله‌ها دقیق باشند
- RTL به درستی کار کند
- Responsive برای mobile/tablet/desktop کار کند
- کامنت‌های "Pixel Perfect from Figma" اضافه شده باشد
- هیچ خطای linting وجود نداشته باشد

