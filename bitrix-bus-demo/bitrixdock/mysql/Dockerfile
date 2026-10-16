ARG MYSQL_VERSION=8.4
FROM --platform=linux/amd64 mysql:${MYSQL_VERSION}

LABEL org.opencontainers.image.source="https://github.com/bitrixdock/bitrixdock"

MAINTAINER vitams

COPY my.cnf /etc/mysql/conf.d/my.cnf

CMD ["mysqld"]

EXPOSE 3306
