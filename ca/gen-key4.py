#!/usr/bin/env python3
"""Authentic version of the gag: a CA whose P-384 private key is literally 4.

This reproduces the eMudhra "EM OV TLS CA - G3C" detail — a private scalar far
below 256, recoverable in a fraction of a second. We only USE it to sign a leaf
for the local canary; we never forge anything.

Requires: pip install cryptography
Run once, at home. Produces the same files as gen-certs.sh.
"""
import datetime as dt
from pathlib import Path

from cryptography import x509
from cryptography.x509.oid import NameOID, ExtendedKeyUsageOID
from cryptography.hazmat.primitives import hashes, serialization
from cryptography.hazmat.primitives.asymmetric import ec

HERE = Path(__file__).resolve().parent
NOW = dt.datetime.now(dt.timezone.utc)
TEN_YEARS = NOW + dt.timedelta(days=3650)


def write_key(path: Path, key) -> None:
    path.write_bytes(key.private_bytes(
        serialization.Encoding.PEM,
        serialization.PrivateFormat.PKCS8,
        serialization.NoEncryption(),
    ))


def write_cert(path: Path, cert) -> None:
    path.write_bytes(cert.public_bytes(serialization.Encoding.PEM))


# --- The punchline: d = 4 on P-384 ---
ca_key = ec.derive_private_key(4, ec.SECP384R1())
ca_name = x509.Name([x509.NameAttribute(
    NameOID.COMMON_NAME, "Demo Dodgy CA G3 (private key = 4, stand-in)")])

ca_cert = (
    x509.CertificateBuilder()
    .subject_name(ca_name).issuer_name(ca_name)
    .public_key(ca_key.public_key())
    .serial_number(x509.random_serial_number())
    .not_valid_before(NOW).not_valid_after(TEN_YEARS)
    .add_extension(x509.BasicConstraints(ca=True, path_length=None), critical=True)
    .add_extension(x509.KeyUsage(
        digital_signature=False, content_commitment=False, key_encipherment=False,
        data_encipherment=False, key_agreement=False, key_cert_sign=True,
        crl_sign=True, encipher_only=False, decipher_only=False), critical=True)
    .sign(ca_key, hashes.SHA384())
)

leaf_key = ec.generate_private_key(ec.SECP384R1())
leaf_cert = (
    x509.CertificateBuilder()
    .subject_name(x509.Name([x509.NameAttribute(NameOID.COMMON_NAME, "tls-canary")]))
    .issuer_name(ca_name)
    .public_key(leaf_key.public_key())
    .serial_number(x509.random_serial_number())
    .not_valid_before(NOW).not_valid_after(TEN_YEARS)
    .add_extension(x509.SubjectAlternativeName(
        [x509.DNSName("localhost"), x509.DNSName("tls-canary")]), critical=False)
    .add_extension(x509.BasicConstraints(ca=False, path_length=None), critical=True)
    .add_extension(x509.ExtendedKeyUsage([ExtendedKeyUsageOID.SERVER_AUTH]), critical=False)
    .sign(ca_key, hashes.SHA384())
)

write_key(HERE / "badca.key", ca_key)
write_cert(HERE / "badca.pem", ca_cert)
write_key(HERE / "leaf.key", leaf_key)
write_cert(HERE / "leaf.pem", leaf_cert)
(HERE / "bundle-outdated.pem").write_bytes((HERE / "badca.pem").read_bytes())

print("done. The CA private key is 4. Files written to", HERE)
