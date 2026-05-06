<?php
$footerScriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
if (basename($footerScriptDir) === 'pages') {
    $footerBaseUrl = dirname($footerScriptDir);
} elseif (basename($footerScriptDir) === 'admin') {
    $footerBaseUrl = dirname($footerScriptDir);
} else {
    $footerBaseUrl = $footerScriptDir;
}
$footerBaseUrl = rtrim($footerBaseUrl, '/');
?>
<footer class="footer-cine py-4 mt-5">
  <div class="container text-center">
    <small>&copy; <?= date('Y') ?> MMCinema</small>
  </div>
</footer>
<script src="<?= htmlspecialchars($footerBaseUrl) ?>/assets/js/mobile-netflix.js"></script>
