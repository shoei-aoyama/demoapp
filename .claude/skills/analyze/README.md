# 🔎 analyze — 実装方針の作成

GitHub Issue・関連仕様・既存コードを調査し、人間が承認する実装方針を作成してIssueコメントへ記録する読み取り専用スキル。

## 使い方

```
/analyze 12
```

## パイプライン

```
前提: 対象Issueが起票済みであること
読込: Issue本文・コメント / docs/requirements/ 等 / 関連する既存コード
出力: Issueコメントへの実装方針（承認後）
次:   /implement <Issue番号>
```

## 手順

1. `gh issue view`でIssue本文・コメントを取得し、目的・受け入れ条件・対象外を整理
2. 関連する`docs/`配下の仕様を確認（`requirements/` `features/` `architecture/` `decisions/` `development/`）
3. Issueに直接関係する既存コードを調査（再利用できる処理・影響範囲・破壊的変更の有無）
4. 実装方針案（ブランチ構成・変更ファイル・事前確認・動作確認）を提示
5. GOサイン後、`## 実装方針（承認済み）`としてIssueコメントへ記録

## 判定

`READY` / `NEEDS_CLARIFICATION` / `BLOCKED`

## ルール

- コード・ローカルファイル・ブランチは変更しない
- 不明な仕様を推測しない
- 承認前にIssueコメントを投稿しない
- 未解決の仕様判断を「事前確認」に残したまま`READY`にしない
