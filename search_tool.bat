@echo off
set search_term=%1
echo 🔍 正在搜尋「%search_term%」的現有資源...
echo 🌍 搜尋 GitHub 開源倉庫...
curl -s "https://api.github.com/search/repositories?q=%search_term%" | jq -r ".items[] | \"\(.name) - \(.html_url)\"" | more
pause
