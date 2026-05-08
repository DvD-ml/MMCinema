document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('criticasSearchForm');
    const input = document.getElementById('criticasSearchInput');
    const filterInput = document.getElementById('criticasFiltroInput');
    const status = document.getElementById('criticasLiveStatus');
    const peliculasResults = document.getElementById('criticasPeliculasResults');
    const seriesResults = document.getElementById('criticasSeriesResults');
    const tabButtons = document.querySelectorAll('.criticas-tab-btn');
    const filterButtons = document.querySelectorAll('.criticas-filter-btn');

    if (!form || !input || !peliculasResults || !seriesResults) {
        return;
    }

    const initialHtml = {
        peliculas: peliculasResults.innerHTML,
        series: seriesResults.innerHTML
    };

    let activeTab = input.dataset.initialTab || 'peliculas';
    let activeFilter = filterInput ? filterInput.value : 'todo';
    let timer = null;
    let controller = null;

    const escapeHtml = (value) => String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

    const formatDate = (value) => {
        if (!value) return '';
        const date = new Date(String(value).replace(' ', 'T'));
        if (Number.isNaN(date.getTime())) return escapeHtml(value);

        return date.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    const stars = (score) => {
        const rating = Number(score) || 0;
        let html = '<div class="critica-stars-display">';
        for (let i = 1; i <= 5; i += 1) {
            html += `<span class="star ${i <= rating ? 'on' : 'off'}">&#9733;</span>`;
        }
        html += '</div>';
        return html;
    };

    const card = (item, tipo) => {
        const title = escapeHtml(item.titulo || 'Desconocida');
        const username = escapeHtml(item.username || 'Anonimo');
        const content = escapeHtml(item.contenido || '').replace(/\n/g, '<br>');
        const obraId = Number(item.obra_id) || 0;
        const link = tipo === 'series'
            ? `serie.php?id=${obraId}`
            : `pelicula.php?id=${obraId}`;
        const poster = item.poster
            ? (tipo === 'series' ? `../${escapeHtml(item.poster)}` : `../assets/img/posters/${escapeHtml(item.poster)}`)
            : '';

        return `
            <div class="critica-card mb-4 critica-card-live">
                <div class="row g-3">
                    <div class="col-auto">
                        ${poster
                            ? `<div class="critica-poster-wrap"><img src="${poster}" alt="${title}" class="critica-poster"></div>`
                            : '<div class="critica-poster-wrap critica-poster-placeholder"><div class="placeholder-content"><span>Sin poster</span></div></div>'
                        }
                    </div>
                    <div class="col">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                            <div>
                                <strong>${username}</strong>
                                <div class="small text-muted">${formatDate(item.creado)}</div>
                            </div>
                            ${stars(item.puntuacion)}
                        </div>
                        <p class="critica-obra mb-2">
                            <strong>${tipo === 'series' ? 'Serie' : 'Pelicula'}:</strong>
                            ${obraId ? `<a href="${link}" class="text-decoration-none">${title}</a>` : title}
                        </p>
                        <p class="mb-0 critica-contenido">${content}</p>
                    </div>
                </div>
            </div>
        `;
    };

    const render = (data) => {
        const target = data.tipo === 'series' ? seriesResults : peliculasResults;
        const label = data.tipo === 'series' ? 'series' : 'peliculas';

        if (!data.resultados.length) {
            target.innerHTML = '<div class="alert alert-info text-center">No se encontraron criticas relacionadas.</div>';
        } else {
            target.innerHTML = data.resultados.map((item) => card(item, data.tipo)).join('');
        }

        status.textContent = `${data.total} resultado${data.total === 1 ? '' : 's'} en ${label}`;
    };

    const search = async () => {
        const value = input.value.trim();
        const target = activeTab === 'series' ? seriesResults : peliculasResults;

        if (value.length === 0) {
            target.innerHTML = initialHtml[activeTab];
            status.textContent = '';
            return;
        }

        if (controller) {
            controller.abort();
        }
        controller = new AbortController();

        status.textContent = 'Buscando...';

        const params = new URLSearchParams({ tipo: activeTab, buscar: value, filtro: activeFilter });

        try {
            const response = await fetch(`${form.dataset.api}?${params.toString()}`, {
                signal: controller.signal,
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) {
                throw new Error('search failed');
            }

            render(await response.json());
        } catch (error) {
            if (error.name !== 'AbortError') {
                status.textContent = 'No se pudo buscar ahora mismo.';
            }
        }
    };

    input.addEventListener('input', function () {
        window.clearTimeout(timer);
        timer = window.setTimeout(search, 220);
    });

    tabButtons.forEach((button) => {
        button.addEventListener('click', function () {
            activeTab = this.dataset.tab || activeTab;
        }, { capture: true });
    });

    filterButtons.forEach((button) => {
        button.addEventListener('click', function () {
            activeFilter = this.dataset.filtro || 'todo';
            if (filterInput) {
                filterInput.value = activeFilter;
            }

            filterButtons.forEach((item) => item.classList.toggle('active', item === this));

            const url = new URL(window.location.href);
            url.searchParams.set('filtro', activeFilter);
            url.searchParams.set('tab', activeTab);
            url.searchParams.set('pp', '1');
            url.searchParams.set('ps', '1');
            if (input.value.trim()) {
                url.searchParams.set('buscar', input.value.trim());
                window.history.replaceState({}, '', url.toString());
                search();
            } else {
                url.searchParams.delete('buscar');
                window.location.href = url.toString();
            }
        });
    });
});
