# 🛠️ detail — 技術設計ドキュメント作成

指定された種別の設計案を整理し、承認後に技術設計ドキュメントを1件作成または更新するスキル。

`/requirements` → `/more-requirements` → `/detail`という、プロジェクト全体の大元要件を作る最初の1回だけのルートで使用する。以降の個別機能追加は`/make-docs`が担当する。

## 使い方

```
/detail database お問い合わせデータの保存設計
/detail api お問い合わせ送信API
/detail feature お問い合わせ機能
/detail architecture メール送信構成
/detail decision メール送信方式の選定
```

## パイプライン

```
前提: /more-requirements 完了（初回チェーン）
出力: docs/database/ | docs/api/ | docs/features/ | docs/architecture/ | docs/decisions/
次:   /issue <要件ドキュメントのパス>
```

## 種別

| 種別           | 保存先                |
| -------------- | --------------------- |
| `database`     | `docs/database/`      |
| `api`          | `docs/api/`           |
| `feature`      | `docs/features/`      |
| `architecture` | `docs/architecture/`  |
| `decision`     | `docs/decisions/`     |

## プロセス

1. 種別に対応するテンプレート（`references/`）があれば読む。なければ既存の同種ドキュメントを参考にする
2. 関連する要件・Issue・既存ドキュメント・既存コードを調査し、重複・矛盾・影響範囲を確認
3. 不足情報を2〜3問ずつ質問
4. 設計案を提示 → 承認
5. 承認後、1件だけ作成または更新

## ルール

- 承認なしにファイルを作成・更新しない
- 要件やIssueにない機能を追加しない
- 不要なクラス・レイヤー・パターンを提案しない
- 複数ドキュメントを同時に作成しない
- コード・Migration・設定ファイルの変更、Issue起票は行わない
