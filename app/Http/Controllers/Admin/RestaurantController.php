<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Restaurant;


class RestaurantController extends Controller
{
    public function index(Request $request) 
    {
        $keyword = $request->input('keyword');

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

    public function create(Request $request) 
    {
        return view('admin.restaurants.create');
    }

    public function store(Request $request) 
    {
        //バリデーション設定
        $request->validate([
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
        $restaurant->name = $request->input('name');
        $restaurant->description = $request->input('description');
        $restaurant->lowest_price = $request->input('lowest_price');
        $restaurant->highest_price = $request->input('highest_price');
        $restaurant->postal_code = $request->input('postal_code');
        $restaurant->address = $request->input('address');
        $restaurant->opening_time = $request->input('opening_time');
        $restaurant->closing_time = $request->input('closing_time');
        $restaurant->seating_capacity = $request->input('seating_capacity');
    
        if ($request->hasFile('image')) {

            // アップロードされたファイル（name="image"）をstorage/app/public/restaurantsフォルダに保存
            $image_path = $request->file('image')->store('public/restaurants');

            // ファイル名を取得
            $imageName = basename($image_path);
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

    public function update(Request $request, Restaurant $restaurant) {

        //バリデーション設定
        $request->validate([
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
            
            $restaurant->name = $request->input('name');
            $restaurant->description = $request->input('description');
            $restaurant->lowest_price = $request->input('lowest_price');
            $restaurant->highest_price = $request->input('highest_price');
            $restaurant->postal_code = $request->input('postal_code');
            $restaurant->address = $request->input('address');
            $restaurant->opening_time = $request->input('opening_time');
            $restaurant->closing_time = $request->input('closing_time');
            $restaurant->seating_capacity = $request->input('seating_capacity');

            if ($request->hasFile('image')) {

                // アップロードされたファイル（name="image"）をstorage/app/public/restaurantsフォルダに保存
                $image_path = $request->file('image')->store('public/restaurants');

                // ファイル名を取得
                $imageName = basename($image_path);
                $restaurant->image = $imageName;
            }
        
            $restaurant->save(); // 保存を確実に行う
        // 店舗詳細ページへリダイレクトし、フラッシュメッセージを設定
        return redirect()->route('admin.restaurants.show', $restaurant->id)->with('flash_message', '店舗を編集しました。');
    }

    public function destroy(Request $request, Restaurant $restaurant) 
    {
        // レストランを削除する処理
        $restaurant->delete(); 
        return redirect()->route('admin.restaurants.index', $restaurant->id)->with('flash_message', '店舗を削除しました。');
    }
    
}

