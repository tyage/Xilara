FROM node
MAINTAINER tyage <namatyage@gmail.com>

ARG SRCDIR="/usr/local/collector"

ADD ./ ${SRCDIR}

RUN set -x && \
	cd ${SRCDIR} && \
	npm i

EXPOSE 8000

WORKDIR ${SRCDIR}
CMD node collector.js
