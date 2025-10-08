# coachtechフリマ
## 環境構築
**Dockerビルド**
1. `git@github.com:hiyo1925fiore/Coachtech-FleaMarket.git`
2. DockerDesktopアプリを立ち上げる
3. プロジェクト直下で、以下のコマンドを実行する
```
make init
```
※使用可能なコマンドはMakefileを参照
## userのログイン用初期データ
初期データ1
- メールアドレス: hoge1@example.com
- パスワード: password
  
初期データ2
- メールアドレス: hoge2@example.com
- パスワード: password

初期データ3
- メールアドレス: hoge3@example.com
- パスワード: password
## PHPUnitを利用したテストに関して
以下のコマンドを実行してください  
```
//テスト用データベースの作成
docker-compose exec mysql bash
mysql -u root -p
//パスワードはrootと入力
create database demo_test;
  
docker-compose exec php bash
php artisan migrate:fresh --env=testing
./vendor/bin/phpunit
```
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
