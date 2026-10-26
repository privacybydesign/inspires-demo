#!/usr/bin/env python3
"""Tiny local HTTPS server that presents the canary leaf cert.

The leaf chains to the stand-in distrusted CA. A client that trusts that CA
(the app's outdated bundle) will connect; a client on the system trust store
will refuse. Nothing here leaves the laptop.
"""
import http.server
import ssl

CERT = "/ca/leaf.pem"
KEY = "/ca/leaf.key"
PORT = 8443


class Handler(http.server.BaseHTTPRequestHandler):
    protocol_version = "HTTP/1.1"   # so clients get a clean, framed response

    def do_GET(self):
        body = b"canary ok\n"
        self.send_response(200)
        self.send_header("Content-Type", "text/plain")
        self.send_header("Content-Length", str(len(body)))
        self.send_header("Connection", "close")
        self.end_headers()
        self.wfile.write(body)

    def log_message(self, *args):
        pass  # quiet


def main():
    ctx = ssl.SSLContext(ssl.PROTOCOL_TLS_SERVER)
    ctx.load_cert_chain(CERT, KEY)
    httpd = http.server.HTTPServer(("0.0.0.0", PORT), Handler)
    httpd.socket = ctx.wrap_socket(httpd.socket, server_side=True)
    print(f"tls-canary listening on :{PORT}")
    httpd.serve_forever()


if __name__ == "__main__":
    main()
