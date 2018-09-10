<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Image;
use DB;
use File;

class ImageController extends Controller
{
    //画像を受け取る
    public function upload(Request $request) {
        //ファイルのバリデーション
        $this->validate($request, [
            'image' => [
                'required',
                'file',
                'image',
                'mimes:jpeg, png',
                // 'dimensions:min_width=120,min_height=120,max_width=400,max_height=400',
            ]
        ]);

        $file = $request->file('image');

        //バリデーション突破
        if ($file->isValid([])) {
            //新たにランダムなファイル名を命名する
            $new_file_name = uniqid() . "." . $file->guessExtension();
            //仮のファイル置き場に移動させる
            $file->move(public_path() . "/var/tmp/fair", $new_file_name);
            //初回のみ仮のファイル置き場作成
            if (!file_exists(public_path() . "/img")) {
                mkdir(public_path() . "/img");
            }
            if (!file_exists(public_path() . "/img/fair")) {
                mkdir(public_path() . "/img/fair");
            }
            if (!file_exists(public_path() . "/img/fair/" . $request->member_id)) {
                mkdir(public_path() . "/img/fair/" . $request->member_id);
            }

            //画像のパスをDBに登録する
            $image_all = Image::where('member_id', $request->member_id)->get();
            $image = new Image;
            $image->member_id = $request->member_id;
            $image->file_id = count($image_all) + 1;
            $image->file_name = $new_file_name;
            $image->save();

            //本番用ファイル置き場に移動する
            File::move(public_path() . "/var/tmp/fair/" . $new_file_name, public_path() . "/img/fair/" . $request->member_id . "/" . $new_file_name);

            return response()->json(['file_id' => $image->file_id, 'file_name' => $image->file_name], 200);
        }
    }

    //個別の画像リクエストに応じて画像を取得して返す
    public function getImage($id, $file_id) {
        $image = Image::where('member_id', $id)->where('file_id', $file_id)->first();
        //画像がない場合
        if (count($image) == 0) {
            return response()->json(['message' => 'no images']);
        }
        return response()->file(public_path() . '/img/fair/' . $id . '/' . $image->file_name);
    }


    //画像一覧の情報を返却する
    public function getAllImage($id) {
        $image_all = Image::where('member_id', $id)->get();
        return response()->json($image_all, 200);
    }
}