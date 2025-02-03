<?php

namespace Tests\Feature\Admin;

use App\Models\Restaurant;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\Paginator; 
use Tests\TestCase;

class RestaurantTest extends TestCase
{
    use RefreshDatabase;

    //店舗一覧ページ
    public function test_guest_cannot_access_admin_restaurant_index_page()
    {
        $response = $this->get(route('admin.restaurants.index'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_user_cannot_access_admin_restaurant_index_page()
    {
        $user = User::factory()->create(); //一般ユーザー作成
        $response = $this->actingAs($user)->get(route('admin.restaurants.index'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_user_can_access_admin_restaurant_index_page()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.index')); // 管理者ユーザーで店舗一覧ページにアクセス
        $response->assertStatus(200); // 200 OK
    }

    //店舗詳細ページ
    public function test_guest_cannot_access_admin_restaurant_show_page()
    {
        $restaurant = Restaurant::factory()->create(); // 店舗を作成
        $response = $this->get(route('admin.restaurants.show', $restaurant));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_user_cannot_access_admin_restaurant_show_page()
    {
        $user = User::factory()->create(); // ユーザーを作成
        $restaurant = Restaurant::factory()->create(); // 店舗を作成
        $response = $this->actingAs($user)->get( route('admin.restaurants.show', $restaurant)); // ログイン済みユーザーで店舗詳細ページにアクセス
        $response->assertRedirect(route('admin.login')); // ログインページにリダイレクト
    }

    public function test_admin_user_can_access_admin_restaurant_show_page()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.show', $restaurant)); // 管理者ユーザーで店舗詳細ページにアクセス
        $response->assertStatus(200); // 200 OK
    }

    // 店舗登録ページ
    public function test_guest_cannot_access_admin_restaurant_create_page()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->get(route('admin.restaurants.create', $restaurant));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_user_cannot_access_admin_restaurant_create_page()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->get( route('admin.restaurants.create', $restaurant)); // ログイン済みユーザーで店舗登録ページにアクセス
        $response->assertRedirect(route('admin.login')); // ログインページにリダイレクト
    }

    public function test_admin_user_can_access_admin_restaurant_create_page()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.show', $restaurant)); // 管理者ユーザーで店舗登録ページにアクセス
        $response->assertStatus(200); // 200 OK
    }

    //店舗登録機能
    public function test_guest_cannot_restaurant_create()
    {
        $response = $this->post(route('admin.restaurants.store')); //post メソッドで店舗登録のリクエストを送信
        $response->assertRedirect(route('admin.login')); // ログインページにリダイレクト
    }

    public function test_user_cannot_restaurant_create()
    {
        $user = User::factory()->create();

        $restaurantData = [
            'name' => '新店舗',
            'description' => 'テスト',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'テスト',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => 50
        ];

        $response = $this->actingAs($user)->post(route('admin.restaurants.store'), $restaurantData);
        $this->assertDatabaseMissing('restaurants', $restaurantData);
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_user_can_restaurant_create()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        
        $restaurantData = [
            'name' => '新店舗',
            'description' => 'テスト',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'テスト',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => '50',
            
        ];
        $response = $this->actingAs($admin, 'admin')->post(route('admin.restaurants.store'), $restaurantData);
        // リダイレクトされたことを確認
        $response->assertStatus(302);
        // 送信したデータがproductsテーブルに保存されていることを検証する
        $this->assertDatabaseHas('restaurants', $restaurantData);
    }

    //店舗編集ページ
    public function test_guest_cannot_access_admin_restaurant_edit_page()
    {
        $restaurant = Restaurant::factory()->create(); 
        $response = $this->get(route('admin.restaurants.edit', $restaurant));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_user_cannot_access_admin_restaurant_edit_page()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->get( route('admin.restaurants.edit', $restaurant)); // ログイン済みユーザーで店舗編集ページにアクセス
        $response->assertStatus(302)->assertRedirect(route('admin.login'));
    }

    public function test_admin_user_can_access_admin_restaurant_edit_page()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get( route('admin.restaurants.edit', $restaurant));
        $response->assertStatus(200);
    }

    //店舗更新機能
    public function test_guest_cannot_restaurant_update()
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->put(route('admin.restaurants.update', $restaurant)); //put メソッドで店舗編集のリクエストを送信
        $response->assertRedirect(route('admin.login')); 
    }

    public function test_user_cannot_restaurant_updata()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();

        $restaurantData = [
            'name' => '更新店舗',
            'description' => 'テスト',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'テスト',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => '50',
            
        ];

        $response = $this->actingAs($user)->put(route('admin.restaurants.update',  ['restaurant' => $restaurant->id] ,$restaurant));

        $this->assertDatabaseMissing('restaurants', $restaurantData);
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_user_can_restaurant_updata()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $restaurant = Restaurant::factory()->create();

        $restaurantData = [
            'name' => '更新店舗',
            'description' => 'テスト',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'テスト',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => '50',
            
        ];

        $response = $this->actingAs($admin, 'admin')->put(route('admin.restaurants.update', ['restaurant' => $restaurant->id], $restaurant));

        // リダイレクトされたことを確認
        $response->assertStatus(302);

        // データベースが更新されていることを確認
        $this->assertDatabaseHas('restaurants', array_merge(['id' => $restaurant->id], $restaurantData));
    }

    //店舗削除機能
    public function test_guest_cannot_restaurant_delete()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->delete(route('restaurants.destroy',  $restaurant));
        $response->assertRedirect(route('login')); 
    }

    public function test_user_cannot_estaurant_delete()
{
    $user = User::factory()->create();
    $restaurant = Restaurant::factory()->create();

    $response = $this->actingAs($user)->delete(route('restaurants.destroy', $restaurant));

    $response->assertForbidden();
}

    public function test_admin_user_can_restaurant_delete()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($admin, 'admin')->delete(route('restaurants.destroy',  $restaurant));

        // リダイレクトされたことを確認
        $response->assertStatus(302);

        // データベースが更新されていることを確認
        $this->assertDatabaseMissing('restaurants', $restaurant);
    }

}
