FROM php:5.6-cli
COPY . /usr/src/fitbox
WORKDIR /usr/src/fitbox
CMD [ "php", "./index.php" ]