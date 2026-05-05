<?php
if (!function_exists('mm_admin_badge_active')) {
    function mm_admin_badge_active(string $current, string $expected): string {
        return $current === $expected ? 'btn btn-warning text-dark fw-semibold' : 'btn btn-outline-light';
    }
}

if (!function_exists('mm_render_series_admin_nav')) {
    function mm_render_series_admin_nav(string $active, array $context = []): void {
        $idSerie = isset($context['id_serie']) ? (int)$context['id_serie'] : 0;
        $idTemporada = isset($context['id_temporada']) ? (int)$context['id_temporada'] : 0;
        $serieLabel = $context['serie_label'] ?? '';
        $temporadaLabel = $context['temporada_label'] ?? '';
        ?>
        <div class="rounded-4 p-3 mb-4 admin-subnav-box">
            <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
                <div class="small text-light-emphasis">
                    <a href="index.php" class="text-decoration-none text-light-emphasis">Admin</a>
                    <span class="mx-1">/</span>
                    <a href="series_panel.php" class="text-decoration-none text-light-emphasis">Series</a>

                    <?php if ($serieLabel !== ''): ?>
                        <span class="mx-1">/</span>
                        <span><?= htmlspecialchars($serieLabel) ?></span>
                    <?php endif; ?>

                    <?php if ($temporadaLabel !== ''): ?>
                        <span class="mx-1">/</span>
                        <span><?= htmlspecialchars($temporadaLabel) ?></span>
                    <?php endif; ?>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="series_panel.php" class="<?= mm_admin_badge_active($active, 'panel') ?>">Resumen</a>
                    <a href="series.php" class="<?= mm_admin_badge_active($active, 'series') ?>">Series</a>
                    <a href="temporadas.php<?= $idSerie > 0 ? '?id_serie=' . $idSerie : '' ?>" class="<?= mm_admin_badge_active($active, 'temporadas') ?>">Temporadas</a>
                    <a href="episodios.php<?= $idTemporada > 0 ? '?id_temporada=' . $idTemporada : '' ?>" class="<?= mm_admin_badge_active($active, 'episodios') ?>">Episodios</a>
                    <a href="criticas_series.php" class="<?= mm_admin_badge_active($active, 'criticas') ?>">Críticas</a>
                </div>
            </div>
        </div>
        <?php
    }
}