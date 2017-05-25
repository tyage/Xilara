FROM alpine
MAINTAINER tyage <namatyage@gmail.com>

ARG REPOSITORY="https://github.com/webmin/webmin.git"
ARG BRANCH="1.830"
ARG SRCDIR="/webmin"

RUN set -x && \
  apk upgrade --update && \
  apk add --update \
    git \
    perl && \
  git clone ${REPOSITORY} ${SRCDIR} && \
  cd ${SRCDIR} && \
  git checkout ${BRANCH} && \
  echo -e "\n\n\n80\nadmin\nadmin\nadmin\ny\n" | ./setup.sh && \
  touch /var/webmin/miniserv.log

CMD /etc/webmin/start && tail -f /var/webmin/miniserv.log

EXPOSE 80
