FROM wordpress

RUN set -x && \
    apt update && \
    export DEBIAN_FRONTEND=noninteractive && \
    apt install -y mariadb-server && \
    touch /var/log/access.log

RUN /usr/local/bin/docker-entrypoint.sh
