<div
    x-data="loginModal()"
    x-cloak
    x-show="open"
    x-transition.opacity
    @click.self="close()"
    @keydown.escape.window="close()"
    class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 px-4"
    role="dialog"
    aria-modal="true"
>
    <div x-show="open" x-transition.scale class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl ring-1 ring-primary-50">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-black text-primary-800">ورود / ثبت‌نام</h3>
            <button @click="close()" class="text-text-muted transition hover:text-primary-700" aria-label="close login modal">
                ✕
            </button>
        </div>

        <div class="mt-4 space-y-3 text-sm text-text-secondary">
            <p class="leading-6">برای ادامه، شماره موبایل خود را وارد کنید. کد تایید برای شما ارسال می‌شود.</p>
        </div>

        <div class="mt-5 space-y-4">
            <label class="block text-sm font-semibold text-primary-800">
                شماره موبایل
                <input
                    x-model="mobile"
                    type="tel"
                    placeholder="09xxxxxxxxx"
                    class="mt-2 w-full rounded-btn border border-primary-100 px-4 py-2.5 text-sm text-text-secondary focus:border-primary-500 focus:outline-none"
                />
            </label>

            <template x-if="step === 'verify'">
                <label class="block text-sm font-semibold text-primary-800">
                    کد تایید
                    <input
                        x-model="code"
                        type="text"
                        inputmode="numeric"
                        maxlength="5"
                        class="mt-2 w-full rounded-btn border border-primary-100 px-4 py-2.5 text-sm text-text-secondary focus:border-primary-500 focus:outline-none"
                    />
                </label>
            </template>

            <div class="space-y-2 text-xs">
                <template x-if="message">
                    <div class="rounded-btn bg-primary-50 px-3 py-2 text-primary-700" x-text="message"></div>
                </template>
                <template x-if="error">
                    <div class="rounded-btn bg-red-50 px-3 py-2 text-red-700" x-text="error"></div>
                </template>
            </div>

            <div class="flex flex-col gap-3">
                <button
                    x-show="step === 'request'"
                    @click="requestOtp"
                    :disabled="loading"
                    class="inline-flex items-center justify-center rounded-pill bg-gradient-to-r from-primary-600 via-primary-500 to-accent-500 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-primary-600/30 transition hover:-translate-y-0.5 hover:opacity-95 disabled:opacity-70"
                >
                    <span x-show="!loading">دریافت کد تایید</span>
                    <span x-show="loading">در حال ارسال...</span>
                </button>

                <button
                    x-show="step === 'verify'"
                    @click="verifyOtp"
                    :disabled="loading"
                    class="inline-flex items-center justify-center rounded-pill bg-primary-700 px-4 py-2.5 text-sm font-semibold text-white shadow-md transition hover:-translate-y-0.5 hover:bg-primary-800 disabled:opacity-70"
                >
                    <span x-show="!loading">ورود</span>
                    <span x-show="loading">در حال ورود...</span>
                </button>

                <button @click="close()" class="text-center text-xs text-text-muted hover:text-primary-700">انصراف</button>
            </div>
        </div>
    </div>
</div>

