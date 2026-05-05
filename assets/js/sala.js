/**
 * SALA.JS - Seat Selection Management
 * Handles interactive seat selection for ticket booking
 */

document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('cantidad');
    const selCountEl = document.getElementById('selCount');
    const maxCountEl = document.getElementById('maxCount');
    const asientosJson = document.getElementById('asientos_json');
    const btnConfirmar = document.getElementById('btnConfirmar');
    const seatButtons = document.querySelectorAll('.seat.free');

    let selected = [];

    function maxCount() {
        return parseInt(quantityInput?.value || 1);
    }

    function updateUI() {
        selCountEl.textContent = selected.length;
        maxCountEl.textContent = maxCount();
        asientosJson.value = JSON.stringify(selected);
        btnConfirmar.disabled = (selected.length !== maxCount());
    }

    quantityInput?.addEventListener('change', () => {
        const m = maxCount();
        if (selected.length > m) {
            const toRemove = selected.slice(m);
            toRemove.forEach(code => {
                const btn = document.querySelector(`.seat.free[data-seat="${code}"]`);
                if (btn) btn.classList.remove('selected');
            });
            selected = selected.slice(0, m);
        }
        updateUI();
    });

    seatButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const code = btn.getAttribute('data-seat');
            const m = maxCount();

            if (selected.includes(code)) {
                selected = selected.filter(s => s !== code);
                btn.classList.remove('selected');
            } else if (selected.length < m) {
                selected.push(code);
                btn.classList.add('selected');
            }
            updateUI();
        });
    });

    updateUI();
});
