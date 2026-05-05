/**
 * Admin Search - Búsqueda en tiempo real para tablas
 * Permite filtrar filas de tabla mientras escribes
 */

document.addEventListener('DOMContentLoaded', function() {
    // Buscar todos los inputs de búsqueda
    const searchInputs = document.querySelectorAll('[data-search-table]');
    
    searchInputs.forEach(input => {
        input.addEventListener('keyup', function() {
            const tableId = this.getAttribute('data-search-table');
            const table = document.getElementById(tableId);
            
            if (!table) return;
            
            const searchTerm = this.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const isVisible = text.includes(searchTerm);
                
                row.style.display = isVisible ? '' : 'none';
                if (isVisible) visibleCount++;
            });
            
            // Mostrar mensaje si no hay resultados
            const noResults = table.querySelector('.admin-no-results');
            if (noResults) {
                noResults.style.display = visibleCount === 0 ? '' : 'none';
            }
        });
    });
});

/**
 * Filtro por select
 * Filtra tabla por columna específica
 */
function filterTableByColumn(tableId, columnIndex, filterValue) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const rows = table.querySelectorAll('tbody tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const cell = row.cells[columnIndex];
        if (!cell) return;
        
        const cellText = cell.textContent.toLowerCase();
        const isVisible = filterValue === '' || cellText.includes(filterValue.toLowerCase());
        
        row.style.display = isVisible ? '' : 'none';
        if (isVisible) visibleCount++;
    });
    
    // Mostrar mensaje si no hay resultados
    const noResults = table.querySelector('.admin-no-results');
    if (noResults) {
        noResults.style.display = visibleCount === 0 ? '' : 'none';
    }
}

/**
 * Limpiar búsqueda
 */
function clearTableSearch(inputId, tableId) {
    const input = document.getElementById(inputId);
    if (input) {
        input.value = '';
        input.dispatchEvent(new Event('keyup'));
    }
}
