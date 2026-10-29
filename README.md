# inspires-demo

Demo-webapp voor het Caesar Inspires-praatje (4 nov 2026). De app heeft twee
bewuste gebreken die precies de twee incidenten van 21 oktober naspelen:

1. **PHP2Shell** — de app draait op een PHP-versie onder de gepatchte release
   (CERT-EU 2026-018, CVE-2026-109618 / -109619, CVSS 9.2).
2. **eMudhra** — de app vertrouwt een ingetrokken intermediate CA via een
   verouderde CA-bundel (eMudhra G3-sleutelcompromis; de sleutel van
   "EM OV TLS CA - G3C" was letterlijk het getal 4).

Tijdens het praatje roep ik dobby aan op twee issues. Hij maakt per gebrek een
PR; bij een merge naar `main` herbouwt een lokale watcher de container en
springen de lampjes op `/health` van rood naar groen.

**Alles draait lokaal op de laptop. Niets gaat naar buiten.** De eMudhra-check
praat met een lokaal TLS-servertje (`tls-canary`), niet met de publieke
testpagina. Er staat geen exploit in deze repo: de app detecteert alleen de twee
misconfiguraties en de fix haalt ze weg.

## Onderdelen

| Map | Wat |
|-----|-----|
| `app/` | De PHP-demo-app: landingspagina + `/health` met de twee lampjes |
| `ca/` | Scripts die de stand-in "foute CA" + het testcert maken |
| `tls-canary/` | Lokaal HTTPS-servertje dat een cert uit die foute CA presenteert |
| `watcher/` | Lokale "deploy": ziet een merge op `main`, herbouwt en herstart |
| `issues/` | De tekst van de twee GitHub-issues voor dobby |

## Eenmalig klaarzetten (thuis, met internet)

```sh
# 1. certificaten genereren
cd ca
./gen-certs.sh            # zero-deps, OpenSSL. Stand-in CA met een gewone sleutel
# of, voor de echte "sleutel = 4"-gag (vereist: pip install cryptography):
# python3 gen-key4.py

# 2. draaien
cd ..
docker compose up -d --build
open http://localhost:8080/
# poort bezet? APP_PORT=8765 docker compose up -d --build
```

`/health` toont dan twee rode lampjes. Dat is de beginstand voor de demo.

## De fix (wat dobby doet)

- **Issue #1:** verhoog `PHP_TAG` in `Dockerfile` naar een gepatchte versie
  (8.4.27 / 8.5.12). → php-lampje groen.
- **Issue #2:** laat `CA_BUNDLE` in `app/config.php` naar de systeem-truststore
  wijzen in plaats van `bundle-outdated.pem`. → emudhra-lampje groen.

## Live-flow

```sh
# op de laptop, naast de browser met de slides:
./watcher/watch-and-deploy.sh      # poll → rebuild → restart bij een merge
```

## Reserveplan

Als het om minuut 25 nog niet groen is: laat de openstaande PR's zien en speel
een opname van een geslaagde oefenrun af. Draai daarom vooraf minstens 5 runs en
noteer duur en slagingspercentage.
