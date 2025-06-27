<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Profile;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Exhibition;
use App\Models\Purchase;
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\ConditionsTableSeeder;
use Database\Seeders\ExhibitionsTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        // データベースを完全にリフレッシュ
        $this->artisan('migrate:fresh');

        // 明示的にテーブルのAuto Incrementをリセット
        DB::statement('ALTER TABLE users AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE conditions AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE exhibitions AUTO_INCREMENT = 1');

        // テスト用ストレージの設定
        Storage::fake('public');

        // シーダー実行
        $this->seed(ConditionsTableSeeder::class);
    }

    /**
     * テストケース10: 商品購入機能
     * 「購入する」ボタンを押すと購入が完了する
     */
    public function test_click_the_purchase_button_to_complete_the_purchase()
    {
        // ユーザーを作成
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // 商品データ作成
        $exhibition = Exhibition::factory()->forUser($otherUser)->create();

        // セッションに配送先情報を設定
        session([
            'shipping_address' => [
                'post_code' => '774-6874',
                'address' => 'test address',
                'building' => 'test building'
            ]
        ]);

        // ユーザーでログイン
        $this->actingAs($user);

        // 2. 商品購入画面を開く
        $response = $this->get('purchase/:1');

        $response->assertStatus(200);
        $response->assertViewIs('purchase');

        //「購入する」ボタンを押す
        $purchase = Purchase::factory()->raw([
            'user_id' => $user->id,
            'exhibition_id' => $exhibition->id,
        ]);
        $response = $this->post("/purchase/:1", $purchase);

        // 商品一覧画面にリダイレクトされることを確認
        $response->assertStatus(302);
        $response->assertRedirect('/');

        //購入情報がデータベースに存在することを確認
        $this->assertDatabasehas('purchases', [
            'exhibition_id' => $exhibition->id,
            'user_id' => $user->id,
            'payment' =>  $purchase['payment'],
            'post_code' => '774-6874',  // セッションから取得される値
            'address' =>  'test address',  // セッションから取得される値
            'building' =>  'test building'  // セッションから取得される値
        ]);
    }

    /**
     * テストケース10: 商品購入機能
     * 購入した商品は商品一覧画面にて「Sold」と表示される
     */
    public function test_purchased_items_show_sold_label()
    {
        // ユーザーを作成
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // 商品データ作成
        $exhibition = Exhibition::factory()->forUser($otherUser)->create();

        // 商品一覧画面にアクセスしてSoldラベルが表示されないことを確認
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('Sold');
        $response->assertSee($exhibition->name);


        // セッションに配送先情報を設定
        session([
            'shipping_address' => [
                'post_code' => '774-6874',
                'address' => 'test address',
                'building' => 'test building'
            ]
        ]);

        // ユーザーでログイン
        $this->actingAs($user);

        // 2. 商品購入画面を開く
        $response = $this->get('purchase/:1');

        $response->assertStatus(200);
        $response->assertViewIs('purchase');

        //「購入する」ボタンを押す
        $purchase = Purchase::factory()->raw([
            'user_id' => $user->id,
            'exhibition_id' => $exhibition->id,
        ]);
        $response = $this->post("/purchase/:1", $purchase);

        // 商品一覧画面にリダイレクトされることを確認
        $response->assertStatus(302);
        $response->assertRedirect('/');

        // 商品一覧画面にアクセス
        $response = $this->get('/');

        $response->assertStatus(200);


        // 購入済み商品は「Sold」ラベルが表示される
        $response->assertSee('Sold');
        $response->assertSee($exhibition->name);

    }

    /**
     * テストケース10: 商品購入機能
     * 「プロフィール/購入した商品一覧」に追加されている
     */
    public function test_purchased_items_are_displayed_on_purchased_item_list_screen()
    {
        // ユーザーを作成
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        //商品を購入するユーザーのプロフィールを作成
        Profile::factory()->create([
            'user_id' => $user->id,
            'img_url' => 'profile_images/マーガレット.jpg',
        ]);

        // 商品データ作成
        $exhibition = Exhibition::factory()->forUser($otherUser)->create();

        // ユーザーでログイン
        $this->actingAs($user);

        // プロフィール/購入した商品一覧にアクセスして商品が表示されないことを確認
        $response = $this->get('/mypage?page=buy');

        $response->assertStatus(200);
        $response->assertDontSee('Sold');
        $response->assertDontSee($exhibition->name);


        // セッションに配送先情報を設定
        session([
            'shipping_address' => [
                'post_code' => '774-6874',
                'address' => 'test address',
                'building' => 'test building'
            ]
        ]);

        // 2. 商品購入画面を開く
        $response = $this->get('purchase/:1');

        $response->assertStatus(200);
        $response->assertViewIs('purchase');

        //「購入する」ボタンを押す
        $purchase = Purchase::factory()->raw([
            'user_id' => $user->id,
            'exhibition_id' => $exhibition->id,
        ]);
        $response = $this->post("/purchase/:1", $purchase);

        // 商品一覧画面にリダイレクトされることを確認
        $response->assertStatus(302);
        $response->assertRedirect('/');

        // 商品一覧画面にアクセス
        $response = $this->get('/mypage?page=buy');

        $response->assertStatus(200);


        // 購入済み商品が表示されることを確認
        $response->assertSee('Sold');
        $response->assertSee($exhibition->name);
    }
}
