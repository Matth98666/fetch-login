#!/bin/bash
# Copyright SYRADEV - Regis TEDONE - 2023
# Compress Fetch-Login JS files

declare sourceJS="./js/"
declare destinationJS="../public/js/"
declare uglify="./node_modules/uglify-js/bin/uglifyjs"

for file in "$sourceJS"*.js; do
  sourceFile=$(basename "$file")
  sourceFilenoExt="${sourceFile%%.*}"
  echo Treating "$sourceFile":
  $uglify "$file" -o $destinationJS"$sourceFilenoExt".min.js -c -m --comments '/syradev/' --source-map "root='https://www.mysimplon.org/fetch_login/src/js/',url='$sourceFilenoExt.min.js.map'"
  echo "$sourceFile" treated.
done
echo ----------------------------------
echo JS Compression done!
