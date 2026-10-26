# Bug #1 (PHP2Shell): this base image is BELOW the patched release.
# Fix (issue #1): bump PHP_TAG to a patched version, e.g. 8.4.27-apache.
#
# NB: pick a tag that actually exists on Docker Hub when you test. The "below
# patched" and "patched" numbers on the slides are 8.4.x / 8.5.x; use whatever
# real patch tags exist at build time. The /health check reads the REAL running
# version, so the lamp follows the base image honestly.
ARG PHP_TAG=8.4.1-apache
FROM php:${PHP_TAG}

# Quiet the version-leak header (cosmetic; not the demo's point).
RUN { echo "expose_php = Off"; } > /usr/local/etc/php/conf.d/zz-demo.ini

COPY app/ /var/www/html/
# Only the trust bundle the app reads — never the CA private key.
COPY ca/bundle-outdated.pem /var/www/ca/bundle-outdated.pem

# DocumentRoot stays /var/www/html; config.php references ../ca.
EXPOSE 80
