//
async function submitCartForm(form) {
    const formData = new FormData(form);
    const response = await fetch(form.action, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData,
    });

    if (!response.ok) {
        return;
    }

    const data = await response.json();
    if (data.redirect) {
        window.location.href = data.redirect;
        return;
    }
    if (typeof data.cartCount !== 'undefined') {
        setCartCount(data.cartCount);
    }

    const cartContent = document.getElementById('cart-content');
    if (cartContent && typeof data.html === 'string') {
        cartContent.innerHTML = data.html;
    }
}
document.addEventListener('submit', function (e) {
    const form = e.target;
    if (!(form instanceof HTMLFormElement)) return;
    if (!form.hasAttribute('data-ajax-cart')) return;
    e.preventDefault();
    submitCartForm(form);
});

document.addEventListener('change', function (e) {
    const input = e.target;
    if (!(input instanceof HTMLInputElement)) return;
    const form = input.closest('form[data-ajax-cart]');
    if (!form) return;
    if (form.getAttribute('data-cart-action') !== 'set') return;
    submitCartForm(form);
});

