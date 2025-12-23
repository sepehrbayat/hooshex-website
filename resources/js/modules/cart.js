const CART_STORAGE_KEY = 'hooshex_cart';

const isAuthenticated = () => Boolean(window.App?.isAuthenticated);
const initialSessionCart = Array.isArray(window.App?.sessionCart) ? window.App.sessionCart : [];

const safeParse = (value) => {
    try {
        return JSON.parse(value ?? '[]');
    } catch (e) {
        console.warn('Cart parse failed', e);
        return [];
    }
};

const loadLocalCart = () => {
    const items = safeParse(localStorage.getItem(CART_STORAGE_KEY));

    return items
        .map((item) => ({
            key: item.key ?? `${item.type}:${item.id}`,
            type: item.type ?? 'course',
            id: item.id,
            title: item.title ?? '',
            price: Number(item.price ?? 0),
            quantity: Math.max(1, Number(item.quantity ?? 1)),
        }))
        .filter((item) => item.id !== undefined && item.id !== null);
};

const persistLocalCart = (items) => {
    localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(items));
};

const countItems = (items) => items.reduce((sum, item) => sum + (item.quantity ?? 0), 0);
const totalItems = (items) => items.reduce((sum, item) => sum + (item.price ?? 0) * (item.quantity ?? 1), 0);

let cartItems = isAuthenticated() ? initialSessionCart : loadLocalCart();

const updateCartBadges = () => {
    const count = countItems(cartItems);
    document.querySelectorAll('[data-cart-count]').forEach((el) => {
        if (count > 0) {
            el.textContent = count > 99 ? '99+' : count;
            el.classList.remove('hidden');
        } else {
            el.classList.add('hidden');
        }
    });
};

const emitCartUpdated = () => {
    updateCartBadges();
    window.dispatchEvent(
        new CustomEvent('cart:updated', {
            detail: {
                items: cartItems,
                count: countItems(cartItems),
                total: totalItems(cartItems),
            },
        }),
    );
};

const syncLocalFromSession = () => {
    if (isAuthenticated() && initialSessionCart.length) {
        cartItems = initialSessionCart.map((item) => ({
            ...item,
            key: item.key ?? `${item.type}:${item.id}`,
        }));
        persistLocalCart(cartItems);
    }
};

syncLocalFromSession();
updateCartBadges();

export const hxCart = {
    getItems: () => cartItems,
    count: () => countItems(cartItems),
    total: () => totalItems(cartItems),
    setItems(nextItems) {
        cartItems = nextItems.map((item) => ({
            ...item,
            key: item.key ?? `${item.type}:${item.id}`,
            quantity: Math.max(1, Number(item.quantity ?? 1)),
            price: Number(item.price ?? 0),
        }));
        persistLocalCart(cartItems);
        emitCartUpdated();
    },
    addItem(rawItem) {
        const item = {
            type: rawItem.type ?? 'course',
            id: rawItem.id,
            title: rawItem.title ?? '',
            price: Number(rawItem.price ?? 0),
            quantity: Math.max(1, Number(rawItem.quantity ?? 1)),
        };

        if (item.id === undefined || item.id === null) {
            console.warn('Item id is required to add to cart');
            return;
        }

        const key = `${item.type}:${item.id}`;
        const existingIndex = cartItems.findIndex((entry) => entry.key === key);

        if (existingIndex >= 0) {
            const existing = cartItems[existingIndex];
            cartItems[existingIndex] = { ...existing, quantity: existing.quantity + item.quantity };
        } else {
            cartItems.push({ ...item, key });
        }

        persistLocalCart(cartItems);
        emitCartUpdated();

        if (isAuthenticated()) {
            this.syncToSession().catch(() => {});
        }
    },
    removeItem(key) {
        cartItems = cartItems.filter((item) => item.key !== key);
        persistLocalCart(cartItems);
        emitCartUpdated();

        if (isAuthenticated()) {
            this.syncToSession().catch(() => {});
        }
    },
    async syncToSession() {
        if (!isAuthenticated()) {
            return null;
        }

        try {
            const response = await window.axios.post('/api/cart/sync', {
                items: cartItems,
            });

            if (response?.data?.items) {
                this.setItems(
                    response.data.items.map((item) => ({
                        ...item,
                        key: item.key ?? `${item.type}:${item.id}`,
                    })),
                );
            }

            return response?.data ?? null;
        } catch (error) {
            console.error('Cart sync failed', error);
            return null;
        }
    },
    openCart() {
        window.dispatchEvent(new CustomEvent('cart:open'));
    },
};

// Make hxCart globally available for backward compatibility
window.hxCart = hxCart;

