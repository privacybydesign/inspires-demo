# inspires-demo

Demo app for a live talk. On stage dobby fixes issue #1 (PHP version) and
issue #2 (CA bundle) at the same time, in two separate PRs that are merged one
after the other while the audience watches the lamps on `/health.php` turn
green.

## Rules for fix PRs

Two runs work on this repo in parallel, both starting from `main`. A file that
both PRs touch turns the second merge into a conflict on stage. So:

- One issue per PR, and only the file that issue names:
  - #1 (PHP version): `Dockerfile`, only the `PHP_TAG` default.
  - #2 (CA bundle): `app/config.php`, only `CA_BUNDLE`.
- Do not change this file, `README.md`, `issues/`, `docker-compose.yml`, the
  workflow, `ca/` or `tls-canary/` in a fix PR, not even to record what you
  learned. Put such notes in the PR description instead.
- The PHP tag must exist on Docker Hub as `php:<x.y.z>-apache`. Pick the lowest
  patch at or above the threshold in `app/config.php` (`PATCHED_PHP`) for the
  branch that is running.
- No exploit code. The app only detects the two misconfigurations.
- Keep the PR description short: what changed, which lamp turns green, and the
  risk. A patch bump of the base image is low risk; changing which CAs the app
  trusts deserves a human review.

## Checking a fix locally

```sh
ca/gen-certs.sh                     # once; the certs are gitignored
APP_PORT=8080 docker compose up -d --build
curl -s localhost:8080/health.php   # "ok": true for the lamp you fixed
```

The version check reads the PHP version that is really running, so only a
rebuilt image turns the PHP lamp green.
