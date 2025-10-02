<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    /**
     * テスト用ユーザーを作成
     */
    protected function createTestUser()
    {
        return User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
    }

    /**
     * テストケース2: ログイン機能
     * ログインページが正常に表示されることをテスト
     */
    public function test_login_page_can_be_displayed()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    /**
     * テストケース2: ログイン機能
     * メールアドレスが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_email_is_required_for_login()
    {
        // 1. ログインページを開く
        $response = $this->get('/login');
        $response->assertStatus(200);

        // 2. メールアドレスを入力せずに他の必要項目を入力する
        $loginData = [
            'email' => '', // メールアドレスを空にする
            'password' => 'password123',
        ];

        // 3. ログインボタンを押す
        $response = $this->post('/login', $loginData);

        // バリデーションエラーでリダイレクトされることを確認
        $response->assertStatus(302);

        // セッションにバリデーションエラーが含まれることを確認
        $response->assertSessionHasErrors(['email']);

        // 期待されるエラーメッセージを確認
        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください'
        ]);

        // ユーザーが認証されていないことを確認
        $this->assertGuest();
    }

    /**
     * テストケース2: ログイン機能
     * メールアドレスが無効なメール形式だった場合、バリデーションメッセージが表示される
     */
    public function test_login_fails_with_invalid_email_format()
    {
        $loginData = [
            'email' => 'invalid-email-format', // 無効なメール形式
            'password' => 'password123',
        ];

        $response = $this->post('/login', $loginData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email']);
        $response->assertSessionHasErrors([
            'email' => 'メールアドレス形式で入力してください'
        ]);

        $this->assertGuest();
    }

    /**
     * テストケース2: ログイン機能
     * パスワードが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_password_is_required_for_login()
    {
        // 1. ログインページを開く
        $response = $this->get('/login');
        $response->assertStatus(200);

        // 2. パスワードを入力せずに他の必要項目を入力する
        $loginData = [
            'email' => 'test@example.com',
            'password' => '', // パスワードを空にする
        ];

        // 3. ログインボタンを押す
        $response = $this->post('/login', $loginData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['password']);
        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください'
        ]);

        $this->assertGuest();
    }

    /**
     * テストケース2: ログイン機能
     * 入力情報が間違っている場合、バリデーションメッセージが表示される
     */
    public function test_login_fails_with_invalid_credentials()
    {
        // テスト用ユーザーを作成
        $this->createTestUser();

        // 1. ログインページを開く
        $response = $this->get('/login');
        $response->assertStatus(200);

        // 2. 必要項目を登録されていない情報を入力する
        $loginData = [
            'email' => 'wrong@example.com', // 存在しないメールアドレス
            'password' => 'wrongpassword',   // 間違ったパスワード
        ];

        // 3. ログインボタンを押す
        $response = $this->post('/login', $loginData);

        $response->assertStatus(302);

        // 認証エラーメッセージを確認
        $response->assertSessionHasErrors(['email']);
        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません'
        ]);

        // ユーザーが認証されていないことを確認
        $this->assertGuest();
    }

    /**
     * テストケース2: ログイン機能
     * 存在するメールアドレスで間違ったパスワードを入力した場合、バリデーションメッセージが表示される
     */
    public function test_login_fails_with_wrong_password()
    {
        // テスト用ユーザーを作成
        $user = $this->createTestUser();

        $loginData = [
            'email' => $user->email,
            'password' => 'wrongpassword', // 間違ったパスワード
        ];

        $response = $this->post('/login', $loginData);

        $response->assertStatus(302);

        $response->assertSessionHasErrors(['email']);
        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません'
        ]);

        $this->assertGuest();
    }

    /**
     * テストケース2: ログイン機能
     * 正しい情報が入力された場合、ログイン処理が実行される
     */
    public function test_user_can_login_with_correct_credentials()
    {
        // テスト用ユーザーを作成
        $user = $this->createTestUser();

        // 1. ログインページを開く
        $response = $this->get('/login');
        $response->assertStatus(200);

        // 2. 全ての必要項目を入力する
        $loginData = [
            'email' => $user->email,
            'password' => 'password123',
        ];

        // 3. ログインボタンを押す
        $response = $this->post('/login', $loginData);

        // ログイン処理が実行される - リダイレクトを確認
        $response->assertStatus(302);

        // ログイン後のリダイレクト先を確認
        $response->assertRedirect('/?page=mylist');

        // ユーザーが認証されていることを確認
        $this->assertAuthenticated();

        // 認証されたユーザーが正しいことを確認
        $this->assertEquals($user->id, auth()->id());
    }

    /**
     * テストケース2: ログイン機能
     * メール未認証ユーザーのログインテスト（もしメール認証が必要な場合）
     */
    public function test_unverified_user_cannot_login()
    {
        // メール未認証のユーザーを作成
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'unverified@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => null, // メール未認証
        ]);

        $loginData = [
            'email' => $user->email,
            'password' => 'password123',
        ];

        $response = $this->post('/login', $loginData);

        // メール認証が必要な場合の処理（アプリケーションの設定による）
        // 認証されていないことを確認
        $this->assertGuest();
    }

    /**
     * テストケース2: ログイン機能
     * 既にログイン済みのユーザーがログインページにアクセスした場合、商品一覧画面（マイリスト）が表示される
     */
    public function test_authenticated_user_is_redirected_from_login_page()
    {
        // ユーザーを作成してログイン
        $user = $this->createTestUser();
        $this->actingAs($user);

        // ログインページにアクセス
        $response = $this->get('/login');

        // 商品一覧画面（マイリスト）にリダイレクトされることを確認
        $response->assertStatus(302);
        $response->assertRedirect('/?page=mylist');
    }
}
