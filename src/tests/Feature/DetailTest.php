<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Profile;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Exhibition;
use App\Models\Purchase;
use App\Models\Favorite;
use App\Models\Comment;
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\CategoriesTableSeeder;
use Database\Seeders\ConditionsTableSeeder;
use Database\Seeders\ExhibitionsTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DetailTest extends TestCase
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
        DB::statement('ALTER TABLE categories AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE conditions AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE exhibitions AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE favorites AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE comments AUTO_INCREMENT = 1');

        // テスト用ストレージの設定
        Storage::fake('public');

        // シーダー実行
        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ConditionsTableSeeder::class);
    }

    /**
     * レスポンスからいいね数を取得するヘルパーメソッド
     * @param \Illuminate\Testing\TestResponse $response
     * @return int
     */
    private function getFavoriteCountFromResponse($response)
    {
        $content = $response->getContent();

        // より柔軟な正規表現パターンを使用
        $patterns = [
            // パターン1: class="exhibition-actions__count-favorite"
            '/<[^>]*class="[^"]*exhibition-actions__count-favorite[^"]*"[^>]*>(\d+)<\/[^>]+>/',
            // パターン2: class='exhibition-actions__count-favorite' (シングルクォート)
            "/<[^>]*class='[^']*exhibition-actions__count-favorite[^']*'[^>]*>(\d+)<\/[^>]+>/",
            // パターン3: より緩い検索
            '/exhibition-actions__count-favorite[^>]*>(\d+)/',
            // パターン4: data属性やその他の属性も考慮
            '/<[^>]*exhibition-actions__count-favorite[^>]*>(\d+)</',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content, $matches)) {
                return (int) $matches[1];
            }
        }

        // DOMCrawlerを使用した方法（Symfony DomCrawlerが必要）
        try {
            $dom = new \DOMDocument();
            @$dom->loadHTML($content);
            $xpath = new \DOMXPath($dom);

            // より詳細なXPathクエリ
            $queries = [
                '//*[contains(@class, "exhibition-actions__count-favorite")]',
                '//span[contains(@class, "exhibition-actions__count-favorite")]',
                '//*[@class="exhibition-actions__count-favorite"]',
            ];

            foreach ($queries as $query) {
                $nodes = $xpath->query($query);
                if ($nodes->length > 0) {
                    $text = trim($nodes->item(0)->textContent);
                    if (is_numeric($text)) {
                        return (int) $text;
                    }
                }
            }
        } catch (\Exception $e) {
            // DOMエラーの場合は無視して続行
        }

        return 0;
    }

    /**
     * レスポンスからいいね数を取得するヘルパーメソッド
     * @param \Illuminate\Testing\TestResponse $response
     * @return int
     */
    private function getCommentCountFromResponse($response)
    {
        $content = $response->getContent();

        // より柔軟な正規表現パターンを使用
        $patterns = [
            // パターン1: class="exhibition-actions__count-comment"
            '/<[^>]*class="[^"]*exhibition-actions__count-comment[^"]*"[^>]*>(\d+)<\/[^>]+>/',
            // パターン2: class='exhibition-actions__count-comment' (シングルクォート)
            "/<[^>]*class='[^']*exhibition-actions__count-comment[^']*'[^>]*>(\d+)<\/[^>]+>/",
            // パターン3: より緩い検索
            '/exhibition-actions__count-comment[^>]*>(\d+)/',
            // パターン4: data属性やその他の属性も考慮
            '/<[^>]*exhibition-actions__count-comment[^>]*>(\d+)</',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content, $matches)) {
                return (int) $matches[1];
            }
        }

        // DOMCrawlerを使用した方法（Symfony DomCrawlerが必要）
        try {
            $dom = new \DOMDocument();
            @$dom->loadHTML($content);
            $xpath = new \DOMXPath($dom);

            // より詳細なXPathクエリ
            $queries = [
                '//*[contains(@class, "exhibition-actions__count-comment")]',
                '//span[contains(@class, "exhibition-actions__count-comment")]',
                '//*[@class="exhibition-actions__count-comment"]',
            ];

            foreach ($queries as $query) {
                $nodes = $xpath->query($query);
                if ($nodes->length > 0) {
                    $text = trim($nodes->item(0)->textContent);
                    if (is_numeric($text)) {
                        return (int) $text;
                    }
                }
            }
        } catch (\Exception $e) {
            // DOMエラーの場合は無視して続行
        }

        return 0;
    }

    /**
     * テストケース7: 商品詳細情報取得
     * 必要な情報を取得できる
     */
    public function test_can_get_all_details()
    {
        // ユーザーを作成
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // 商品データ作成
        $exhibition = Exhibition::factory()->forUser($otherUser)->create();

        // いいねを追加
        $favorite = Favorite::factory()->create([
            'user_id' => $user->id,
            'exhibition_id' => $exhibition->id
        ]);

        // コメントを追加
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'exhibition_id' => $exhibition->id
        ]);

        //商品詳細ページを開く
        $response = $this->get("/item/:1");

        $response->assertStatus(200);
        $response->assertViewIs('detail');

        // 商品画像のパスが正しく設定されている
        $response->assertSee('storage/' . $exhibition->img_url, false);

        // 商品詳細が表示されることを確認
        $response->assertSee($exhibition->name);
        $response->assertSee($exhibition->brand);
        $response->assertSee(number_format($exhibition->price));
        $response->assertSee($exhibition->description);
        $response->assertSee($exhibition->condition_id);

        // 選択されたカテゴリが全て表示されることを確認
        foreach ($exhibition->categories as $index => $category) {
            $response->assertSee($category->category);
        }

        //いいね数が表示されることを確認
        $response->assertSee($favorite->count());

        //コメント情報が表示されることを確認（コメント数・ユーザー名・コメント内容）
        $response->assertSee($comment->count());
        $response->assertSee($comment->user->name);
        $response->assertSee($comment->comment);
    }

    /**
     * テストケース7: 商品詳細情報取得
     * 複数選択されたカテゴリが表示される
     */
    public function test_multiple_selected_categories_are_desplayed()
    {
        // ユーザーを作成
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // 商品データ作成
        $exhibition = Exhibition::factory()->forUser($otherUser)->create();

        // シーダーで作成されたカテゴリーからランダムに選択（2-4個）
        $selectedCategories = Category::inRandomOrder()->take(rand(2, 4))->get();

        // カテゴリーをアタッチ
        $exhibition->categories()->attach($selectedCategories->pluck('id'));

        //商品詳細ページを開く
        $response = $this->get("/item/:1");

        $response->assertStatus(200);

        // アタッチしたカテゴリーが全て表示されることを確認
        foreach ($selectedCategories as $category) {
            $response->assertSee($category->category);
        }
    }

    /**
     * テストケース8: いいね機能
     * いいねアイコンを押下することによって、いいねした商品として登録することができる
     */
    public function test_can_give_a_favorite_by_pressing_the_favorite_icon()
    {
        // ユーザーを作成
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // 商品データ作成
        $exhibition = Exhibition::factory()->forUser($otherUser)->create();

        // ユーザーでログイン
        $this->actingAs($user);

        // 商品詳細ページを開く
        $responseBefore = $this->get('item/:1');
        $responseBefore->assertStatus(200);

        // いいね前の表示数を取得（初期値は0と仮定）
        $beforeCount = $this->getFavoriteCountFromResponse($responseBefore);
        $this->assertDatabaseCount('favorites', 0);

        //いいねアイコンを押す
        $response = $this->post('/item/:1/favorite');
        $response->assertStatus(200);

        // いいね後の商品詳細ページを再取得
        $responseAfter = $this->get("/item/:1");
        $responseAfter->assertStatus(200);

        // いいね後の表示数を取得
        $afterCount = $this->getFavoriteCountFromResponse($responseAfter);


        // いいねの数が1増加していることを確認
        $this->assertEquals($beforeCount + 1, $afterCount, 'いいね数が正しく増加していません');

        //増加したいいねのユーザー・商品が一致しているかを確認
        $this->assertDatabasehas('favorites', [
            'user_id' => $user->id,
            'exhibition_id' => $exhibition->id,
        ]);
    }

    /**
     * テストケース8: いいね機能
     * 追加済みのアイコンは色が変化する
     */
    public function test_favorite_icon_changes_color()
    {
        // ユーザーを作成
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // 商品データ作成
        $exhibition = Exhibition::factory()->forUser($otherUser)->create();

        // ユーザーでログイン
        $this->actingAs($user);

        // 商品詳細ページを開く（いいねする前）
        $response = $this->get('item/:1');
        $response->assertStatus(200);
        $response->assertViewIs('detail');

        // いいねしていない状態のクラスを確認
        $response->assertSee('favorite-star'); // 通常のクラス
        $response->assertDontSee('favorite-star favorited'); // いいね済みクラスがないことを確認

        //いいねアイコンを押す
        $response = $this->post('/item/:1/favorite');
        $response->assertStatus(200);

        // いいね後に商品詳細ページを確認
        $afterFavoriteResponse = $this->actingAs($user)->get("/item/:1");

        // いいね済みクラスが追加されていることを確認
        $afterFavoriteResponse->assertSee('favorite-star favorited');
    }

    public function test_favorite_icon_changes_back_when_unfavorited()
    {
        // ユーザーと商品を作成
        $user = User::factory()->create();
        $exhibition = Exhibition::factory()->create();

        // 先にいいねをつける
        $this->actingAs($user)->post('/item/:1/favorite');

        // いいね済み状態を確認
        $response = $this->actingAs($user)->get("/item/:1");
        $response->assertSee('favorite-star favorited');

        // いいねを解除
        $unfavoriteResponse = $this->actingAs($user)->post('/item/:1/favorite');

        // いいね解除後の状態を確認
        $afterUnfavoriteResponse = $this->actingAs($user)->get("/item/:1");

        // いいね済みクラスが削除されていることを確認
        $afterUnfavoriteResponse->assertDontSee('favorite-star favorited');
        $afterUnfavoriteResponse->assertSee('favorite-star'); // 通常のクラスは残っている
    }

    /**
     * テストケース9: コメント送信機能
     * ログイン済みのユーザーはコメントを送信できる
     */
    public function test_authenticated_user_can_send_comments()
    {
        // ユーザーを作成
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        //コメントするユーザーのプロフィールを作成
        $profile = Profile::factory()->create([
            'user_id' => $user->id,
            'img_url' => 'profile_images/sample1.jpg',
            'post_code' => '123-4567',
            'address' => '東京都目黒区下目黒2-20-28',
            'building' => 'いちご目黒ビル4階'
        ]);

        // 商品データ作成
        $exhibition = Exhibition::factory()->forUser($otherUser)->create();

        // ユーザーでログイン
        $this->actingAs($user);

        // 商品詳細ページを開く
        $responseBefore = $this->get('item/:1');
        $responseBefore->assertStatus(200);

        // コメントする前の表示数を取得（初期値は0と仮定）
        $beforeCount = $this->getCommentCountFromResponse($responseBefore);
        $this->assertDatabaseCount('comments', 0);

        //コメントを入力して送信ボタンを押す
        $comment = Comment::factory()->make([
            'exhibition_id' => $exhibition->id,
            'user_id' => $user->id,
        ]);
        $response = $this->post("/item/:1", [
            'comment' => $comment->comment,
        ]);

        // 商品詳細画面にリダイレクトされることを確認
        $response->assertStatus(302);
        $response->assertRedirect('item/:1');

        // コメント後の商品詳細ページを再取得
        $responseAfter = $this->get("/item/:1");
        $responseAfter->assertStatus(200);

        // コメント後の表示数を取得
        $afterCount = $this->getCommentCountFromResponse($responseAfter);


        // コメント数が1増加していることを確認
        $this->assertEquals($beforeCount + 1, $afterCount, 'コメント数が正しく増加していません');

        //コメントしたユーザー名・ユーザー画像・コメント内容が表示されているかを確認
        $responseAfter->assertSee($comment->user->name);
        $responseAfter->assertSee($comment->comment);

        // コメントしたユーザー画像のパスが正しく設定されている
        $responseAfter->assertSee('storage/' . $profile->img_url, false);

        $this->assertDatabasehas('comments', [
            'exhibition_id' => $exhibition->id,
            'user_id' => $user->id,
            'comment' => $comment->comment,
        ]);
    }

    /**
     * テストケース9: コメント送信機能
     * ログイン前のユーザーはコメントを送信できない
     */
    public function test_unauthenticated_user_cannot_send_comments()
    {
        // ユーザーを作成
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // 商品データ作成
        $exhibition = Exhibition::factory()->forUser($otherUser)->create();

        // 商品詳細ページを開く
        $responseBefore = $this->get('item/:1');
        $responseBefore->assertStatus(200);

        //コメントを入力して送信ボタンを押す
        $comment = Comment::factory()->make([
            'exhibition_id' => $exhibition->id,
        ]);
        $response = $this->post("/item/:1", [
            'comment' => $comment->comment,
        ]);

        // ログインページにリダイレクトされることを確認
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        // 再度商品詳細ページを開く
        $responseAfter = $this->get("/item/:1");
        $responseAfter->assertStatus(200);

        //コメントが表示されていないことを確認
        $responseAfter->assertDontSee($comment->comment);
    }

    /**
     * テストケース9: コメント送信機能
     * コメントが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_comment_is_required_validation()
    {
        // ユーザーを作成
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        //コメントするユーザーのプロフィールを作成
        $profile = Profile::factory()->create([
            'user_id' => $user->id,
            'img_url' => 'profile_images/sample1.jpg',
            'post_code' => '123-4567',
            'address' => '東京都目黒区下目黒2-20-28',
            'building' => 'いちご目黒ビル4階'
        ]);

        // 商品データ作成
        $exhibition = Exhibition::factory()->forUser($otherUser)->create();

        // ユーザーでログイン
        $this->actingAs($user);

        // 商品詳細ページを開く
        $responseBefore = $this->get('item/:1');
        $responseBefore->assertStatus(200);

        //コメントを入力して送信ボタンを押す
        $comment = Comment::factory()->make([
            'exhibition_id' => $exhibition->id,
            'user_id' => $user->id,
            'comment' => '', //何も入力しない
        ]);
        $response = $this->post("/item/:1", [
            'comment' => $comment->comment,
        ]);

        // バリデーションエラーでリダイレクトされることを確認
        $response->assertStatus(302);

        // セッションにバリデーションエラーが含まれることを確認
        $response->assertSessionHasErrors(['comment']);

        // 期待されるエラーメッセージを確認
        $response->assertSessionHasErrors([
            'comment' => 'コメントを入力してください'
        ]);
    }

    /**
     * テストケース9: コメント送信機能
     * コメントが255文字以上の場合、バリデーションメッセージが表示される
     */
    public function test_comment_maximum_length_validation()
    {
        // ユーザーを作成
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        //コメントするユーザーのプロフィールを作成
        $profile = Profile::factory()->create([
            'user_id' => $user->id,
            'img_url' => 'profile_images/sample1.jpg',
            'post_code' => '123-4567',
            'address' => '東京都目黒区下目黒2-20-28',
            'building' => 'いちご目黒ビル4階'
        ]);

        // 商品データ作成
        $exhibition = Exhibition::factory()->forUser($otherUser)->create();

        // ユーザーでログイン
        $this->actingAs($user);

        // 商品詳細ページを開く
        $responseBefore = $this->get('item/:1');
        $responseBefore->assertStatus(200);

        //コメントを入力して送信ボタンを押す
        $comment = Comment::factory()->make([
            'exhibition_id' => $exhibition->id,
            'user_id' => $user->id,
            'comment' => 'テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用テスト用', //256文字のコメント
        ]);
        $response = $this->post("/item/:1", [
            'comment' => $comment->comment,
        ]);

        // バリデーションエラーでリダイレクトされることを確認
        $response->assertStatus(302);

        // セッションにバリデーションエラーが含まれることを確認
        $response->assertSessionHasErrors(['comment']);

        // 期待されるエラーメッセージを確認
        $response->assertSessionHasErrors([
            'comment' => 'コメントは255文字以内で入力してください'
        ]);
    }
}
