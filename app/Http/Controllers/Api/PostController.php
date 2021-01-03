<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        return response()->json(Post::all() , 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $messages = [
            'required' => 'The :attribute field is required',
        ];
        $validator =Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required'
        ], $messages);

        if($validator -> fails()) {
            return response()->json(['msg' => $validator->errors()], 200);
        }else{
            $posts = Post::create([
                'title' => $request->title,
                'description' => $request->description
            ]);
            return response()->json(['posts' => $posts , 'msg' => 'Data Created Successfully'], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $posts = Post::find($id);

        return response()->json($posts, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $posts = Post::findOrFail($id);
        $posts->update([
            'title' => $request->title,
            'description' => $request->description
        ]);

        return response()->json(['msg' => 'Update Success'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $posts = Post::find($id);
        $posts->delete();

        return response()->json(['postList' => $posts,'msg' => 'Delete Successful'], 200);
    }
}
