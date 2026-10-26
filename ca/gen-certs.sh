#!/usr/bin/env bash
# Generate a stand-in "distrusted" CA and a leaf cert for the TLS canary.
# Zero dependencies beyond OpenSSL. Run once, at home.
#
# This is a STAND-IN, not a real eMudhra cert. For the authentic "private key
# is the number 4" gag, run gen-key4.py instead (needs: pip install cryptography).
set -euo pipefail
cd "$(dirname "$0")"

SUBJ_CA="/CN=Demo Dodgy CA G3 (stand-in, not real eMudhra)"
SUBJ_LEAF="/CN=tls-canary"
SAN="subjectAltName=DNS:localhost,DNS:tls-canary"

echo "==> CA key + self-signed root"
openssl ecparam -name secp384r1 -genkey -noout -out badca.key
openssl req -x509 -new -key badca.key -sha384 -days 3650 \
  -subj "$SUBJ_CA" \
  -addext "basicConstraints=critical,CA:TRUE" \
  -addext "keyUsage=critical,keyCertSign,cRLSign" \
  -out badca.pem

echo "==> leaf key + CSR"
openssl ecparam -name secp384r1 -genkey -noout -out leaf.key
openssl req -new -key leaf.key -subj "$SUBJ_LEAF" -out leaf.csr

echo "==> sign leaf with the CA"
openssl x509 -req -in leaf.csr -CA badca.pem -CAkey badca.key -CAcreateserial \
  -days 3650 -sha384 \
  -extfile <(printf "%s\nbasicConstraints=CA:FALSE\nkeyUsage=critical,digitalSignature\nextendedKeyUsage=serverAuth\n" "$SAN") \
  -out leaf.pem
rm -f leaf.csr badca.srl

# The "outdated bundle" the app trusts: it still contains the bad root.
cp badca.pem bundle-outdated.pem

echo "==> done. Files: badca.pem badca.key leaf.pem leaf.key bundle-outdated.pem"
