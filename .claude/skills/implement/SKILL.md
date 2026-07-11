---
name: implement
description: Issueコメントの承認済み実装方針を読み込み、developを最新化してIssue用ブランチを作成し、方針の範囲内で初回実装を行う。
argument-hint: "[issue-number]"
disable-model-invocation: true
allowed-tools:
  - Read
  - Glob
  - Grep
  - Bash
  - Write
  - Edit
disallowed-tools:
  - NotebookEdit
  - Skill
---

# implement

## 目的

対象Issueのコメントに記録された、最新の`## 実装方針（承認済み）`を読み込む。

`develop`を最新化し、方針シートに記載されたIssue用ブランチを作成したうえで、承認済みの範囲内で初回実装を行う。

## 使用方法

Issue番号を指定して手動で実行する。

```text
/implement 12
```

このSkillを実行した時点で、承認済み実装方針に基づくブランチ作成と初回実装について、人間のGOサインを得ているものとする。

## 手順

1. 次のコマンドでIssue本文とコメントを取得する。

   ```bash
   gh issue view <Issue番号> --json number,title,body,comments,state,url
   ```

2. コメントから、最新の次の見出しを持つ方針シートを取得する。

   ```markdown
   ## 実装方針（承認済み）
   ```

   方針シートが存在しない場合は停止する。

3. 方針シートから次を確認する。
   - ブランチ構成
   - 変更・追加するファイル
   - 変更なし（スコープ外）
   - 事前確認
   - 動作確認

   未完了の事前確認や、実装前に判断が必要な項目が残っている場合は停止する。

4. `git status --short`を実行する。

   未コミットの変更がある場合、対象Issueの「## 関連要件」に記載されたドキュメントパスと一致するか確認する。

   - 一致しない変更、または一致するか判断できない変更が含まれる場合は、stash・破棄・コミットを行わず停止する。
   - 「## 関連要件」に記載されたドキュメントパスとの一致のみで構成されている場合は、その変更を保持したまま次の手順へ進み、作成するIssue用ブランチへそのまま持ち越す。この場合、変更内容を報告に明記する。

5. `develop`へ切り替え、最新化する。

   ```bash
   git switch develop
   git fetch origin
   git pull --ff-only origin develop
   ```

   `develop`が存在しない場合や、fast-forwardで更新できない場合は停止する。

6. 方針シートに記載されたブランチ名について、同名のローカル・リモートブランチが存在しないことを確認する。

   同名ブランチが存在する場合は、削除・上書き・再利用をせず停止する。

7. 方針シートに記載されたブランチ名で、Issue用ブランチを作成して切り替える。

   ```bash
   git switch -c '<方針シートに記載されたブランチ名>'
   ```

8. 承認済みの実装方針と次のルールに従い、コードを実装する。
   - `.claude/rules/project-principles.md`
   - `.claude/rules/laravel.md`
   - `.claude/rules/blade-tailwind.md`

9. 実装内容と変更ファイルを報告する。

## 実装原則

- 方針シートの「変更・追加」に記載された範囲を基準にする
- 「変更なし（スコープ外）」へ踏み込まない
- Issueの受け入れ条件に必要な最小限のコードだけを実装する
- 不要な抽象化・共通化・レイヤーを追加しない
- 既存の命名規則、構成、実装パターンを優先する
- 方針シートで指定された既存コードを再利用する
- Issueと無関係な修正やリファクタリングを行わない

方針シートにない変更でも、次の条件をすべて満たす場合は実施してよい。

- Issueの達成に不可欠である
- 影響範囲が小さい
- 既存仕様を変更しない
- 新しい設計判断を必要としない

方針外で実施した変更は、完了報告で必ず明示する。

## コマンドの制限

承認済みの実装に必要な`artisan make:*`など、ファイル生成コマンドは使用してよい。

Migrationファイルは作成してよいが、データベースの状態を変更するコマンドは実行しない。

禁止例:

```text
php artisan migrate
./vendor/bin/sail artisan migrate
make migrate
php artisan migrate:fresh
php artisan migrate:refresh
php artisan migrate:reset
php artisan migrate:rollback
php artisan db:seed
```

上記に限らず、Migration、Seeder、Rollbackなど、データベースへ変更を加えるコマンドは禁止する。

既存ファイルは`Write`または`Edit`で編集する。`sed -i`や`echo > file`など、Bashによるファイル内容の一括書き換えは行わない。

## 停止条件

実装中に次が判明した場合は、推測で進めず停止する。

- 方針シートとIssue・既存コードに矛盾がある
- 方針シートの想定を超える影響がある
- 想定外のパッケージ追加や破壊的変更が必要
- 新しい仕様・セキュリティ・データ設計の判断が必要

途中で停止した場合、それまでの変更は戻さず、変更内容と停止理由を報告する。

## 対象外

- 実装方針の作成・変更
- テストコードの作成・変更
- テストや動作確認コマンドの実行
- Pint・Lint・静的解析・ビルド
- コードレビュー・リファクタリング
- 検証やレビュー後の修正対応
- コミット・Push・Pull Request作成
- Issueの範囲外の修正

## 出力形式

### ステータス

`COMPLETED`または`STOPPED`のいずれか1つ。

### 対象Issue

- Issue番号
- Issueタイトル

### 作成ブランチ

実際に作成して切り替えたブランチ名を記載する。

ブランチ作成前に停止した場合は「未作成」と記載する。

### 実装内容

受け入れ条件と対応する実装内容を簡潔に記載する。

### 変更ファイル

新規作成・変更したファイルパスと、その役割を記載する。

### 方針外の変更

承認済み方針に含まれていなかった変更と、その必要性を記載する。

該当しない場合は「なし」と記載する。

### 未対応・停止理由

未対応の事項または停止理由があれば記載する。

### 次の工程

`COMPLETED`の場合は、方針シートの「動作確認」に基づく検証工程へ進むよう案内する。

## 完了条件

- 方針シートに記載されたブランチ上で実装している
- 承認済みの範囲内で実装している
- 実装内容と方針外の変更を報告している

## 参照

- `.claude/skills/analyze/SKILL.md`
- `.claude/rules/project-principles.md`
- `.claude/rules/issue-workflow.md`
- `.claude/rules/laravel.md`
- `.claude/rules/blade-tailwind.md`
