Title: PHP draait onder de gepatchte release (PHP2Shell, CERT-EU 2026-018)

De app draait op een PHP-versie die onder de gepatchte release ligt. CERT-EU
2026-018 meldt twee kritieke lekken (CVE-2026-109618, CVE-2026-109619, CVSS 9.2,
mogelijk ongeauthenticeerde remote code execution). Zie `/health.php`: het
`php`-lampje staat op rood.

## Wat er moet gebeuren
- Verhoog `PHP_TAG` in `Dockerfile` naar een gepatchte versie (8.4.27 of 8.5.12,
  of de nieuwste patch binnen de branch die we draaien).

## Klaar wanneer
- `/health.php` geeft voor `php` `"ok": true`.
- De draaiende versie is >= de branch-drempel in `app/config.php`.

NB: alleen de versie-bump. Raak de certificaat-check (issue #2) niet aan.
