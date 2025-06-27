<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Profile;

use App\Models\Condition;
use App\Models\Exhibition;
use App\Models\Purchase;
use App\Models\Favorite;
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\CategoriesTableSeeder;
use Database\Seeders\ConditionsTableSeeder;
use Database\Seeders\ExhibitionsTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfileTest extends TestCase
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
        DB::statement('ALTER TABLE conditions AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE purchases AUTO_INCREMENT = 1');

        // シーダー実行
        $this->seed(UsersTableSeeder::class);
        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ConditionsTableSeeder::class);
        $this->seed(ExhibitionsTableSeeder::class);
    }

    /**
     * テスト用ユーザー1を作成
     */
    private function createTestUser()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        Profile::factory()->create([
            'user_id' => $user->id,
            'img_url' => 'profile_images/マーガレット.jpg',
        ]);

        return $user;
    }

    /**
     * テスト用ユーザー2を作成
     */
    private function createTestOtherUser()
    {
        $otherUser = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        Profile::factory()->create([
            'user_id' => $otherUser->id,
            'img_url' => '',
        ]);

        return $otherUser;
    }


    /**
     * テストケース13: ユーザー情報取得
     * プロフィール情報が表示される
     * （プロフィール画像、ユーザー名）
     */
    public function test_profile_is_displayed()
    {
        // テストユーザーを作成
        $user = $this->createTestUser();

        // 1. ユーザーにログインする
        $response = $this->get('/login');
        $this->actingAs($user);
        $response->assertStatus(200);

        // 2. プロフィールページを開く
        $response = $this->get('/mypage');

        // ページが正常に表示されることを確認
        $response->assertStatus(200);

        // プロフィール情報が表示されていることを確認
        $response->assertSee($user->name);
        $response->assertSee($user->profile->img_url);
    }

    /**
     * テストケース13: ユーザー情報取得
     * 自分が出品した商品が表示される
     */
    public function test_only_own_exhibitions_are_displayed()
    {
        // テストユーザーを作成
        $user = $this->createTestUser();
        $otherUser = $this->createTestOtherUser();

        // 自分の出品と他人の出品を作成
        $ownExhibition = Exhibition::factory()->forUser($user)->create();
        $otherExhibition = Exhibition::factory()->forUser($otherUser)->create();

        // 自分で自分の商品を購入（通常はありえないが、テスト用）
        Purchase::factory()->create([
            'user_id' => $user->id,
            'exhibition_id' => $ownExhibition->id,
        ]);

        // 他人の商品を購入
        Purchase::factory()->create([
            'user_id' => $user->id,
            'exhibition_id' => $otherExhibition->id,
        ]);

        // ユーザーでログイン
        $this->actingAs($user);

        // プロフィールページ（出品した商品一覧）を開く
        $response = $this->get('mypage?page=sell');

        // 他人の出品は表示されない
        $response->assertDontSee($otherExhibition->name);
        // 自分の出品は表示される
        $response->assertSee($ownExhibition->name);
    }

    /**
     * テストケース13: ユーザー情報取得
     * 自分が購入した商品が表示される
     */
    public function test_only_purchased_exhibitions_are_displayed()
    {
        // テストユーザーを作成
        $user = $this->createTestUser();
        $otherUser = $this->createTestOtherUser();

        // ユーザーの出品を作成
        $availableExhibition = Exhibition::factory()->forUser($user)->create();
        $soldExhibition = Exhibition::factory()->forUser($otherUser)->create();

        // 商品を購入
        Purchase::factory()->create([
            'user_id' => $user->id,
            'exhibition_id' => $soldExhibition->id,
        ]);

        // ユーザーでログイン
        $this->actingAs($user);

        // プロフィールページ（購入した商品一覧）を開く
        $response = $this->get('mypage?page=buy');

        // 未購入商品は表示されない
        $response->assertDontSee($availableExhibition->name);

        // 購入済み商品は「Sold」ラベル付きで表示される
        $response->assertSee('Sold');
        $response->assertSee($soldExhibition->name);
    }
}
