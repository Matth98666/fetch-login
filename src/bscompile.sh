#!/bin/bash
# Copyright SYRADEV - Regis TEDONE - 2023
# Compile & Compress Bootstrap scss files

echo Treating Bootstrap 5 files
sass --style=compressed ./scss/bs5.scss ../public/css/bootstrap.min.css
echo -------------------------------------
echo Bootstrap SCSS Compilation done!