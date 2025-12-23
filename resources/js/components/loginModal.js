/**
 * Login Modal Alpine.js Component
 * Handles OTP request and verification flow
 */
export default () => ({
    open: false,
    step: 'request',
    mobile: '',
    code: '',
    loading: false,
    message: '',
    error: '',
    next: null,
    init() {
        window.addEventListener('login:open', (event) => {
            this.next = event.detail?.next ?? null;
            this.open = true;
            this.error = '';
            this.message = '';
        });
    },
    close() {
        this.open = false;
        this.step = 'request';
        this.mobile = '';
        this.code = '';
        this.error = '';
        this.message = '';
    },
    async requestOtp() {
        this.loading = true;
        this.error = '';
        this.message = '';

        try {
            await window.axios.post('/auth/otp/request', { mobile: this.mobile });
            this.step = 'verify';
            this.message = 'کد تایید ارسال شد';
        } catch (err) {
            this.error = err?.response?.data?.message ?? 'ارسال کد ناموفق بود';
        } finally {
            this.loading = false;
        }
    },
    async verifyOtp() {
        this.loading = true;
        this.error = '';
        this.message = '';

        try {
            await window.axios.post('/auth/otp/verify', {
                mobile: this.mobile,
                code: this.code,
            });

            window.App = window.App || {};
            window.App.isAuthenticated = true;

            this.message = 'با موفقیت وارد شدید';

            await window.hxCart.syncToSession();

            if (this.next === 'cart') {
                setTimeout(() => window.hxCart.openCart(), 150);
            }

            setTimeout(() => this.close(), 250);
        } catch (err) {
            this.error = err?.response?.data?.message ?? 'ورود ناموفق بود';
        } finally {
            this.loading = false;
        }
    },
});

