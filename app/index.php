<?php
// The landing page is a homemade bitcoin wallet: the two demo bugs are not on it.
// Only the footer gives them away, to whoever looks closely (dobby, on stage).
// The verdicts with lamps live at /health.php.
require_once __DIR__ . '/status.php';
$s = demo_status();
$php = $s['checks']['php'];
$node = [
    'accepted'    => 'verbonden',
    'rejected'    => 'certificaat geweigerd',
    'unreachable' => 'niet bereikbaar',
][$s['checks']['emudhra']['tls']];
?>
<!doctype html>
<html lang="nl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mijn bitcoin wallet</title>
  <style>
    html, body { height: 100%; margin: 0; }
    body { display: flex; flex-direction: column; font: 16px/1.5 system-ui, sans-serif; color: #2b2b2b; background: #fff; }
    header { display: flex; align-items: center; gap: 10px; padding: 12px 24px; border-bottom: 1px solid #e5e5e5; font-weight: 700; }
    .logo { display: grid; place-items: center; width: 28px; height: 28px; border-radius: 50%; background: #F7931A; color: #fff; font-size: 18px; transform: rotate(14deg); }
    main { flex: 1; display: flex; flex-direction: column; justify-content: center; gap: 14px; padding: 0 24px; font-variant-numeric: tabular-nums; }
    .label { font-size: 14px; color: #777; }
    .balance { margin: 0; font-size: 52px; font-weight: 800; line-height: 1; }
    .balance small { font-size: inherit; font-weight: 400; color: #bbb; }
    .balance span { color: #F7931A; }
    .actions { display: flex; gap: 10px; }
    .button { padding: 8px 18px; border-radius: 8px; background: #F7931A; color: #fff; font-weight: 600; text-decoration: none; }
    .button.ghost { background: none; color: #2b2b2b; box-shadow: inset 0 0 0 1.5px #ddd; }
    ul { margin: 0; padding: 0; list-style: none; font-size: 14px; }
    li { display: flex; gap: 16px; padding: 4px 0; }
    li b { width: 10.5em; white-space: nowrap; color: #2a8a4a; }
    li i { margin-left: auto; font-style: normal; color: #999; }
    footer { display: flex; justify-content: space-between; padding: 12px 24px; border-top: 1px solid #e5e5e5; font-size: 14px; color: #777; }
  </style>
</head>
<body>
  <header><span class="logo">₿</span> Mijn zelfgebouwde bitcoin wallet</header>
  <main>
    <div>
      <div class="label">Saldo</div>
      <p class="balance">12<small>,00000000</small> <span>BTC</span></p>
    </div>
    <div class="actions">
      <a class="button" href="#">Versturen</a>
      <a class="button ghost" href="#">Ontvangen</a>
    </div>
    <ul>
      <li><b>+10,00000000 BTC</b> Gemined op de zolder-pc <i>mei 2010</i></li>
      <li><b>+2,00000000 BTC</b> Weddenschap gewonnen <i>jun 2011</i></li>
    </ul>
  </main>
  <footer>
    <span>Node: <?= $node ?></span>
    <span>PHP <?= htmlspecialchars($php['current']) ?></span>
  </footer>
</body>
</html>
