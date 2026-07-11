---
name: pr
description: コミット済みの作業ブランチをPushし、develop向けの通常Pull Requestを作成する。
argument-hint: "[issue-number]"
disable-model-invocation: true
allowed-tools:
  - Read
  - Glob
  - Grep
  - Bash
disallowed-tools:
  - Write
  - Edit
  - NotebookEdit
  - Skill
---

# pr

## 目的

対象Issue、承認済み実装方針、`develop`との差分を確認し、現在の作業ブランチをPushして通常のPull Requestを作成する。

PRの向きは、作業ブランチから`develop`とする。

このSkillを実行した時点で、`/review`とGeminiによるレビューが完了し、PR作成について人間の承認を得ているものとする。

## 使用方法

```text
/pr 12
```

## 手順

1. 対象Issueと承認済み実装方針を取得する。

   ```bash
   gh issue view <Issue番号> --json number,title,body,comments,state,url
   ```

   Issueが存在しない、閉じている、または`## 実装方針（承認済み）`がない場合は停止する。

2. Gitの状態を確認する。

   ```bash
   git branch --show-current
   git status --short
   git fetch origin
   ```

   次の場合は停止する。
   - 現在のブランチが`main`または`develop`
   - 未コミットの変更がある
   - `develop`との差分コミットがない
   - マージまたはリベースの競合が残っている

3. デフォルトブランチが`develop`であることを確認する。

   ```bash
   gh repo view --json defaultBranchRef --jq '.defaultBranchRef.name'
   ```

   `develop`でない場合は、設定を変更せず停止する。

4. `develop`との差分とコミット履歴を確認する。

   ```bash
   git log --oneline develop..HEAD
   git diff --name-status develop...HEAD
   git diff --stat develop...HEAD
   ```

5. 現在のブランチを対象とするPRが存在しないことを確認する。

   ```bash
   gh pr list --head "$(git branch --show-current)" --state all --json number,state,url
   ```

   既存PRがある場合は、新規作成せず停止する。

6. Issue、方針シート、コミット履歴、差分からPRタイトルと本文を作成する。

7. 現在のブランチをPushする。

   ```bash
   git push -u origin "$(git branch --show-current)"
   ```

8. Draftではない通常PRを`develop`向けに作成する。

   ```bash
   gh pr create \
     --base develop \
     --head "$(git branch --show-current)" \
     --title "<接頭辞>: <日本語のPRタイトル>" \
     --body-file -
   ```

   PR本文は標準入力から渡し、本文作成のためにローカルファイルを作らない。

9. 作成されたPRの番号とURLを報告する。

## PRタイトル

次の形式にする。

```text
<接頭辞>: <日本語の変更内容>
```

接頭辞は変更内容に応じて選ぶ。

| 接頭辞     | 用途                     |
| ---------- | ------------------------ |
| `feat`     | 新機能・画面・処理の追加 |
| `fix`      | 不具合修正               |
| `refactor` | 挙動を変えない整理       |
| `docs`     | ドキュメント変更         |
| `style`    | CSS・表示上の変更        |
| `chore`    | 設定・環境・CIなどの変更 |

## PR本文

次のテンプレートを使用する。

```markdown
## 概要

- Issue #XXX として、画面・機能・修正内容を実装
- mock-backed adapterとしての実装など、重要な前提がある場合のみ追記

## 主な実装

- **機能・ファイル名**：実装内容と設計上の意図
- **機能・ファイル名**：実装内容と設計上の意図

## Figma / 設計書との差異・補足

- 承認済み方針との相違点と理由
- 実装上のトレードオフと判断根拠

## 本PR対象外

- 対象外の内容
- 対応が必要になる条件

## Test plan

### 自動チェック

- 未実施（テスト導入工程で別途対応）

### 受け入れ条件

- [ ] Issueの受け入れ条件

### リグレッション確認

- [ ] 既存機能への意図しない影響がない

Closes #XXX
```

次のセクションは、該当する場合だけ記載する。

- `Figma / 設計書との差異・補足`
- `本PR対象外`
- `リグレッション確認`
- mock-backed adapterなどの補足

受け入れ条件は、対象Issueからそのまま対応関係が分かる形で転記する。

実施していない確認を完了済みの`[x]`にしない。

本文末尾には必ず次を記載する。

```text
Closes #<Issue番号>
```

## 禁止事項

- コードやコミット内容を変更しない
- 未コミット変更を自動でコミットしない
- `main`向けのPRを作成しない
- Draft PRを作成しない
- 既存PRを更新・上書きしない
- リポジトリ設定を変更しない
- 実施していないテストや確認を完了扱いにしない
- PRを自動でマージしない
- 作業ブランチを削除しない

## 出力形式

### ステータス

`CREATED`または`STOPPED`。

### 対象Issue

- Issue番号
- Issueタイトル

### Pull Request

- PR番号
- PRタイトル
- Baseブランチ
- Headブランチ
- PR URL

### Issue連携

- `Closes #<Issue番号>`の記載有無
- `develop`がデフォルトブランチであること

### 停止理由

`STOPPED`の場合のみ記載する。

### 次の工程

PR作成後は、人間による内容確認と`develop`へのマージを案内する。

## 完了条件

- 作業ブランチをPushしている
- `develop`向けの通常PRを作成している
- PR本文がIssueと実装差分に一致している
- `Closes #<Issue番号>`を記載している
- PR番号とURLを報告している

## 参照

- `.claude/skills/analyze/SKILL.md`
- `.claude/skills/implement/SKILL.md`
- `.claude/skills/review/SKILL.md`
- `.claude/skills/commit/SKILL.md`
- `.claude/rules/issue-workflow.md`
