# WordPress 開發規範資料庫

## 這是什麼？
這個專案整理了 **WordPress 重要的開發規範**，幫助我們開發外掛、主題，並減少不必要的錯誤。
每個資料夾都代表一個重要的 WordPress 開發主題，點進去後可以查看詳細說明與範例程式碼。

---
# 📌 WordPress 開發指南（請先閱讀！）

教學方式及環境注意事項
-環境是WIN10、使用VSCODE、全部都要同步到GIT(但不會操作要從基礎開始教學)
-代碼全部都要放在畫布、有更新畫布一次皆須逛一次倉庫避免基礎錯誤
-教學需要拆超小步驟（先帶安裝環境，再拆小步驟一次只教學一個動作例如安裝XXX這種超基礎步驟絕對需要，每一步都能 獨立測試，這樣不用一次接收太多資訊，學起來會更順暢！）
-白話文教學（不用技術術語，只用簡單理解的說法）
-一定要親手實做（不能只是看懂，要實際跑過程式）
-錯誤率最低的步驟順序（先給一個步驟跟可能發生的結果，再等文玉回報）

🚨 **畫布已滿？請這樣處理！**
- 優先清理不再需要的代碼片段（請確認它們已經存入 Git）
- 若仍然不夠，請建新畫布，並標記「畫布版本」，以免混亂


🚀 **快速入口**
- 📝 [開發 SOP](./WordPress%20開發%20SOP.txt)（完整流程）
- 🔄 [芝麻同步 SOP](./芝麻同步SOP.txt)（Git 維護流程）


**👉 建議順序：**
1️⃣ 先看「開發 SOP」，確保流程正確。
2️⃣ 再看「芝麻同步 SOP」，確保 Git 正確同步。
3️⃣ 有問題？請先查「注意事項」，再來求救！
## 📂目錄總覽
| 📂 目錄名稱 | 說明 |
|------------|--------|

| [`cpt/`](cpt/README.md) | **Custom Post Type（CPT）**：WordPress 自訂文章類型與分類法 |
| [`elementor/`](elementor/README.md) | **Elementor**：Elementor 小工具（Widgets）開發指南 |
| [`hook/`](hook/README.md) | **Hooks（Actions & Filters）**：WordPress 核心鉤子開發 |
| [`restapi/`](restapi/README.md) | **WordPress REST API**：自訂 API 端點與資料操作 |
| [`safety/`](safety/README.md) | **安全性規範**：WordPress 開發安全最佳實踐 |
| [`searchandfilter/`](searchandfilter/README.md) | **搜尋與篩選**：建立高效的搜尋與篩選功能 |
| [`wp-ajax/`](wp-ajax/README.md) | **AJAX 技術**：使用 AJAX 讓 WordPress 更動態化 |
| [`wphandbook/`](wphandbook/README.md) | **WordPress 開發手冊**：WordPress 重要開發知識彙整 |
| [`ci-cd/`](ci-cd/README.md) | **自動部署（CI/CD）**：實現自動化部署的指南 |
| [`code-snippets/`](code-snippets/README.md) | **程式碼片段管理**：管理和使用常用的 PHP 片段 |

---

## WordPress 開發 SOP

### 逛倉庫找現貨（避免重複造輪子）
1. **逛倉庫找現貨** → 先搜尋 GitHub & 外部開源資源，看有沒有現成工具。
2. **逛我們自己的基本庫存** → 檢查 `wpgit` 倉庫是否已經有相關功能。
3. **決定：「帶走 or 自己寫」** → 有現成的就帶走（Git Submodule），沒有就自己寫並存進 Git 倉庫。
4. **確認好用的，存進「檔案庫」** → 確保未來能快速取用，不怕 GitHub 倉庫失效。

**當你要開發新功能時，只要喊：「澄澄/霖霖 ，去倉庫逛逛！」，就走這個流程！**

---

## 如何使用？
1. **下載專案**
   ```bash
   git clone https://github.com/ufo7183/wpgit.git
   ```
2. **初始化子模組**
   ```bash
   cd wpgit
   git submodule update --init --recursive
   ```
3. **開始開發**
   - 根據需要進入相應的目錄，查看詳細說明與範例程式碼。
   - 如需新增外部資源，使用 Git Submodule 進行管理：
     ```bash
     git submodule add <外部資源網址> <目標目錄>
     ```
   - 提交更改：
     ```bash
     git add .
     git commit -m "新增/更新了 XXX 功能"
     git push origin main
     ```

---

📢 **本專案將持續更新，歡迎關注與貢獻！**


| [`cpt/`](cpt/README.md) |  **Custom Post Type（CPT）**：WordPress 自訂文章類型與分類法 |
| [`elementor/`](elementor/README.md) |  **Elementor**：Elementor 小工具（Widgets）開發指南 |
| [`hook/`](hook/README.md) | 🔗 **Hooks（Actions & Filters）**：WordPress 核心鉤子開發 |
| [`restapi/`](restapi/README.md) |  **WordPress REST API**：自訂 API 端點與資料操作 |
| [`safety/`](safety/README.md) |  **安全性規範**：WordPress 開發安全最佳實踐 |
| [`searchandfilter/`](searchandfilter/README.md) | 🔍 **搜尋與篩選**：建立高效的搜尋與篩選功能 |
| [`wp-ajax/`](wp-ajax/README.md) |  **AJAX 技術**：使用 AJAX 讓 WordPress 更動態化 |
| [`wphandbook/`](wphandbook/README.md) |  **WordPress 開發手冊**：WordPress 重要開發知識彙整 |

---

##  如何使用？
1️**下載專案**
```bash
git clone https://github.com/ufo7183/wpgit.git