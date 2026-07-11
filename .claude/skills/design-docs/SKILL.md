---
name: design-docs
description: 指定された種別の設計案を整理し、人間の承認後に設計ドキュメントを1件作成または更新する。
argument-hint: "[database|api|feature|architecture|decision] [概要]"
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

# design-docs

## 目的

要件・Issue・既存コード・既存ドキュメントを調査し、指定された種別の設計案を作成する。

人間のGOサインを得た場合のみ、対応する`docs/`配下へ設計ドキュメントを1件作成または更新する。

要件定義、Issue起票、コード実装は行わない。

## 使用方法

```text
/design-docs database お問い合わせデータの保存設計
/design-docs api お問い合わせ送信API
/design-docs feature お問い合わせ機能
/design-docs architecture メール送信構成
/design-docs decision メール送信方式の選定
```

## 種別

| 種別           | 保存先               |
| -------------- | -------------------- |
| `database`     | `docs/database/`     |
| `api`          | `docs/api/`          |
| `feature`      | `docs/features/`     |
| `architecture` | `docs/architecture/` |
| `decision`     | `docs/decisions/`    |

指定された種別が一覧にない場合は停止する。

## 手順

1. 指定された種別に対応する`references/`のテンプレートが存在する場合は読む。
2. テンプレートが存在しない場合は、同種の既存ドキュメントの構成を参考にする。既存ドキュメントもない場合は、共通の設計原則に従って必要最小限の構成を作る。
3. 関連する要件、Issue、既存ドキュメント、既存コードを調査する。
4. 既存設計との重複・矛盾・影響範囲を確認する。
5. 設計に必要な情報が不足している場合は、一度に2〜3問ずつ質問する。
6. 設計案をチャット上へ提示する。
7. 人間からGOサインまたは修正指示を受ける。
8. GOサイン後、設計ドキュメントを1件作成または更新する。
9. 作成結果と関連する次の工程を報告する。

## 設計原則

- 現在必要な設計だけを記載する
- 既存コードと既存設計のパターンを優先する
- 要件やIssueにない機能を追加しない
- 不明点を推測で補わない
- 将来の拡張だけを理由に抽象化しない
- 選択肢がある場合は、採用案と判断理由を示す
- 実装方法を過度に固定しない
- 同じ内容のドキュメントを重複して作成しない

## 承認後の処理

新規作成の場合、ファイル名は内容を表す短いkebab-caseにする。

```text
docs/database/contact-submissions.md
docs/api/contact-api.md
docs/features/contact-form.md
docs/architecture/mail-delivery.md
docs/decisions/mail-provider.md
```

同じ設計を扱う既存ドキュメントがある場合は、新規作成せず更新案を提示する。

既存ドキュメントを更新する場合も、人間のGOサインを得るまで変更しない。

## 禁止事項

- 承認前のファイル作成・更新
- 不明な仕様や設計判断の推測
- 要件外の機能追加
- 不要なクラス・レイヤー・パターンの提案
- 複数ドキュメントの同時作成
- コード・Migration・設定ファイルの変更
- Issue起票、ブランチ操作、コミット、Push、PR作成
- `references/`が存在しないことだけを理由とした停止

`Write`と`Edit`は、人間の承認後に対象ドキュメントを作成または更新する場合だけ使用する。

`Bash`は、Issue・Git履歴・既存情報の確認だけに使用する。

## 完了報告

- 種別
- 作成または更新したファイル
- 設計概要
- 未決事項
- 関連する要件・Issue

実装対象のIssueがある場合は、次の工程として案内する。

```text
次は /analyze <Issue番号> で、実装方針の作成を進めてください。
```

## 参照

- `.claude/rules/project-principles.md`
- `.claude/rules/laravel.md`
- `.claude/rules/database.md`
- `.claude/rules/api.md`
- `docs/database/`
- `docs/api/`
- `docs/features/`
- `docs/architecture/`
- `docs/decisions/`
