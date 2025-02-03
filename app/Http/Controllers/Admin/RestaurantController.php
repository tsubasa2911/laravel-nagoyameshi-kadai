<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Restaurant;


class RestaurantController extends Controller
{
    public function index(Request $reqest) 
    {
        $keyword = $reqest->input('keyword');

        if ($keyword !== null) {
            $restaurants = Restaurant::where('name', 'like', "%{$keyword}")->pagenate(15);
        } else {
            //　検索ワードが空の場合、全レコードを取得
            $restaurants = Restaurant::paginate(15);
        }

        $total = $restaurants->total();

        return view('admin.restaurants.index', compact('restaurants', 'keyword', 'total'));
    }

    public function show(Restaurant $restaurant)
    {
        return view('admin.restaurants.show', compact('restaurant'));
    }

    public function create() 
    {
        return view('admin.restaurants.create');
    }

    public function store(Request $reqest) 
    {
        //バリデーション設定
        $reqest->validate([
            'name' => 'required|max:255',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'description' => 'required',
            'lowest_price' => 'required|numeric|min:0|lte:highest_price',
            'highest_price' => 'required|numeric|min:0|gte:lowest_price',
            'postal_code' => 'required|digits:7',
            'address' => 'required|',
            'opening_time' => 'required|before:closing_time',
            'closing_time' => 'required|after:opening_time',
            'seating_capacity' => 'required|numeric|min:0',
        ]);

        //入力内容をもとにテーブルにデータを追加
        $restaurant = new Restaurant();
        $restaurant->name = $reqest->input('name');
        //$restaurant ->image = $reqest->input('image');
        $restaurant->description = $reqest->input('description');
        $restaurant->lowest_price = $reqest->input('lowest_price');
        $restaurant->highest_price = $reqest->input('highest_price');
        $restaurant->postal_code = $reqest->input('postal_code');
        $restaurant->address = $reqest->input('address');
        $restaurant->opening_time = $reqest->input('opening_time');
        $restaurant->closing_time = $reqest->input('closing_time');
        $restaurant->seating_capacity = $reqest->input('seating_capacity');
    
        if ($reqest->hasFile('image')) {

            // アップロードされたファイル（name="image"）をstorage/app/public/restaurantsフォルダに保存
            $image_path = $reqest->file('image')->store('public/restaurants');

            // ファイル名を取得
            $imageName = basename('$image_path');
            $restaurant->image = $imageName;

        } else {
            //アップロードされていない場合
            $restaurant -> image = '';
        }

        $restaurant->save();

        // 店舗一覧ページへリダイレクトし、フラッシュメッセージを設定
        return redirect()->route('admin.restaurants.index')->with('flash_message', '店舗を登録しました。');
    }
    
    public function edit(Restaurant $restaurant) {
        return view('admin.restaurants.edit', compact('restaurant'));
    }

    public function update(Request $reqest, Restaurant $restaurant) {

        //バリデーション設定
        $reqest->validate([
            'name' => 'required|max:255',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'description' => 'required',
            'lowest_price' => 'required|numeric|min:0|lte:highest_price',
            'highest_price' => 'required|numeric|min:0|gte:lowest_price',
            'postal_code' => 'required|digits:7',
            'address' => 'required|',
            'opening_time' => 'required|before:closing_time',
            'closing_time' => 'required|after:opening_time',
            'seating_capacity' => 'required|numeric|min:0',
        ]);

            //入力内容をもとにテーブルにデータを追加
            $restaurant = new Restaurant();
            $restaurant->name = $reqest->input('name');
            //$restaurant ->image = $reqest->input('image');
            $restaurant->description = $reqest->input('description');
            $restaurant->lowest_price = $reqest->input('lowest_price');
            $restaurant->highest_price = $reqest->input('highest_price');
            $restaurant->postal_code = $reqest->input('postal_code');
            $restaurant->address = $reqest->input('address');
            $restaurant->opening_time = $reqest->input('opening_time');
            $restaurant->closing_time = $reqest->input('closing_time');
            $restaurant->seating_capacity = $reqest->input('seating_capacity');

            if ($reqest->hasFile('image')) {

                // アップロードされたファイル（name="image"）をstorage/app/public/restaurantsフォルダに保存
                $image_path = $reqest->file('image')->store('public/restaurants');

                // ファイル名を取得
                $imageName = basename('$image_path');
                $restaurant->image = $imageName;
            }

        // 店舗詳細ページへリダイレクトし、フラッシュメッセージを設定
        return redirect()->route('admin.restaurants.show', $restaurant)->with('flash_message', '店舗を編集しました。');
    }

    public function destroy(Restaurant $restaurant) 
    {
        // レストランを削除する処理
        $restaurant->delete(); 
        return redirect()->route('admin.restaurants.index')->with('flash_message', '店舗を削除しました。');
    }
    
}

