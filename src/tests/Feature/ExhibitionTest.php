<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Exhibition;
use Database\Seeders\CategoriesTableSeeder;
use Database\Seeders\ConditionsTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ExhibitionTest extends TestCase
{
    use RefreshDatabase,WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        // データベースを完全にリフレッシュ
        $this->artisan('migrate:fresh');

        // 明示的にテーブルのAuto Incrementをリセット
        DB::statement('ALTER TABLE categories AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE conditions AUTO_INCREMENT = 1');

        // テスト用ストレージの設定
        Storage::fake('public');

        // シーダーを実行
        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ConditionsTableSeeder::class);
    }

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
     * テストケース15: 出品商品情報登録
     * 商品出品画面が正常に表示されること
     */
    public function test_exhibition_page_can_be_displayed()
    {
        // テストユーザーを作成
        $user = $this->createTestUser();

        // Seederのデータを使用してカテゴリ・商品の状態を作成
        $categories = Category::all();

        $conditions = Condition::all();

        // 1. ユーザーにログインする
        $response = $this->get('/login');
        $this->actingAs($user);
        $response->assertStatus(200);

        // 2. 商品出品画面を開く
        $response = $this->get('/sell');

        // ページが正常に表示されることを確認
        $response->assertStatus(200);
        $response->assertSee('exhibition');

        // Seederで作成されたカテゴリが選択肢として表示されることを確認
        $response->assertSee('ファッション');
        $response->assertSee('家電');
        $response->assertSee('インテリア');
        $response->assertSee('レディース');
        $response->assertSee('メンズ');
        $response->assertSee('コスメ');
        $response->assertSee('本');
        $response->assertSee('ゲーム');
        $response->assertSee('スポーツ');
        $response->assertSee('キッチン');
        $response->assertSee('ハンドメイド');
        $response->assertSee('アクセサリー');
        $response->assertSee('おもちゃ');
        $response->assertSee('ベビー・キッズ');

        // Seederで作成されたカテゴリが選択肢として表示されることを確認
        $response->assertSee('良好');
        $response->assertSee('目立った傷や汚れなし');
        $response->assertSee('やや傷や汚れあり');
        $response->assertSee('状態が悪い');
    }

    /**
     * テストケース15: 出品商品情報登録
     * 商品出品画面にて必要な情報が保存できること
     * （カテゴリ、商品の状態、商品名、商品の説明、販売価格）
     */
    public function test_user_can_save_exhibited_item_information()
    {
        // テストユーザーを作成
        $user = $this->createTestUser();

        // Seederのデータを使用してカテゴリ・商品の状態を作成
        $category = Category::where('category', 'ファッション')->first();

        $condition = Condition::where('condition', '目立った傷や汚れなし')->first();

        // 画像ファイルのモック
        $file = UploadedFile::fake()->create('test-product.jpg', 1024, 'image/jpeg');

        // 商品データ
        $exhibitionData = [
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'price' => 1000,
            'condition_id' => $condition->id,
            'description' => 'テスト商品の説明文です。',
            'category_id' => $category->id,
            'img_url' => $file,
        ];

        // 商品出品処理
        $response = $this->actingAs($user)
            ->post('/sell', $exhibitionData);

        // アサーション：リダイレクトされる
        $response->assertRedirect('/');

        // データベースに保存されることを確認
        $this->assertDatabaseHas('exhibitions', [
            'seller_id' => $user->id,
            'name' => 'テスト商品',
            'price' => 1000,
            'condition_id' => $condition->id,
            'description' => 'テスト商品の説明文です。',
        ]);

        // 中間テーブルにカテゴリが保存されることを確認
        $exhibition = Exhibition::where('name', 'テスト商品')->first();
        $this->assertTrue($exhibition->categories->contains($category));

        // 画像がアップロードされることを確認
        Storage::disk('public')->assertExists('img/' . $file->hashName());
    }
}
