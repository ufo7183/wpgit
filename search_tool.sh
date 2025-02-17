#!/bin/bash

# 確保有輸入搜尋關鍵字
if [ -z "$1" ]; then
    echo "❌ 請輸入搜尋關鍵字！"
    exit 1
fi

search_term="$1"

echo "🔍 正在搜尋「$search_term」的現有資源..."

# 1️⃣ **搜尋 wpgit 本地倉庫**
echo "📂 搜尋 wpgit 本地倉庫..."
if command -v rg &> /dev/null; then
    # 如果有 ripgrep，使用更快的搜尋方式
    rg --ignore-case --files-with-matches "$search_term" ~/wpgit/ | while read -r file; do
        echo "✅ 在本地倉庫找到：$file"
    done
else
    # 如果沒有 ripgrep，就改用 grep
    grep -irl "$search_term" ~/wpgit/ | while read -r file; do
        echo "✅ 在本地倉庫找到：$file"
    done
fi

# 2️⃣ **搜尋 GitHub**
echo "🌍 搜尋 GitHub 開源倉庫..."
curl -s "https://api.github.com/search/repositories?q=$search_term" | jq -r '.items[] | "\(.name) - \(.html_url)"' | head -n 5

# 3️⃣ **提供「直接帶走現貨」的指令**
echo "✨ 如果有找到現成工具，可以直接使用以下指令帶走："
echo "  git clone <GitHub倉庫網址>"
echo "  或"
echo "  git submodule add <GitHub倉庫網址> ~/wpgit/"
