@echo off
REM Автоматический коммит и пуш изменений

git add .
git commit -m "Fix"
git push origin main

exit
