FROM percona:5.7

COPY config/my.cnf /etc/my.cnf.d/my.cnf

ADD entrypoint.sql /docker-entrypoint-initdb.d/1-entrypoint.sql
