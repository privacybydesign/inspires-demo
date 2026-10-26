Title: App vertrouwt een ingetrokken CA via een verouderde bundel (eMudhra G3)

De app doet uitgaande TLS-calls en verifieert tegen een verouderde CA-bundel die
een ingetrokken intermediate CA nog vertrouwt. Dit speelt het eMudhra
G3-sleutelcompromis na: acht intermediates onder de G3-root hadden P-384-sleutels
met een waarde onder de 256 (de sleutel van "EM OV TLS CA - G3C" was 4), en zijn
op 17–20 oktober 2026 door browsers geblokkeerd en door eMudhra ingetrokken.

Zie `/health.php`: het `emudhra`-lampje staat op rood omdat de verbinding met de
canary slaagt — we vertrouwen dus een CA die we zouden moeten weigeren.

## Wat er moet gebeuren
- Laat `CA_BUNDLE` in `app/config.php` naar de systeem-truststore wijzen
  (`/etc/ssl/certs/ca-certificates.crt`) in plaats van `bundle-outdated.pem`.
  De bad root zit niet in de systeemstore, dus de canary wordt dan geweigerd.

## Klaar wanneer
- `/health.php` geeft voor `emudhra` `"ok": true` (certificaat geweigerd).

NB: alleen de bundel. Raak de PHP-versie (issue #1) niet aan. Geen exploitcode.
