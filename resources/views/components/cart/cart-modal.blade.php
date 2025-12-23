<div
    x-data="cartModal()"
    x-cloak
    x-show="open"
    x-transition.opacity
    @click.self="open = false"
    @keydown.escape.window="open = false"
    class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 px-4"
    role="dialog"
    aria-modal="true"
>
    <div x-show="open" x-transition.scale class="w-full max-w-lg rounded-3xl bg-white p-6 shadow-2xl ring-1 ring-primary-50">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <h3 class="text-lg font-black text-primary-800">سبد خرید</h3>
                <span class="rounded-full bg-primary-50 px-3 py-1 text-xs font-semibold text-primary-700" x-text="count + ' مورد'"></span>
            </div>
            <button @click="open = false" class="text-text-muted transition hover:text-primary-700" aria-label="close cart modal">
                ✕
            </button>
        </div>

        <div class="mt-4 space-y-4">
            <template x-if="items.length === 0">
                <div class="rounded-card bg-primary-50 px-4 py-6 text-center text-sm text-primary-700">
                    <p class="font-semibold">برای خرید وارد شوید</p>
                    <p class="mt-2 text-text-secondary">سبد شما خالی است.</p>
                    <div class="mt-3 flex justify-center">
                        <a
                            href="#"
                            data-action="open-login"
                            data-next="cart"
                            class="inline-flex items-center rounded-pill bg-primary-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-primary-700"
                        >
                            ورود / ثبت‌نام
                        </a>
                    </div>
                </div>
            </template>

            <template x-if="items.length > 0">
                <div class="space-y-3">
                    <template x-for="item in items" :key="item.key">
                        <div class="flex items-center justify-between rounded-card border border-primary-50 px-4 py-3 shadow-sm">
                            <div>
                                <p class="text-sm font-bold text-primary-800" x-text="item.title"></p>
                                <p class="text-xs text-text-muted" x-text="'تعداد: ' + item.quantity"></p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-semibold text-primary-700" x-text="formatted(item.price * item.quantity)"></span>
                                <button
                                    @click="remove(item.key)"
                                    class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-600 transition hover:bg-red-100"
                                >
                                    حذف
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <div class="mt-5 flex flex-col gap-3">
            <div class="flex items-center justify-between text-sm font-semibold text-primary-800">
                <span>جمع کل</span>
                <span x-text="formatted(total)"></span>
            </div>
            <div class="flex items-center gap-2">
                <a
                    href="{{ route('ai-tools.index') }}"
                    wire:navigate
                    class="flex-1 rounded-pill bg-gradient-to-r from-primary-600 via-primary-500 to-accent-500 px-4 py-2 text-center text-sm font-semibold text-white shadow-md transition hover:-translate-y-0.5 hover:opacity-95"
                >
                    ادامه خرید
                </a>
                <button
                    @click="open = false"
                    class="rounded-pill border border-primary-100 px-4 py-2 text-sm font-semibold text-primary-700 transition hover:border-primary-200 hover:-translate-y-0.5"
                >
                    بستن
                </button>
            </div>
        </div>
    </div>
</div>

