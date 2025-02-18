#!/bin/bash

set -euo pipefail

readonly search_term="${1:?請輸入搜尋關鍵字！}"

echo "🔍 正在搜尋「${search_term}」的現有資源..."

# 1️⃣ **搜尋 wpgit 本地倉庫**
echo "📂 搜尋 wpgit 本地倉庫..."
if command -v rg &> /dev/null; then
    readonly files_with_matches=$(rg --ignore-case --files-with-matches "${search_term}" ~/wpgit/)
    if [[ -n "${files_with_matches}" ]]; then
        echo "${files_with_matches}" | while read -r file; do
            echo "✅ 在本地倉庫找到：${file}"
        done
    fi
else
    readonly files_with_matches=$(grep -irl "${search_term}" ~/wpgit/)
    if [[ -n "${files_with_matches}" ]]; then
        echo "${files_with_matches}" | while read -r file; do
            echo "✅ 在本地倉庫找到：${file}"
        done
    fi
fi

# 2️⃣ **搜尋 GitHub**
echo "🌍 搜尋 GitHub 開源倉庫..."
readonly github_search_results=$(curl -s "https://api.github.com/search/repositories?q=${search_term}")
if [[ -n "${github_search_results}" ]]; then
    echo "${github_search_results}" | jq -r '.items[] | "\(.name) - \(.html_url)"' | head -n 5
fi

# 3️⃣ **提供「直接帶走現貨」的指令**
echo "✨ 如果有找到現成工具，可以直接使用以下指令帶走："
echo "  git clone <GitHub倉庫網址>"
echo "  或"
echo "  git submodule add <GitHub倉庫網址> ~/wpgit/"

