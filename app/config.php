<?php
// Central config. The two demo bugs live here and in the Dockerfile.

// --- Bug #2 (eMudhra): which CA bundle do we trust for outgoing TLS? ---
// VULNERABLE: an outdated bundle that still trusts the distrusted CA.
// FIX (issue #2): point this at the system trust store, e.g.
//   '/etc/ssl/certs/ca-certificates.crt'
define('CA_BUNDLE', __DIR__ . '/../ca/bundle-outdated.pem');

// The local TLS canary that presents a cert from the (distrusted) CA.
// Reachable name inside docker-compose is 'tls-canary'; 'localhost' when bare.
define('CANARY_URL', getenv('CANARY_URL') ?: 'https://tls-canary:8443/');

// --- Bug #1 (PHP2Shell): minimum patched PHP per minor branch ---
// CERT-EU 2026-018. Anything below its branch minimum is vulnerable.
const PATCHED_PHP = [
    '8.2' => '8.2.35',
    '8.3' => '8.3.36',
    '8.4' => '8.4.27',
    '8.5' => '8.5.12',
];
