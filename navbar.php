<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$paginaActual = basename($_SERVER['PHP_SELF']);

function mm_nav_active(array $archivos, string $paginaActual): string {
    return in_array($paginaActual, $archivos, true) ? 'active' : '';
}
?>
<nav class="navbar navbar-expand-lg shadow-sm" style="background:#0b0f1a;">
  <div class="container">
    <a class="navbar-brand fw-bold text-white" href="index.php" style="text-decoration:none;">
      <img src="img/logo2.png" alt="MMCinema" width="110">
    </a>

    <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="menu">
      <ul class="navbar-nav ms-auto align-items-lg-center">

        <li class="nav-item">
          <a class="nav-link text-white <?= mm_nav_active(['cartelera.php', 'pelicula.php', 'reservar_entradas.php', 'ticket.php'], $paginaActual) ?>" href="cartelera.php">Cartelera</a>
        </li>

        <li class="nav-item">
          <a class="nav-link text-white <?= mm_nav_active(['proximamente.php'], $paginaActual) ?>" href="proximamente.php">Próximamente</a>
        </li>

        <li class="nav-item">
          <a class="nav-link text-white <?= mm_nav_active(['series.php', 'serie.php'], $paginaActual) ?>" href="series.php">Streaming</a>
        </li>

        <li class="nav-item">
          <a class="nav-link text-white <?= mm_nav_active(['noticias.php', 'noticia.php'], $paginaActual) ?>" href="noticias.php">Noticias</a>
        </li>

        <li class="nav-item">
          <a class="nav-link text-white <?= mm_nav_active(['criticas.php'], $paginaActual) ?>" href="criticas.php">Críticas</a>
        </li>

        <?php if (!empty($_SESSION['usuario_id'])): ?>
          <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
            <li class="nav-item">
              <a class="nav-link text-warning fw-bold" href="admin/index.php">Panel Admin</a>
            </li>
          <?php endif; ?>

          <li class="nav-item">
            <a class="nav-link text-white <?= mm_nav_active(['perfil.php'], $paginaActual) ?>" href="perfil.php">
              👤 <?= htmlspecialchars($_SESSION['usuario'] ?? 'Perfil') ?>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-danger" href="logout.php">Salir</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link text-white <?= mm_nav_active(['login.php'], $paginaActual) ?>" href="login.php">Iniciar sesión</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white <?= mm_nav_active(['registro.php'], $paginaActual) ?>" href="registro.php">Registro</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>