<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Exhibition;
use App\Models\Purchase;
use App\Models\Favorite;
use Database\Seeders\CategoriesTableSeeder;
use Database\Seeders\ConditionsTableSeeder;
use Database\Seeders\ExhibitionsTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;

class ItemListTest extends TestCase
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
        DB::statement('ALTER TABLE favorites AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE purchases AUTO_INCREMENT = 1');

        // ユーザーを順番に作成
        $this->user = User::create([
            'name' => 'テストユーザー1',
            'email' => 'test1@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->otherUser = User::create([
            'name' => 'テストユーザー2',
            'email' => 'test2@example.com',
            'password' => bcrypt('password'),
        ]);

        // シーダー実行
        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ConditionsTableSeeder::class);
        $this->seed(ExhibitionsTableSeeder::class);
    }

    /**
     * テストケース4: 商品一覧取得
     * 全商品を取得できる（シーダーデータを使用）
     */
    public function test_can_get_all_exhibitions_with_seeder_data()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('itemlist');

        // シーダーで作成された具体的な商品名をチェック
        $response->assertSee('腕時計');
        $response->assertSee('HDD');
    }

    /**
     * テストケース4: 商品一覧取得
     * 全商品を取得できる
     */
    public function test_can_get_all_exhibitions()
    {
        // テストデータ作成
        $exhibitions = Exhibition::all();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('itemlist');

        // 全ての商品が表示されることを確認
        foreach ($exhibitions as $exhibition) {
            $response->assertSee($exhibition->name);
        }
    }

    /**
     * テストケース4: 商品一覧取得
     * 購入済み商品は「Sold」と表示される
     */
    public function test_purchased_exhibitions_show_sold_label()
    {
        // ユーザーを作成
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // ユーザーの出品を作成
        $availableExhibition = Exhibition::factory()->forUser($user)->create();
        $soldExhibition = Exhibition::factory()->forUser($otherUser)->create();

        // 商品を購入
        Purchase::factory()->create([
            'user_id' => $user->id,
            'exhibition_id' => $soldExhibition->id,
        ]);

        // 商品一覧ページにアクセス
        $response = $this->get('/');

        $response->assertStatus(200);

        // 未購入商品は通常表示
        $response->assertSee($availableExhibition->name);

        // 購入済み商品は「Sold」ラベルが表示される
        $response->assertSee('Sold');
        $response->assertSee($soldExhibition->name);
    }

    /**
     * テストケース4: 商品一覧取得
     * 自分が出品した商品は表示されない
     */
    public function test_own_exhibitions_are_not_displayed()
    {
        // テストユーザーを作成
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

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

        // 商品一覧ページにアクセス
        $response = $this->get('/');

        // 自分の出品は表示されない
        $response->assertDontSee($ownExhibition->name);
        // 他人の出品は表示される
        $response->assertSee($otherExhibition->name);
    }

    /**
     * テストケース5: マイリスト一覧取得
     * いいねした商品だけが表示される
     */
    public function test_mylist_shows_only_favorited_exhibitions()
    {
        // 商品を作成
        $favoritedExhibition = Exhibition::factory()->create([
            'seller_id' => $this->otherUser->id
        ]);
        $notFavoritedExhibition = Exhibition::factory()->create([
            'seller_id' => $this->otherUser->id
        ]);

        // いいねを追加
        Favorite::factory()->create([
            'user_id' => $this->user->id,
            'exhibition_id' => $favoritedExhibition->id
        ]);

        $response = $this->actingAs($this->user)->get('/?page=mylist');

        $response->assertStatus(200);
        $response->assertViewIs('itemlist');
        $response->assertSee($favoritedExhibition->name);
        $response->assertDontSee($notFavoritedExhibition->name);
    }

    /**
     * テストケース5: マイリスト一覧取得
     * 購入済み商品は「Sold」と表示される
     */
    public function test_mylist_purchased_exhibitions_show_sold_label()
    {
        // いいねして購入済みの商品を作成
        $exhibition = Exhibition::factory()->create([
            'seller_id' => $this->otherUser->id
        ]);

        Favorite::factory()->create([
            'user_id' => $this->user->id,
            'exhibition_id' => $exhibition->id
        ]);

        Purchase::factory()->create([
            'user_id' => $this->user->id,
            'exhibition_id' => $exhibition->id
        ]);

        $response = $this->actingAs($this->user)->get('/?page=mylist');

        $response->assertStatus(200);
        $response->assertSee('Sold');
        $response->assertSee($exhibition->name);
    }

    /**
     * テストケース5: マイリスト一覧取得
     * 未認証の場合は何も表示されない
     */
    public function test_mylist_shows_nothing_for_unauthenticated_users()
    {
        $exhibition = Exhibition::factory()->create([
            'seller_id' => $this->otherUser->id
        ]);

        $response = $this->get('/?page=mylist');

        $response->assertStatus(200);
        $response->assertViewIs('itemlist');
        $response->assertSee('表示する商品がありません');
    }

    /**
     * テストケース6: 商品検索機能
     * 「商品名」で部分一致検索ができる
     */
    public function test_can_search_exhibitions_by_name()
    {
        $matchingExhibition = Exhibition::factory()->create([
            'name' => 'テスト商品',
            'seller_id' => $this->otherUser->id
        ]);

        $nonMatchingExhibition = Exhibition::factory()->create([
            'name' => '別の商品',
            'seller_id' => $this->otherUser->id
        ]);

        $response = $this->get('/?searchTerm=テスト');

        $response->assertStatus(200);
        $response->assertSee($matchingExhibition->name);
        $response->assertDontSee($nonMatchingExhibition->name);
    }

    /**
     * テストケース6: 商品検索機能
     * 検索状態がマイリストでも保持されている
     */
    public function test_search_state_persists_in_mylist()
    {
        $matchingExhibition = Exhibition::factory()->create([
            'name' => 'テスト商品',
            'seller_id' => $this->otherUser->id
        ]);

        $nonMatchingExhibition = Exhibition::factory()->create([
            'name' => '別の商品',
            'seller_id' => $this->otherUser->id
        ]);

        // 両方にいいねを追加
        Favorite::factory()->create([
            'user_id' => $this->user->id,
            'exhibition_id' => $matchingExhibition->id
        ]);

        Favorite::factory()->create([
            'user_id' => $this->user->id,
            'exhibition_id' => $nonMatchingExhibition->id
        ]);

        $response = $this->actingAs($this->user)->get('/?page=mylist&searchTerm=テスト');

        $response->assertStatus(200);
        $response->assertSee($matchingExhibition->name);
        $response->assertDontSee($nonMatchingExhibition->name);
    }

    /**
     * テストケース6: 商品検索機能
     * 検索結果が表示される
     */
    public function test_search_results_are_displayed()
    {
        $exhibition = Exhibition::factory()->create([
            'name' => 'テスト商品',
            'seller_id' => $this->otherUser->id
        ]);

        $response = $this->get('/?searchTerm=テスト');

        $response->assertStatus(200);
        $response->assertViewIs('itemlist');
        $response->assertSee($exhibition->name);
    }

    /**
     * テストケース6: 商品検索機能
     * 検索キーワードが保持されている
     */
    public function test_search_keyword_is_preserved()
    {
        Exhibition::factory()->create([
            'name' => 'テスト商品',
            'seller_id' => $this->otherUser->id
        ]);

        // Livewireコンポーネントを直接テスト
        Livewire::actingAs($this->user)
            ->test('item-search-component')
            ->set('searchTerm', 'テスト')
            ->assertSet('searchTerm', 'テスト'); // プロパティが正しく設定されているか確認

        // ItemListComponentでも検索ワードが保持されているか確認
        Livewire::actingAs($this->user)
            ->test('item-list-component')
            ->call('updateSearchTerm', 'テスト')
            ->assertSet('searchTerm', 'テスト')
            ->assertSee('テスト商品'); // 検索結果として商品が表示される
    }

    /**
     * テストケース6: 商品検索機能
     * 空の検索でも正常に動作することを確認
     */
    public function test_empty_search_shows_all_exhibitions()
    {
        $exhibitions = Exhibition::factory()->count(2)->create([
            'seller_id' => $this->otherUser->id
        ]);

        $response = $this->get('/?searchTerm=');

        $response->assertStatus(200);
        foreach ($exhibitions as $exhibition) {
            $response->assertSee($exhibition->name);
        }
    }
}
