# .claude/skills/ 構成・運用ガイド

## 概要

このディレクトリは、要件整理からPull Request作成までの開発工程で使用するClaude CodeのSkillを格納する。

各Skillは`<skill-name>/SKILL.md`で定義し、原則として1つの工程・1つの責務だけを担当する。

すべてのSkillは手動で実行する。AIが独断で別のSkillを起動したり、後続工程へ進んだりしない。

## 共通原則

すべてのSkillは、次の原則に従う。

1. **1つのSkillは1つの目的に限定する**
2. **調査・設計・実装・レビューなど、異なる工程を混在させない**
3. **人間の承認が必要な工程では、GOサインを得るまで成果物を変更しない**
4. **不明点を推測で補わない**
5. **Issue・承認済み実装方針・既存規約の範囲を超えない**
6. **別Skillの責務を勝手に実行しない**
7. **将来の拡張だけを理由に機能・抽象化・レイヤーを追加しない**
8. **後続工程が利用できる明確な形式で結果を報告する**
9. **問題がなければ、無理に変更や提案を作らない**

詳細な判断原則は`.claude/rules/project-principles.md`を正とする。

## フロントマター

各`SKILL.md`の先頭に、用途に応じたYAMLメタデータを定義する。

```yaml
---
name: skill-name
description: Skillの短い説明
argument-hint: "[必要な引数]"
disable-model-invocation: true
allowed-tools:
  - Read
  - Glob
  - Grep
disallowed-tools:
  - Write
  - Edit
  - NotebookEdit
  - Skill
---
```

- `disable-model-invocation: true`を設定し、手動実行のみとする。
- 使用しないツールを安易に許可しない。
- 読み取り専用Skillでは、`Write`と`Edit`を禁止する。
- `Bash`を許可する場合も、Skill本文で使用可能な用途を制限する。
- `Skill`ツールを禁止し、別Skillの自動実行を防ぐ。

## Skillの記述

各Skillには、責務に応じて次の内容を明記する。

- 目的
- 使用方法または入力
- 手順
- 承認が必要なタイミング
- 停止条件または禁止事項
- 出力または完了報告
- 関連するRules・Skill・ドキュメント

すべてのSkillへ同一の見出しを機械的に強制しない。短く明確に伝わる構成を優先する。

## Skill一覧

| Skill           | 呼び出し           | 責務                                                                                  |
| --------------- | ------------------ | ------------------------------------------------------------------------------------- |
| requirements    | `/requirements`    | 人間の要望を段階的に整理し、承認後に要件ドキュメントを1件作成する                     |
| design-docs     | `/design-docs`     | 必要に応じてDatabase・API・機能・構成・意思決定の設計ドキュメントを作成または更新する |
| issue           | `/issue`           | 要件ドキュメントを最小限の縦割りIssueへ分割し、承認後に起票する                       |
| analyze         | `/analyze`         | Issue・仕様・既存コードを調査し、承認用の実装方針をIssueコメントへ記録する            |
| implement       | `/implement`       | `develop`を最新化し、Issueブランチを作成して承認済み方針の範囲で初回実装する          |
| commit          | `/commit`          | 初回実装または追加修正を判定し、適切な単位と日本語メッセージでコミットする            |
| review          | `/review`          | 方針逸脱・不要コード・規約・品質・セキュリティを読み取り専用で監査する                |
| refactor        | `/refactor`        | 現在必要な最小限のリファクタリング候補だけを提案する                                  |
| pr              | `/pr`              | 作業ブランチをPushし、`develop`向けの通常Pull Requestを作成する                       |

## 開発フロー

```text
/requirements
↓
必要に応じて /design-docs
↓
/issue
↓
/analyze
↓
/implement
↓
/commit
↓
/review
↓
必要に応じて /refactor
↓
Geminiレビュー
↓
修正がある場合は /commit
↓
/pr
↓
人間がdevelopへマージ
```

### 初回コミット

`/implement`の直後に`/commit`を実行する。

初回実装では、新規・既存を問わず変更ファイルを1ファイルずつコミットする。

### レビュー後の修正

`/review`と`/refactor`はコードを変更しない。

指摘や提案を採用するかは人間が判断し、採用した内容だけを通常の指示で修正する。

Geminiレビューを含むレビュー後に変更が発生した場合は、再度`/commit`を実行し、関連する修正をまとめてコミットする。

変更がなければ2回目の`/commit`は不要とする。

## Skill間の正本

各工程では、次の情報を正本として扱う。

| 工程      | 正本                                           |
| --------- | ---------------------------------------------- |
| 要件整理  | `docs/requirements/`または`docs/maintenance/`  |
| Issue分割 | 承認済み要件ドキュメント                       |
| 実装分析  | GitHub Issue・関連docs・既存コード             |
| 初回実装  | Issueコメントの`## 実装方針（承認済み）`       |
| レビュー  | Issue・承認済み実装方針・Git差分・Rules        |
| PR作成    | Issue・承認済み実装方針・コミット履歴・Git差分 |

要件、Issue、実装方針、設計ドキュメント、実装内容を矛盾させない。

## 人間の承認

次の処理は、人間のGOサインを得た場合だけ実行する。

- 要件ドキュメントの作成・更新
- 設計ドキュメントの作成・更新
- GitHub Issueの起票
- 実装方針のIssueコメント投稿
- 初回実装
- レビュー・リファクタリング提案の反映

レビュー後の修正内容は、自動的にすべて採用しない。

## 関連Rules

- プロジェクト共通方針：`.claude/rules/project-principles.md`
- Issue・ブランチ・コミット・PR：`.claude/rules/issue-workflow.md`
- Laravel：`.claude/rules/laravel.md`
- Blade / Tailwind CSS：`.claude/rules/blade-tailwind.md`
- Database：`.claude/rules/database.md`
- API：`.claude/rules/api.md`

テスト関連のSkillとRulesは、運用方針が確定するまで追加しない。
