# 使用 PHP 8.2 的官方 Alpine 镜像作为基础镜像
ARG PHP_VERSION
FROM php:${PHP_VERSION}

# 阿里云镜像替换
ARG CHANGE_SOURCE
RUN if [ "$CHANGE_SOURCE" = "true" ]; then \
        sed -ir 's#dl-cdn.alpinelinux.org#mirrors.aliyun.com#g' /etc/apk/repositories; \
    fi

# 设置环境变量
ENV SW_VERSION="5.1.4" \
    PHPIZE_DEPS="autoconf dpkg dpkg-dev file g++ gcc libc-dev make php-pear pkgconf"

# 复制解压
COPY ./ext/ /php-ext/
RUN find /php-ext/ -type f \( -name "*.tgz" -o -name "*.tar.gz" \) | xargs -i tar -zxf {} -C /php-ext/;

# 安装 PHP Extension
RUN apk update && apk add --no-cache \
    bash \
    $PHPIZE_DEPS \
    libstdc++ \
    openssl-dev \
    zlib-dev \
    libzip-dev \
    curl-dev \
    git \
    libevent-dev \
    mysql-dev \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-install pcntl \
    && docker-php-ext-install zip  \
    && docker-php-ext-install mysqli pdo pdo_mysql  \
    && docker-php-ext-enable pdo_mysql


# 安装 c-ares 扩展
RUN cd /php-ext/c-ares-1.33.1 \
    && ./configure \
    && make \
    && make install

# 安装 swoole
RUN cd /php-ext/swoole-src-5.1.4 \
    && phpize \
    && ./configure  --enable-openssl \
                    --with-openssl-dir=/usr/include/openssl \
                    --enable-swoole-curl \
                    --enable-cares \
                    --enable-mysqlnd \
                    --enable-debug \
    && make -j3 \
    && make install \
    && docker-php-ext-enable swoole

# 清理
RUN apk del $PHPIZE_DEPS

# 安装 Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 配置 PHP
COPY php.ini /usr/local/etc/php/conf.d/

# 设置容器的工作目录
WORKDIR /var/www/project

COPY start.sh /usr/local/bin/start.sh

RUN chmod +x /usr/local/bin/start.sh

# 自动启动服务 or 持久进程
 CMD [ "sh", "-c", "/usr/local/bin/start.sh" ]

# 持久进程，本地开发可以执行此，然后进入容器手动开启服务
#CMD ["sleep", "infinity"]
