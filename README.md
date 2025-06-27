# coachtechフリマ
## 環境構築
**Dockerビルド**
1. `git@github.com:hiyo1925fiore/Coachtech-FleaMarket.git`
2. DockerDesktopアプリを立ち上げる
3. `docker-compose up -d --build`
> MacのM1・M2チップのPCの場合、no matching manifest for linux/arm64/v8 in the manifest list entriesのメッセージが表示されビルドができないことがあります。 エラーが発生する場合は、docker-compose.ymlファイルの「mysql」内に「platform」の項目を追加で記載してください
```
mysql:
    platform: linux/x86_64(この文追加)
    image: mysql:8.0.26
    environment:
```
**Laravel環境構築**
1. `docker-compose exec php bash`
2. `composer install`
3. `composer require livewire/livewire`
4. `composer require laravel/cashier`
5. 「.env.example」ファイルを 「.env」ファイルに命名を変更。または、新しく.envファイルを作成
6. .envに以下の環境変数を追加
```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
STRIPE_KEY=your-stripe-key（stripeの公開可能キー）
STRIPE_SECRET=your-stripe-secret（stripeのシークレットキー）
```
5. アプリケーションキーの作成
```
php artisan key:generate
```
6. マイグレーションの実行
```
php artisan migrate
```
7. シーディングの実行
```
php artisan db:seed
```
8. シンボリックリンクの作成
```
php artisan storage:link
```
## userのログイン用初期データ
初期データ1
- メールアドレス: hoge1@example.com
- パスワード: hoge1234  
初期データ2
- メールアドレス: hoge2@example.com
- パスワード: hoge5678
## 使用技術（実行環境）
- PHP 8.4.1
- Laravel 8.83.8
- MySQL 8.0.26
- Mailpit
## ER図
![er drawio](https://github.com/user-attachments/assets/caf79233-d1e8-4ef4-96c1-22edc6fa7194)


## URL
- 開発環境：http://localhost/
- phpMyAdmin：http://localhost:8080/
- Mailpit：http://localhost:8025/
