# 🤖 demoapp

Claude Code（AI）とチームが協調して開発を進めるための**AI駆動型 Laravel スケルトン**です。  
要件定義からコミット・PRまでの開発ライフサイクル全体を、スキル・エージェント・ドキュメント規約で自動化・標準化します。

Blade + Tailwind CSS による単一 Laravel アプリ（小規模な商品注文デモ）を題材に、
「要件定義されたものから、Issue → 実装方針 → 人間承認 → Claude Code による実装 → PR → Review まで、AI主体で開発できる」ことを示すためのプロジェクトです。

---

## 📁 ディレクトリ構成

```
.
├── .claude/                # Claude Code カスタム設定（スキル・エージェント・ルール）
├── .github/                # GitHub Actions・Issue/PR テンプレート
├── app/                    # Laravel アプリケーションコード
├── resources/views/        # Blade テンプレート
├── routes/                 # ルーティング
├── database/               # Migration / Seeder
├── docs/                   # プロジェクトドキュメント（Single Source of Truth）
├── docker/                 # Docker 設定ファイル
├── CLAUDE.md               # Claude Code へのプロジェクト指示書
├── .claudeignore           # Claude Code が読み込まないファイルの指定
├── .mcp.json               # MCP サーバー設定
├── .octocov.yml            # カバレッジ計測・レポート設定
├── .env.example            # 環境変数のサンプル
├── .gitignore              # Git 管理対象外ファイルの定義
├── docker-compose.yml      # Docker コンテナ定義
└── Makefile                # よく使うコマンドのショートカット
```

---

## 📂 各ディレクトリ・ファイルの役割と記載内容

### 🤖 `.claude/`

Claude Code がプロジェクト内で動作するための**カスタム定義**を格納します。

| サブディレクトリ | 役割 | 何を書くか |
| --- | --- | --- |
| `agents/` | 特定の専門家ペルソナとして自律稼働するエージェント定義 | `name` / `model` / `tools` のフロントマター＋動作仕様（例: `steering-planner`・`code-reviewer`） |
| `rules/` | レビュー観点別の詳細ルール | Laravel / DB / Blade・Tailwind / Issue 運用などの規約 |
| `skills/` | `/コマンド` で呼び出せるカスタムスキル定義 | `SKILL.md` にフロントマター＋プロセス・ルール（例: `/review`・`/commit`） |
| `settings.local.json` | Claude Code のローカル権限設定 | 許可する Bash コマンドの allowlist（`permissions.allow`）。機密情報は書かない |

> 詳細は `.claude/README.md` を参照。

---

### 🏛️ `CLAUDE.md`

Claude Code が**毎回自動的に読み込む**プロジェクト指示書です。  
ここに書いた内容が、このリポジトリ上でのすべての AI の行動規範になります。

**書くべき内容:**

| セクション | 内容 |
| --- | --- |
| プロジェクト概要 | 何を作っているか・技術スタック |
| アプリケーション設計 | Controller / Model / Blade の責務、実装順序 |
| コーディング規約 | 命名・禁止パターン・インポートルールなど |
| Git 規約 | ブランチ命名・コミットメッセージ形式 |

> ⚠️ 実装コードを変更して設計と乖離が生じた場合は、コードと同時にこのファイルも更新すること。

---

### 🚫 `.claudeignore`

Claude Code がコンテキストとして**読み込まないファイル・ディレクトリ**を指定します。  
`.gitignore` と同じ書式（glob パターン）です。

**書くべき内容:**

```
# 生成物・キャッシュ（コンテキスト汚染防止）
vendor/
build/
dist/

# 機密情報
.env
.env.*
!.env.example

# ログ・一時ファイル
*.log
storage/logs/

# バイナリ・メディア
*.png
*.jpg
*.pdf
```

> コンテキストウィンドウを効率的に使うため、AI が読む必要のないファイルはここで除外します。

---

### 🔌 `.mcp.json`

Claude Code が使用する **MCP（Model Context Protocol）サーバー**の接続設定を定義します。

**書くべき内容:**

```json
{
  "mcpServers": {
    "github": {
      "command": "npx",
      "args": ["-y", "@modelcontextprotocol/server-github"],
      "env": {
        "GITHUB_TOKEN": "${GITHUB_TOKEN}"
      }
    }
  }
}
```

