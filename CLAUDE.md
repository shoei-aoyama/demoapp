# CLAUDE.md

応答・コード内コメント・コミットメッセージはすべて日本語で記述する。

---

## マーキング定義

```text
[MUST]   例外が一切想定できないルール。
[SHOULD] 原則従う。合理的な理由がありレビュアーが合意した場合のみ例外可。
[FYI]    ルールではなく背景情報。「なぜそう決めたか」の補足。
```

---

## プロジェクト定義

| 項目                 | 値                                                                |
| -------------------- | ------------------------------------------------------------------ |
| 位置づけ             | AIを使ったLaravel新規開発をその場で見せるための小規模デモアプリ    |
| 構成                 | 単一 Laravel アプリ（フロントエンドの分離なし、Blade で画面を構築） |
| バックエンド         | PHP 8.4, Laravel 11                                                |
| ビュー               | Blade, Tailwind CSS                                                |
| DB                   | MySQL 8.4 LTS                                                      |
| インフラ             | Docker                                                             |
| パッケージマネージャ | Composer                                                           |
| タスクランナー       | Makefile                                                           |
| 認証                 | 初期実装ではなし（後日 AI に追加させるデモ候補）                   |

---

## リポジトリ構造

```
project/
├── CLAUDE.md                    # アーキテクチャ・規約・ルール（常時ロード）
├── README.md                    # プロジェクト概要（人間向け）
├── .claude/
│   ├── settings.json            # 権限・hooks 設定
│   ├── settings.local.json      # ローカル設定（.gitignore 対象）
│   ├── agents/                  # 特化型AIエージェント
│   ├── rules/                   # レビュー観点別の詳細ルール
│   └── skills/                  # 再利用可能なAIワークフロー（スラッシュコマンド）
├── .mcp.json                    # GitHub MCP 設定
├── docs/                        # 仕様・設計ドキュメント
│   ├── README.md                # オンボーディング起点（新規参画者の入口）
│   ├── requirements/            # 要件定義書
│   ├── design/                  # デザイン・基盤方針
│   ├── features/                # 機能設計書（機能一覧・画面フロー・状態遷移）
│   ├── decisions/                # ADR（アーキテクチャ決定記録）
│   ├── api/                     # API仕様（将来 API 化する場合のみ使用）
│   ├── database/                # DB設計（ER図・テーブル定義）
│   ├── runbooks/                # 運用手順書
│   ├── reference/                # 既知の罠などのリファレンス
│   ├── integrations/             # 外部連携の基本ルール（現状未使用）
│   └── maintenance/              # 進行中の保守案件
├── app/                          # Laravel アプリケーションコード
│   ├── Http/
│   │   ├── Controllers/          # Request受付・View返却・リダイレクト
│   │   └── Requests/             # FormRequest（バリデーション）
│   └── Models/                   # Eloquent（リレーション・Attribute）
├── resources/views/              # Blade テンプレート
├── routes/web.php                # ルーティング
├── database/
│   ├── migrations/
│   └── seeders/
├── docker/
├── docker-compose.yml
├── Makefile
├── .gitignore
└── .claudeignore
```

### 仕様ドキュメント（docs/）

仕様駆動開発（SDD）に基づき、**仕様を先に書き、仕様に対して実装する。**

```
1. 仕様作成（初回のみ /requirements → /more-requirements → /detail。以降は /make-docs）
2. 仕様レビュー
3. 実装（/implement：仕様を入力として）
4. テスト（/test-gen：仕様との整合性を検証）
```

---

## アプリケーション設計

### 基本方針

