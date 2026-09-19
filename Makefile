.PHONY: help install ensure-env up down clean restart build \
       fresh migrate seed test test-coverage \
       pint stan \
       shell-backend logs \
       github-labels

# デフォルト
help: ## ヘルプ表示
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

# --- セットアップ ---
install: ensure-env ## 初期セットアップ（クローン直後の初回のみ実行）
	docker compose build --no-cache
	docker compose up -d
	docker compose exec -T backend composer install
	docker compose exec -T backend sh -c '[ -f .env ] || cp .env.example .env'
	docker compose exec -T backend php artisan key:generate
	docker compose exec -T backend php artisan migrate:fresh --seed
	@echo ""
	@echo "==================================================================="
	@echo " セットアップ完了"
	@echo "-------------------------------------------------------------------"
	@echo "  backend:    http://localhost:8000"
	@echo "  phpMyAdmin: http://localhost:8080"
	@echo "  MailHog:    http://localhost:8025"
	@echo "==================================================================="

github-labels: ## GitHub Issue運用に必要なラベル(feat/fix/refactor/docs/style/chore)を作成（新規プロジェクトで1回実行）
	gh label create "feat" --description "新機能" --color "0e8a16" --force
	gh label create "fix" --description "不具合修正" --color "d93f0b" --force
	gh label create "refactor" --description "挙動を変えないコード整理" --color "fbca04" --force
	gh label create "docs" --description "ドキュメント変更" --color "1d76db" --force
	gh label create "style" --description "CSS・表示上の変更" --color "c5def5" --force
	gh label create "chore" --description "設定・環境・CI・保守作業" --color "cfd3d7" --force

ensure-env: ## .env を生成し HOST_UID/HOST_GID を補完（既存 .env も対象）
	@test -f .env || ( cp .env.example .env && echo ".env を .env.example から生成しました" )
	@grep -q "^HOST_UID=" .env || ( echo "HOST_UID=$$(id -u)" >> .env && echo "HOST_UID を .env に追記しました" )
	@grep -q "^HOST_GID=" .env || ( echo "HOST_GID=$$(id -g)" >> .env && echo "HOST_GID を .env に追記しました" )

# --- コンテナ管理 ---
up: ## コンテナ起動
	docker compose up -d

down: ## コンテナ停止（volume は保持）
	docker compose down

clean: ## コンテナ・volume を全削除（環境のクリーンリセット用）
	docker compose down -v

restart: ## コンテナ再起動
	docker compose restart

build: ## コンテナビルド
	docker compose build

logs: ## 全コンテナのログ表示
	@docker compose logs -f || true

# --- DB ---
fresh: ## DB初期化 + シード
	docker compose exec backend php artisan migrate:fresh --seed

migrate: ## マイグレーション実行
	docker compose exec backend php artisan migrate

seed: ## シーディング実行
	docker compose exec backend php artisan db:seed

# --- テスト ---
test: ## PHPUnit実行
	docker compose exec backend php artisan test

test-coverage: ## PHPUnit実行（カバレッジ計測。CI と同じ。pcov 必須）
	docker compose exec backend ./vendor/bin/phpunit --coverage-clover=coverage.xml

# --- フォーマット・リント ---
pint: ## PHP フォーマット（Laravel Pint）
	docker compose exec backend ./vendor/bin/pint

stan: ## PHP 静的解析（PHPStan / larastan）
	docker compose exec backend ./vendor/bin/phpstan analyse --memory-limit=2G --no-progress

# --- シェル ---
shell-backend: ## バックエンドコンテナにログイン
	docker compose exec backend bash
