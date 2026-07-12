# 🔬 more-requirements — 要件詳細化

`/requirements`で作成した大枠の要件ドキュメントを、実装着手可能な粒度まで深掘りするスキル。

プロジェクト全体の大元要件を定義する最初の1回だけのルート（`/requirements` → `/more-requirements` → `/detail`）で使用する。個別の新機能・修正・改善は`/plan-docs`が起点。

## 使い方

```
/more-requirements docs/requirements/contact-form.md
```

## パイプライン

```
前提: /requirements 完了
入力: docs/requirements/ または docs/maintenance/ の要件ドキュメント
出力: 同ドキュメントの更新（新規ファイルは作成しない）
次:   必要に応じて /detail → /issue
```

## プロセス

1. 指定された要件ドキュメントを読み込む
2. 挙動のエッジケース、判定可能な受け入れ条件、対象外の境界、制約を2〜3問ずつ深掘り
3. 承認後、同じドキュメントを更新（項目構成は`/requirements`と共通、深さだけが変わる）

## ルール

- 承認なしにドキュメントを更新しない
- 新規ドキュメントを作成しない（`/requirements`が作成した1件を更新する）
- 未決事項を優先的に解消する
- 技術設計・Issue起票・実装は行わない