> 接続するサービスごとにエントリを追加します。トークンは環境変数経由で渡し、直接書かないこと。

---

### 📊 `.octocov.yml`

CI でのカバレッジ計測・レポートを制御する **octocov** の設定ファイルです。  
PHPUnit が出力した `coverage.xml` を読み取り、PR へのコメントやバッジ生成を行います。

**書くべき内容:**

```yaml
coverage:
  paths:
    - coverage.xml    # PHPUnit のカバレッジレポートパス

# PR へのカバレッジコメント
comment:
  on_pull_request: true       # PR にカバレッジ差分コメントを投稿

# カバレッジ閾値（下回ると CI が fail）
coverage_badge:
  path: docs/coverage_badge.svg

# 閾値設定
threshold:
  file: 80                    # ファイル単位のカバレッジ最低値（%）
```

---

### 🔐 `.env.example`

環境変数の**サンプルファイル**です。実際の値は書かず、キー名とダミー値・コメントだけを記載します。  
新しい開発者がローカル環境を構築する際の雛形として使います。

> `.env` 本体は `.gitignore` に必ず追加し、リポジトリに含めないこと。

---

### 🐳 `docker-compose.yml`

ローカル開発環境の**Dockerコンテナ定義**です。  
`make up` 一発でバックエンド・DBが起動する環境を定義します。

**書くべき内容:**

```yaml
services:
  backend:       # Laravel（PHP-FPM）
  nginx:         # Webサーバー
  mysql:         # データベース
  phpmyadmin:    # DB管理ツール（開発用）
  mailpit:       # メール受信確認（開発用）
```

---

### 🛠️ `Makefile`

よく使うコマンドを**短いエイリアス**で実行できるようにするファイルです。  
`docker compose exec` の長いコマンドを `make xxx` に短縮します。

**書くべき内容の例:**

```makefile
up:      ## コンテナ起動
down:    ## コンテナ停止
migrate: ## マイグレーション実行
seed:    ## シーダー実行
pint:    ## PHP フォーマット（Laravel Pint）
stan:    ## 静的解析（PHPStan）
test:    ## テスト実行
```

---

### 🐙 `.github/`

GitHub の動作を制御する設定を格納します。

| ファイル/フォルダ | 何を書くか |
| --- | --- |
| `workflows/ci.yml` | PR・push 時の品質ゲート（Pint / PHPStan / PHPUnit） |
| `workflows/release-pr.yml` | `develop → main` のリリース PR 自動作成 |
| `ISSUE_TEMPLATE/` | バグ報告・新機能・改善・タスクの Issue テンプレート |
| `PULL_REQUEST_TEMPLATE.md` | PR 作成時のデフォルト本文テンプレート |

> 詳細は `.github/README.md` を参照。

---

### 📚 `docs/`

プロジェクトの **Single Source of Truth（唯一の事実の源泉）** となるドキュメントを格納します。

| サブディレクトリ | 何を書くか |
| --- | --- |
| `requirements/` | 機能の要件定義書（ユーザーストーリー・受け入れ条件） |
| `features/` | 機能設計書（画面フロー・ビジネスルール） |
| `database/` | ER図・テーブル定義・インデックス設計 |
| `api/` | API仕様（将来 API 化する場合のみ使用） |
| `maintenance/` | バグ修正・エンハンスの保守運用ドキュメント |
| `decisions/` | アーキテクチャ決定記録（ADR） |
| `runbooks/` | デプロイ・障害対応などの運用手順書 |
| `integrations/` | 外部サービス連携仕様（現状未使用） |
| `reference/` | 技術調査・用語集・参照資料 |

> ⚠️ 実装コードと設計書が乖離した場合は、必ず両方を同時に更新すること。

---

## 🚀 開発の始め方

```bash
# 1. 環境変数を設定
cp .env.example .env

# 2. コンテナを起動
make up

# 3. アプリのセットアップ
make migrate
make seed

# 4. ブラウザで確認
# バックエンド: http://localhost:8000
```

---

## 🤖 AI との協調開発フロー

```
/make-docs   # 機能設計
/issue       # GitHub Issue 作成
/analyze     # 実装方針の策定・承認
/implement   # 実装
/commit      # コミット
/review      # レビュー
/refactor    # 必要に応じてリファクタリング
/pr          # PR 作成
```

詳細は `.claude/rules/issue-workflow.md` を参照。