[MUST] Controller / FormRequest / Model / Blade の標準構成のみを使う。Repository・Service・UseCases など独自レイヤーは追加しない
[MUST] Controller で Eloquent を直接操作してよい
[MUST] 複数項目の入力検証や再利用される検証は FormRequest へ分離する。単純な検証は Controller 内で完結してよい
[MUST] ビューは Blade を使用し、DB アクセスや複雑な業務処理を Blade 内に書かない
[MUST] カート情報は Session で保持する（DB に永続化しない）
[SHOULD] Controller が明確に肥大化する場合のみ、Laravel 標準機能（FormRequest, Policy 等）で分離を検討する

### 実装順序

```
1. Migration + Model（リレーション定義）
2. FormRequest（バリデーション）
3. Controller + ルーティング
4. Blade ビュー
```

### DB 設計規約

[MUST] テーブル名: 複数形 `snake_case`
[MUST] カラム名: `snake_case`
[MUST] 主キー: `bigint` AUTO_INCREMENT
[MUST] 外部キー: `{単数形テーブル名}_id`
[MUST] 必須カラム: `id`, `created_at`, `updated_at`

詳細は `.claude/rules/database.md` を参照。

### コーディング規約

[MUST] 1関数 = 1責務
[MUST] マジックナンバー禁止（定数化）
[SHOULD] ネスト最大3階層（早期リターン）
例外: アルゴリズム上やむを得ない場合はレビュアーと合意

[MUST] 未使用コード即削除
[MUST] PSR-12 準拠、`make pint` でフォーマット
[MUST] 型宣言（引数・戻り値）必須
[MUST] クラス参照は `use` 文でインポートして短縮名を使う

| 対象             | 規則               |
| ---------------- | ------------------ |
| 変数・メソッド   | `camelCase`        |
| クラス           | `PascalCase`       |
| 定数             | `UPPER_SNAKE_CASE` |
| テーブル・カラム | `snake_case`       |

### エラーハンドリング

[MUST] 想定内（バリデーション等）と想定外（障害）を区別
[MUST] 握りつぶし禁止。想定外は必ずログ記録
[MUST] ユーザーに内部情報（スタックトレース、SQL）を返さない
[MUST] `dd()`、`dump()` などのデバッグコードを残さない

### テスト（PHPUnit）

- `tests/Unit/`: 単体テスト
- `tests/Feature/`: 結合テスト（画面・エンドポイント単位）
- メソッド名は日本語可: `test_カートに商品を追加できる()`

---

## Git 運用

### Issue 管理（タスク・改善・バグの起票ルール）

エンハンス・改善・バグ・調査タスクは **GitHub Issue で管理する**。docs には「現状の仕様・運用手順」のみ記載し、`[ ]` 形式の TODO リストは原則残さない。気付き次第 Issue に登録すること。

- 起票には `.github/ISSUE_TEMPLATE/` のテンプレートを使う
  - **bug_report**: 既存機能が期待通りに動かない
  - **feature_request**: 新機能の追加
  - **improvement**: 既存機能の改善・リファクタ・性能改善・UX 改善
  - **task**: 調査・ドキュメント・運用準備など、コード変更を伴わない作業
- ブランチ命名は `<type>/#<Issue番号>-<内容>`。Issue 番号と type を揃える（`feat` Issue なら `feat/` ブランチ）
- docs に「将来やること」を書きたくなった時は、まず Issue を立てて docs からはリンクのみ貼る

### ブランチ

```
main       ← 本番。直接コミット禁止
develop    ← ステージング。機能ブランチのマージ先
  ├── feat/#123-user-registration       ← 新機能
  ├── fix/#456-csv-export-bug           ← バグ修正（緊急修正含む）
  ├── refactor/#789-auth-logic          ← リファクタリング
  └── docs/user-registration            ← ドキュメント作成（/make-docs、Issue番号なし）
# 命名規則: <type>/#<Issue番号>-<内容の kebab-case>
# docsブランチのみ例外でIssue番号なし（ドキュメント作成時点ではIssueが未起票のため）
# すべてのブランチは develop から作成する（緊急修正も例外なし、`hotfix` type は廃止）
# 緊急バグ修正も fix/ を使う。緊急性は GitHub のラベル等で表現する
```

