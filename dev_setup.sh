#!/bin/env sh

# setup.sh is a quick-and-dirty way for me
# to get the project running after I clone it


composer i
npm i
npm run dev
cp .env.example .env
./artisan key:generate
vim .env
