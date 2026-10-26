<?php require_once __DIR__ . '/status.php'; $s = demo_status(); ?>
<!doctype html>
<html lang="nl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Yivi demo</title>
  <style>
    :root { color-scheme: light dark; }
    body { font: 16px/1.5 system-ui, sans-serif; max-width: 42rem; margin: 3rem auto; padding: 0 1rem; }
    h1 { font-size: 1.5rem; }
    .lamp { display: flex; align-items: center; gap: .75rem; padding: .75rem 1rem; border: 1px solid #8884; border-radius: .5rem; margin: .5rem 0; }
    .dot { width: .9rem; height: .9rem; border-radius: 50%; flex: none; }
    .ok  .dot { background: #1a9e4b; } .ok  { border-color: #1a9e4b88; }
    .bad .dot { background: #d23; }    .bad { border-color: #d2333388; }
    .detail { color: #8889; font-size: .85rem; }
    code { font-family: ui-monospace, monospace; }
  </style>
</head>
<body>
  <h1>Yivi demo-app</h1>
  <p>Een doodgewone demo. Twee dingen zijn alleen niet in orde.</p>

  <?php foreach ($s['checks'] as $name => $c): ?>
    <div class="lamp <?= $c['ok'] ? 'ok' : 'bad' ?>">
      <span class="dot"></span>
      <div>
        <strong><?= $name === 'php' ? 'PHP-versie' : 'Certificaatvertrouwen' ?></strong>
        <div class="detail"><?= htmlspecialchars($c['detail']) ?></div>
      </div>
    </div>
  <?php endforeach; ?>

  <p class="detail">Statusdata: <code>/health.php</code></p>
</body>
</html>