### rebase 運用

[MUST] タスクブランチへの develop / main の取り込みは `git merge` ではなく `git rebase` を使う
[MUST] PR を出す前に必ず `git rebase origin/develop` を実行する（レビュー開始時点で最新差分を見せるため）
[SHOULD] レビュー期間中はコンフリクト発生時のみ追加で rebase（マージ直前の強制 rebase は不要）

❌ `git merge origin/develop`
✅ `git fetch origin && git rebase origin/develop`

コンフリクト解消後は `git rebase --continue` で再開する。

[MUST] force push は `--force-with-lease`。`--force` 禁止

❌ `git push --force`
✅ `git push --force-with-lease`

[MUST] develop / main ブランチでは rebase 禁止（共有ブランチの歴史を書き換えない）

### マージ戦略

| マージ方向               | マージ方法                              | 理由                                     |
| ------------------------ | ---------------------------------------- | ----------------------------------------- |
| タスクブランチ → develop | **Squash and merge**                    | 作業コミットを整理して履歴をきれいに保つ |
| develop → main           | **Create a merge commit（通常マージ）** | 履歴を繋げて次回PRの差分を正しく保つ     |

[FYI] develop → main でスカッシュマージすると、Git が履歴の親子関係を認識できなくなり、次回 PR で過去の全差分が再表示される。必ず通常マージを使うこと。

### コミットメッセージ

```
<type>: <概要>
```

| type     | 用途                     |
| -------- | ------------------------ |
| feat     | 新機能                   |
| fix      | バグ修正（緊急修正含む） |
| refactor | リファクタリング         |
| docs     | ドキュメント             |
| style    | フォーマット             |
| test     | テスト                   |
| chore    | ビルド・設定             |

### PR

**タスクブランチ → develop:**

[MUST] 1 PR = 1 機能
[MUST] タイトルはコミットメッセージ規約に準拠

**develop → main（Release PR）:**

[MUST] タイトル: `Release: YYYY/MM/DD`
[SHOULD] 本文: 含まれる PR の一覧 + 確認状況チェックリスト
[MUST] `.github/workflows/release-pr.yml` の `workflow_dispatch` 経由でのみ作成（手動 `gh pr create` 禁止）

### approve ルール

| PR の種類                    | 必要な approve | 担当   |
| ---------------------------- | -------------- | ------ |
| タスクブランチ → develop     | **最低 1 名**  | **PL** |
| develop → main（Release PR） | **最低 1 名**  | **PM** |

[MUST] approve なしのマージは禁止
[FYI] 自動レビュー（claude-code / codex）は **PG（開発者）自身**が PR 提出前のセルフチェックとして実施するもの。人手レビューの代替にはならない（両方を経る前提）

---

## 開発環境

### コマンド

```
make install              # 初期セットアップ
make up / down / restart  # コンテナ管理
make fresh                # DB初期化 + シード
make migrate              # マイグレーション
make seed                 # シーディング
make test                 # PHPUnit
make pint                 # PHP フォーマット
make stan                 # PHP 静的解析（PHPStan）
```

### アクセス先

| サービス   | URL                    |
| ---------- | ---------------------- |
| バックエンド | http://localhost:8000 |
| phpMyAdmin | http://localhost:8080  |
| MailHog    | http://localhost:8025  |

---

## MCP（GitHub 連携）

- 設定: `.mcp.json`（プロジェクトスコープ）
- 認証: OAuth（初回 `/mcp` でブラウザ認証）

[MUST] push は `git push` で行う（MCP 経由禁止）
[MUST] Issue/PR 作成は必ずユーザーに確認してから実行

| 操作                              | 権限  |
| --------------------------------- | ----- |
| 読み取り（参照・検索）            | allow |
| 書き込み（作成・更新）            | ask   |
| リモート直接変更（push_files 等） | deny  |
