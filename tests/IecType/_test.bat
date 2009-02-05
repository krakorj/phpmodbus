@echo off
call ../config.bat

for %%f in (test.*.php) do %php% -q "%%f" > "output/%%f.html"

@echo on
%diff% -r output ref
pause
