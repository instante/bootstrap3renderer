#!/usr/bin/env bash
pushd "$(dirname "$0")/.."

if [ -f ./tests/php-unix.ini ]; then
    cp ./tests/php-unix.ini ./tests/php.ini
    echo "" >> ./tests/php.ini # empty line
else
    echo "" > ./tests/php.ini
fi
PHP_EXT=`php -r "echo ini_get('extension_dir');"`
echo "extension_dir=$PHP_EXT" >> ./tests/php.ini

./vendor/bin/tester ./tests/$1 -p php -c ./tests

popd
