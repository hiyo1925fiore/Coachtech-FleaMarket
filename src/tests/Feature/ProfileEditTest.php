<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileEditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // データベースを完全にリフレッシュ
        $this->artisan('migrate:fresh');

        // 明示的にテーブルのAuto Incrementをリセット
        DB::statement('ALTER TABLE users AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE profiles AUTO_INCREMENT = 1');

        // テスト用ストレージの設定
        Storage::fake('public');
    }

    /**
     * テストケース14: ユーザー情報変更
     * 変更項目が初期値として過去設定されていること（プロフィール画像、ユーザー名、郵便番号、住所）
     */
    public function test_user_profile_edit_page_shows_initial_values()
    {
        // 1. ユーザーにログインする
        $user = User::factory()->create();
        $profile = Profile::factory()->create([
            'user_id' => $user->id,
            'img_url' => 'profile_images/sample1.jpg',
            'post_code' => '123-4567',
            'address' => '東京都目黒区下目黒2-20-28',
            'building' => 'いちご目黒ビル4階'
        ]);

        $this->actingAs($user);

        // 2. プロフィールページを開く
        $response = $this->get('/mypage/profile');

        $response->assertStatus(200);

        // 各項目の初期値が正しく表示されている
        $response->assertSee('value="' . $user->name . '"', false);
        $response->assertSee('value="' . $profile->post_code . '"', false);
        $response->assertSee('value="' . $profile->address . '"', false);
        $response->assertSee('value="' . $profile->building . '"', false);

        // プロフィール画像のパスが正しく設定されている
        $response->assertSee('storage/' . $profile->img_url, false);
    }

    /**
     * テストケース14: ユーザー情報変更
     * プロフィール情報を正常に更新できること
     */
    public function test_user_can_update_profile_successfully()
    {
        // 1. ユーザーにログインする
        $user = User::factory()->create(['name' => 'Original Name']);
        $profile = Profile::factory()->create([
            'user_id' => $user->id,
            'img_url' => 'profile_images/sample1.jpg',
            'post_code' => '111-1111',
            'address' => '東京都千代田区1-1-1'
        ]);

        $this->actingAs($user);

        // 2. プロフィール更新画面を開く
        $this->get('/mypage/profile')->assertStatus(200);

        // 3. 各項目に適切な情報を入力して保存する
        $file = UploadedFile::fake()->create('new_profile.jpg', 100, 'image/jpeg');

        $response = $this->put('/mypage/profile', [
            'name' => 'Updated Name',
            'img_url' => $file,
            'post_code' => '456-7890',
            'address' => '大阪府大阪市北区1-2-3',
            'building' => '更新ビル5階'
        ]);

        // リダイレクトされることを確認
        $response->assertRedirect();

        // ユーザー名が更新されている
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name'
        ]);

        // プロフィール情報が更新されている
        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'post_code' => '456-7890',
            'address' => '大阪府大阪市北区1-2-3',
            'building' => '更新ビル5階'
        ]);

        // 画像がアップロードされている
        $updatedProfile = Profile::where('user_id', $user->id)->first();
        $this->assertStringContainsString('profile_images/', $updatedProfile->img_url);
        Storage::disk('public')->assertExists($updatedProfile->img_url);
    }

    /**
     * テストケース14: ユーザー情報変更
     * 名前が入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_name_is_required()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->put('/mypage/profile', [
            'name' => '',
            'post_code' => '123-4567',
            'address' => '東京都渋谷区1-1-1'
        ]);

        // バリデーションエラーでリダイレクトされることを確認
        $response->assertStatus(302);

        $response->assertSessionHasErrors('name');
    }

    /**
     * テストケース14: ユーザー情報変更
     * 郵便番号の形式に誤りがある場合、バリデーションメッセージが表示される
     */
    public function test_post_code_format_validation()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        // 正しい形式（XXX-XXXX）以外はエラー
        $response = $this->put('/mypage/profile', [
            'name' => 'Test User',
            'post_code' => '1234567', // ハイフンなし
            'address' => '東京都渋谷区1-1-1'
        ]);

        // バリデーションエラーでリダイレクトされることを確認
        $response->assertStatus(302);

        $response->assertSessionHasErrors('post_code');
    }

    /**
     * テストケース14: ユーザー情報変更
     * 画像アップロードテスト
     */
    public function test_profile_image_upload()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $file = UploadedFile::fake()->create('profile.jpg', 100, 'image/jpeg');

        $response = $this->put('/mypage/profile', [
            'name' => 'Test User',
            'img_url' => $file,
            'post_code' => '123-4567',
            'address' => '東京都渋谷区1-1-1'
        ]);

        $response->assertRedirect();

        // 画像がprofile_imagesディレクトリに保存されている
        $updatedProfile = Profile::where('user_id', $user->id)->first();
        $this->assertStringStartsWith('profile_images/', $updatedProfile->img_url);
        Storage::disk('public')->assertExists($updatedProfile->img_url);
    }

    /**
     * テストケース14: ユーザー情報変更
     * 画像ファイル形式が指定と異なる場合、バリデーションメッセージを表示する
     */
    public function test_image_file_type_validation()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        // テキストファイルをアップロード（画像以外）
        $file = UploadedFile::fake()->create('document.txt', 100);

        $response = $this->put('/mypage/profile', [
            'name' => 'Test User',
            'img_url' => $file,
            'post_code' => '123-4567',
            'address' => '東京都渋谷区1-1-1'
        ]);

        // バリデーションエラーでリダイレクトされることを確認
        $response->assertStatus(302);

        $response->assertSessionHasErrors('img_url');
    }

    /**
     * テストケース14: ユーザー情報変更
     * 未認証ユーザーはアクセス不可
     */
    public function test_guest_cannot_access_profile_edit()
    {
        $response = $this->get('/mypage/profile');

        // ログインページにリダイレクトされる
        $response->assertRedirect('/login');
    }

    /**
     * テストケース14: ユーザー情報変更
     * 他のユーザーのプロフィール編集を防ぐテスト
     */
    public function test_user_cannot_edit_other_users_profile()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $profile2 = Profile::factory()->create(['user_id' => $user2->id]);

        $this->actingAs($user1);

        // user1でuser2のプロフィール更新を試行
        $response = $this->put('/mypage/profile', [
            'user_id' => $user2->id, // 他のユーザーID
            'name' => 'Hacked Name',
            'post_code' => '999-9999',
            'address' => 'ハッキング住所'
        ]);

        // user2のプロフィールが変更されていないことを確認
        $this->assertDatabaseMissing('profiles', [
            'user_id' => $user2->id,
            'post_code' => '999-9999',
            'address' => 'ハッキング住所'
        ]);
    }
}
