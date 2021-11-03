FROM python:3.9.6-alpine3.14

RUN apk update && apk --no-cache add g++

WORKDIR /usr/src/kunlabo

COPY ./python/requirements.txt ./
RUN pip install --no-cache-dir -r requirements.txt
