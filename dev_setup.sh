#!/bin/env sh

# setup.sh is a quick-and-dirty way for me
# to get the project running after I clone it


composer i
mv .env.example .env
./artisan key:generate
vim .env
