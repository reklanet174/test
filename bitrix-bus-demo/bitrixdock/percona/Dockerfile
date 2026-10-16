ARG MYSQL_VERSION=8.4
FROM percona/percona-server:${MYSQL_VERSION}

LABEL org.opencontainers.image.source="https://github.com/bitrixdock/bitrixdock"

COPY --chown=mysql:root --chmod=0664 my.cnf /etc/my.cnf.d/my.cnf
