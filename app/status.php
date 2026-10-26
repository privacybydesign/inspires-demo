<?php
require_once __DIR__ . '/config.php';

/**
 * Bug #1: is the running PHP at or above its branch's patched release?
 */
function check_php(): array
{
    $current = PHP_VERSION;                       // honest: the real running version
    [$maj, $min] = explode('.', $current);
    $branch = "$maj.$min";
    $min_patched = PATCHED_PHP[$branch] ?? null;

    if ($min_patched === null) {
        return [
            'ok'      => false,
            'current' => $current,
            'detail'  => "unknown branch $branch — treat as unpatched",
        ];
    }

    $ok = version_compare($current, $min_patched, '>=');
    return [
        'ok'       => $ok,
        'current'  => $current,
        'required' => $min_patched,
        'detail'   => $ok
            ? "PHP $current is patched"
            : "PHP $current is below $min_patched (PHP2Shell / CERT-EU 2026-018)",
    ];
}

/**
 * Bug #2: do we (wrongly) trust the distrusted CA?
 * We make an outgoing TLS call to the canary, verifying against CA_BUNDLE.
 *  - connection verifies  -> we TRUST the distrusted CA  -> BAD (red)
 *  - connection rejected  -> we refuse it                -> GOOD (green)
 */
function check_emudhra(): array
{
    $ch = curl_init(CANARY_URL);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_CAINFO         => CA_BUNDLE,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT        => 8,
    ]);
    $body      = curl_exec($ch);
    $err       = curl_error($ch);
    $errno     = curl_errno($ch);
    unset($ch);   // curl_close() is a no-op and deprecated since PHP 8.5

    // Decide on the TLS verdict, not on whether the HTTP body read cleanly.
    //  - 60/51: peer verification failed  -> we rejected it            -> GOOD (green)
    //  - body read, or a post-handshake read error (56): cert accepted -> BAD  (red)
    //  - anything else (couldn't connect/resolve/timeout): setup issue -> red, say so
    $verify_failed = in_array($errno, [51, 60], true);
    $accepted      = ($body !== false) || $errno === 56;  // 56 = eof after a verified handshake

    if ($verify_failed) {
        $ok = true;
        $detail = 'certificate correctly rejected';
    } elseif ($accepted) {
        $ok = false;
        $detail = 'TLS verified against a distrusted CA — bundle still trusts eMudhra G3';
    } else {
        $ok = false;
        $detail = "canary unreachable (curl $errno: $err) — check the demo setup";
    }

    return [
        'ok'     => $ok,
        'bundle' => CA_BUNDLE,
        'canary' => CANARY_URL,
        'detail' => $detail,
    ];
}

function demo_status(): array
{
    $php = check_php();
    $em  = check_emudhra();
    return [
        'healthy' => $php['ok'] && $em['ok'],
        'checks'  => ['php' => $php, 'emudhra' => $em],
    ];
}
