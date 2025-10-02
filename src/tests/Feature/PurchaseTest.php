<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Profile;
use App\Models\Exhibition;
use App\Models\Purchase;
use Database\Seeders\ConditionsTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        // シーダー実行
        $this->seed(ConditionsTableSeeder::class);

        // ユーザーを作成
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();

        // 商品を購入するユーザーのプロフィールを作成
        $this->profile = Profile::factory()->create([
            'user_id' => $this->user->id,
            'img_url' => 'profile_images/マーガレット.jpg',
            'post_code' => '123-4567',
            'address' => '東京都目黒区下目黒2-20-28',
            'building' => 'いちご目黒ビル2階',
        ]);

        // 商品データ作成
        $this->exhibition = Exhibition::factory()->forUser($this->otherUser)->create();
    }

    /**
     * テストケース10: 商品購入機能
     * 「購入する」ボタンを押すと購入が完了する
     */
    public function test_click_the_purchase_button_to_complete_the_purchase()
    {
        // セッションに配送先情報を設定
        session([
            'shipping_address' => [
                'post_code' => '774-6874',
                'address' => 'test address',
                'building' => 'test building'
            ]
        ]);

        // ユーザーでログイン
        $this->actingAs($this->user);

        // 2. 商品購入画面を開く
        $response = $this->get('purchase/:1');

        $response->assertStatus(200);
        $response->assertViewIs('purchase');

        //「購入する」ボタンを押す
        $purchase = Purchase::factory()->raw([
            'user_id' => $this->user->id,
            'exhibition_id' => $this->exhibition->id,
        ]);
        $response = $this->post("/purchase/:1", $purchase);

        // 商品一覧画面にリダイレクトされることを確認
        $response->assertStatus(302);
        $response->assertRedirect('/');

        //購入情報がデータベースに存在することを確認
        $this->assertDatabasehas('purchases', [
            'exhibition_id' => $this->exhibition->id,
            'user_id' => $this->user->id,
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
        // 商品一覧画面にアクセスしてSoldラベルが表示されないことを確認
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('Sold');
        $response->assertSee($this->exhibition->name);


        // セッションに配送先情報を設定
        session([
            'shipping_address' => [
                'post_code' => '774-6874',
                'address' => 'test address',
                'building' => 'test building'
            ]
        ]);

        // ユーザーでログイン
        $this->actingAs($this->user);

        // 2. 商品購入画面を開く
        $response = $this->get('purchase/:1');

        $response->assertStatus(200);
        $response->assertViewIs('purchase');

        //「購入する」ボタンを押す
        $purchase = Purchase::factory()->raw([
            'user_id' => $this->user->id,
            'exhibition_id' => $this->exhibition->id,
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
        $response->assertSee($this->exhibition->name);

    }

    /**
     * テストケース10: 商品購入機能
     * 「プロフィール/購入した商品一覧」に追加されている
     */
    public function test_purchased_items_are_displayed_on_purchased_item_list_screen()
    {
        // ユーザーでログイン
        $this->actingAs($this->user);

        // プロフィール/購入した商品一覧にアクセスして商品が表示されないことを確認
        $response = $this->get('/mypage?page=buy');

        $response->assertStatus(200);
        $response->assertDontSee('Sold');
        $response->assertDontSee($this->exhibition->name);


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
            'user_id' => $this->user->id,
            'exhibition_id' => $this->exhibition->id,
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
        $response->assertSee($this->exhibition->name);
    }

    /**
     * テストケース11: 支払い方法選択機能
     * 小計画面で変更が反映される
     */
    public function test_payment_method_selection_is_displayed()
    {
        // 1. 商品購入画面にアクセス
        $response = $this->actingAs($this->user)
            ->get(route('purchase.show', $this->exhibition->id));

        $response->assertStatus(200);

        // 2. 支払い方法の選択肢が表示されているか確認
        $response->assertSee('支払い方法');
        $response->assertSee('選択してください');
        $response->assertSee('コンビニ支払い');
        $response->assertSee('カード支払い');

        // 3. 小計画面の支払い方法表示エリアが存在するか確認
        $response->assertSee('class="purchase-info__payment"', false);

        // 4. JavaScript（payment.js）が読み込まれているか確認
        $response->assertSee('payment.js');
    }

    public function test_payment_method_selection_with_old_value()
    {
        // old値を含めて購入画面にアクセス
    $response = $this->actingAs($this->user)
        ->withSession(['_old_input' => ['payment' => '1']])
        ->get(route('purchase.show', $this->exhibition->id));

    $response->assertStatus(200);

    // old値'1'の場合、コンビニ支払いが選択されていることを確認
    $response->assertSee('<option value="1" selected', false);

    // デフォルトの「選択してください」は選択されていないことを確認
    $response->assertDontSee('<option disabled selected>選択してください</option>', false);
    }

    /**
     * テストケース12: 配送先変更機能（表示機能）
     * 送付先住所変更画面にて登録した住所が商品購入画面に反映されている
     */
    public function test_shipping_address_is_displayed_on_purchase_page()
    {
        // 1. 商品購入画面にアクセス
        $response = $this->actingAs($this->user)
            ->get(route('purchase.show', $this->exhibition->id));

        $response->assertStatus(200);

        // 2. プロフィールの住所情報が表示されているか確認
        $response->assertSee('配送先');
        $response->assertSee($this->profile->post_code);
        $response->assertSee($this->profile->address);
        $response->assertSee($this->profile->building);

        // 3. 変更リンクが表示されているか確認
        $response->assertSee('変更する');
        $response->assertSee(route('purchase.address.edit', $this->exhibition->id));
    }

    /**
     * テストケース12: 配送先変更機能
     * 送付先住所変更画面にて登録した住所が商品購入画面に反映されている
     */
    public function test_shipping_address_change_is_reflected()
    {
        // 1. 商品購入画面にアクセス（セッションに配送先情報を設定するため）
        $this->actingAs($this->user)
            ->get(route('purchase.show', $this->exhibition->id));

        // 2. 住所変更画面にアクセス
        $response = $this->actingAs($this->user)
            ->get(route('purchase.address.edit', $this->exhibition->id));

        $response->assertStatus(200);
        $response->assertSee('住所の変更');

        // 3. 現在の住所がプレースホルダーとして表示されているか確認
        $response->assertSee($this->profile->post_code);
        $response->assertSee($this->profile->address);
        $response->assertSee($this->profile->building);

        // 4. 新しい住所に変更
        $newAddressData = [
            'post_code' => '111-2222',
            'address' => '大阪府大阪市北区梅田1-1-1',
            'building' => '梅田ビル3階',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('purchase.address.update', $this->exhibition->id), $newAddressData);

        // 5. 商品購入画面にリダイレクトされるか確認
        $response->assertRedirect(route('purchase.show', $this->exhibition->id));

        // 6. セッションに新しい住所が保存されているか確認
        $this->assertEquals($newAddressData['post_code'], session('shipping_address')['post_code']);
        $this->assertEquals($newAddressData['address'], session('shipping_address')['address']);
        $this->assertEquals($newAddressData['building'], session('shipping_address')['building']);

        // 7. 商品購入画面で新しい住所が表示されているか確認
        $response = $this->actingAs($this->user)
            ->get(route('purchase.show', $this->exhibition->id));

        $response->assertStatus(200);
        $response->assertSee($newAddressData['post_code']);
        $response->assertSee($newAddressData['address']);
        $response->assertSee($newAddressData['building']);
    }

    /**
     * テストケース12: 配送先変更機能
     * 購入した商品に送付先住所が紐づいて登録される
     */
    public function test_shipping_address_is_saved_with_purchase()
    {
        // 1. 住所変更画面にアクセス
        $response = $this->actingAs($this->user)
            ->get(route('purchase.address.edit', $this->exhibition->id));

        // 2. 配送先住所を登録する
        $newAddressData = [
                'post_code' => '333-4444',
                'address' => '東京都渋谷区渋谷1-1-1',
                'building' => '渋谷ビル5階',
            ];

        $response = $this->actingAs($this->user)
            ->post(route('purchase.address.update', $this->exhibition->id), $newAddressData);

        // 3. 商品を購入する
        $purchaseData = [
            'payment' => '1', // コンビニ支払い
        ];

        $response = $this->actingAs($this->user)
            ->post(route('purchase.store', $this->exhibition->id), $purchaseData);

        // リダイレクト確認
        $response->assertRedirect(route('itemlist'));

        // データベースに購入情報が保存されているか確認
        $this->assertDatabaseHas('purchases', [
            'user_id' => $this->user->id,
            'exhibition_id' => $this->exhibition->id,
            'payment' => '1',
            'post_code' => '333-4444',
            'address' => '東京都渋谷区渋谷1-1-1',
            'building' => '渋谷ビル5階',
        ]);

        // セッションから配送先情報が削除されているか確認
        $this->assertNull(session('shipping_address'));
    }

    /**
     * バリデーションテスト: 郵便番号が必須
     */
    public function test_post_code_is_required()
    {
        $response = $this->actingAs($this->user)
            ->post(route('purchase.address.update', $this->exhibition->id), [
                'post_code' => '',
                'address' => '東京都渋谷区渋谷1-1-1',
                'building' => '',
            ]);

        $response->assertSessionHasErrors('post_code');
    }

    /**
     * バリデーションテスト: 住所が必須
     */
    public function test_address_is_required()
    {
        $response = $this->actingAs($this->user)
            ->post(route('purchase.address.update', $this->exhibition->id), [
                'post_code' => '123-4567',
                'address' => '',
                'building' => '',
            ]);

        $response->assertSessionHasErrors('address');
    }

    /**
     * バリデーションテスト: 支払い方法が必須
     */
    public function test_payment_method_is_required()
    {
        $response = $this->actingAs($this->user)
            ->post(route('purchase.store', $this->exhibition->id), [
                'payment' => '',
            ]);

        $response->assertSessionHasErrors('payment');
    }

    /**
     * 認証テスト: 未ログインユーザーは購入画面にアクセスできない
     */
    public function test_guest_cannot_access_purchase_page()
    {
        $response = $this->get(route('purchase.show', $this->exhibition->id));

        $response->assertRedirect(route('login'));
    }

    /**
     * 認証テスト: 未ログインユーザーは住所変更画面にアクセスできない
     */
    public function test_guest_cannot_access_address_edit_page()
    {
        $response = $this->get(route('purchase.address.edit', $this->exhibition->id));

        $response->assertRedirect(route('login'));
    }
}
