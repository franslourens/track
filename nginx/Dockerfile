FROM nginx

ARG APP_GROUP_ID=1000
ARG APP_GROUP_NAME="app"

RUN addgroup --system --gid ${APP_GROUP_ID} ${APP_GROUP_NAME}
RUN addgroup nginx ${APP_GROUP_NAME}

COPY default.conf /etc/nginx/conf.d/default.conf 

EXPOSE 80