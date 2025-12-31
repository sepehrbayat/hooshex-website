<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>دایرکتوری ابزارهای هوش مصنوعی</title>
</head>
<body class="bg-surface text-text-primary" style="font-family: 'Vazirmatn', sans-serif;">
    <main class="max-w-6xl mx-auto px-4 py-10">
        <header class="mb-8">
            <h1 class="text-2xl font-black mb-2">دایرکتوری ابزارهای هوش مصنوعی</h1>
            <p class="text-text-secondary">فیلتر بر اساس قیمت و دسته‌بندی و مرور سریع ابزارها.</p>
        </header>

        @livewire('ai-tools.grid')
    </main>
</body>
</html>

