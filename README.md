# WordPress 開發規範資料庫

## 這是什麼？
這個專案整理了 **WordPress 重要的開發規範**，幫助我們開發外掛、主題，並減少不必要的錯誤。
每個資料夾都代表一個重要的 WordPress 開發主題，點進去後可以查看詳細說明與範例程式碼。

---

## 📂目錄總覽
| 📂 目錄名稱 | 說明 |
|------------|--------|
<<<<<<< HEAD
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

**當你要開發新功能時，只要喊：「澄澄，去倉庫逛逛！」，就走這個流程！**

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

=======
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
>>>>>>> 2df1dd0 (更新 README.md 並新增 WordPress 開發 SOP)
