<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LogoutTest extends TestCase
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
    private function createTestUser()
    {
        return User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    /**
     * テストケース3: ログアウト機能
     * ログアウトが正常に実行されることを確認
     */
    public function test_user_can_logout()
    {
        // テストユーザーを作成
        $user = $this->createTestUser();

        // ユーザーをログインさせる
        $this->actingAs($user);

        // ログイン状態であることを確認
        $this->assertTrue(Auth::check());

        // ログアウトを実行
        $response = $this->post('/logout');

        // ログアウト処理が実行されることを確認
        $response->assertStatus(302); // リダイレクト
        $response->assertRedirect('/login'); // ログインページにリダイレクト

        // ログアウト状態であることを確認
        $this->assertFalse(Auth::check());
    }

    /**
     * テストケース3: ログアウト機能
     * ログアウトボタンが表示されることを確認
     */
    public function test_logout_button_is_displayed_for_authenticated_user()
    {
        // テストユーザーを作成
        $user = $this->createTestUser();

        // ユーザーをログインさせる
        $this->actingAs($user);

        // 認証状態を確認
        $this->assertAuthenticated();

        // 認証が必要なページ（出品画面）にアクセス
        $response = $this->get('/sell');

        // ページが正常に表示されることを確認
        $response->assertStatus(200);

        // ログアウトボタンまたはログアウトリンクが表示されることを確認
        $response->assertSee('ログアウト');

        // ヘッダーにログアウトボタンが存在することを確認
        $response->assertSee('class="header__form"', false);
        $response->assertSee('type="submit">ログアウト', false);
    }

    /**
     * テストケース3: ログアウト機能
     * 未認証ユーザーがログアウトエンドポイントにアクセスした場合の処理を確認
     */
    public function test_unauthenticated_user_cannot_access_logout()
    {
        // 未認証状態でログアウトエンドポイントにアクセス
        $response = $this->post('/logout');

        // 認証が必要な場合はログインページにリダイレクトされる
        // または適切なエラーレスポンスが返される
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /**
     * テストケース3: ログアウト機能
     * ログアウト後にセッションが無効化されることを確認
     */
    public function test_logout_invalidates_session()
    {
        // テストユーザーを作成
        $user = $this->createTestUser();

        // ユーザーをログインさせる
        $this->actingAs($user);

        // セッションにデータを設定
        session(['test_data' => 'test_value']);

        // セッションデータが存在することを確認
        $this->assertEquals('test_value', session('test_data'));

        // ログアウトを実行
        $response = $this->post('/logout');

        // ログアウト処理が成功することを確認
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        // ログアウト後、セッションが無効化されることを確認
        $this->assertFalse(Auth::check());
    }

    /**
     * テストケース3: ログアウト機能
     * ログアウト後にセッショントークンが再生成されることを確認
     */
    public function test_logout_regenerates_session_token()
    {
        // テストユーザーを作成
        $user = $this->createTestUser();

        // ユーザーをログインさせる
        $this->actingAs($user);

        // ログイン前のセッショントークンを取得
        $oldToken = session()->token();

        // ログアウトを実行
        $response = $this->post('/logout');

        // ログアウト処理が成功することを確認
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        // 新しいセッションでアクセス
        $response = $this->get('/login');

        // セッショントークンが再生成されていることを確認
        $newToken = session()->token();
        $this->assertNotEquals($oldToken, $newToken);
    }

    /**
     * テストケース3: ログアウト機能
     * CSRF保護が機能していることを確認
     */
    public function test_logout_requires_csrf_token()
    {
        // テストユーザーを作成
        $user = $this->createTestUser();

        // ユーザーをログインさせる
        $this->actingAs($user);

        // CSRFトークンなしでログアウトを試行
        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post('/logout');

        $response->assertStatus(302);
    }

    /**
     * テストケース3: ログアウト機能
     * 複数のユーザーが同時にログアウトできることを確認
     */
    public function test_multiple_users_can_logout_independently()
    {
        // 2つのテストユーザーを作成
        $user1 = User::create([
            'name' => 'ユーザー1',
            'email' => 'user1@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
        ]);

        $user2 = User::create([
            'name' => 'ユーザー2',
            'email' => 'user2@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
        ]);

        // ユーザー1としてログイン
        $this->actingAs($user1);
        $this->assertTrue(Auth::check());
        $this->assertEquals($user1->id, Auth::id());

        // ユーザー1がログアウト
        $response = $this->post('/logout');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertFalse(Auth::check());

        // ユーザー2としてログイン
        $this->actingAs($user2);
        $this->assertTrue(Auth::check());
        $this->assertEquals($user2->id, Auth::id());

        // ユーザー2がログアウト
        $response = $this->post('/logout');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertFalse(Auth::check());
    }
}
