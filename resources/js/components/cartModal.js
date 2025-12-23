/**
 * Cart Modal Alpine.js Component
 * Displays and manages cart items
 */
const formatCurrency = (value) =>
    Number(value ?? 0).toLocaleString('fa-IR', { maximumFractionDigits: 0 }) + ' تومان';

export default () => ({
    open: false,
    items: [],
    total: 0,
    count: 0,
    init() {
        this.refresh();

        window.addEventListener('cart:updated', (event) => {
            this.items = event.detail.items ?? [];
            this.total = event.detail.total ?? 0;
            this.count = event.detail.count ?? 0;
        });

        window.addEventListener('cart:open', () => {
            this.open = true;
            this.refresh();
        });
    },
    refresh() {
        this.items = window.hxCart.getItems();
        this.total = window.hxCart.total();
        this.count = window.hxCart.count();
    },
    remove(key) {
        window.hxCart.removeItem(key);
    },
    formatted(value) {
        return formatCurrency(value);
    },
});

