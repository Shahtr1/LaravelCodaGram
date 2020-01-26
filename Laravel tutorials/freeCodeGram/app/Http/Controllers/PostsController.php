<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class PostsController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        $users = auth()->user()->following()->pluck('profiles.user_id');

        //$posts = Post::whereIn('user_id',$users)->orderBy('created_at','DESC')->get();
        $posts = Post::whereIn('user_id',$users)->with('user')->latest()->paginate(4);
        //dd($posts);

        return view('posts.index',compact('posts'));
    }

    public function create(){
        return view('posts.create');
    }

    public function store(){
        $data = request()->validate([
         //   'another' => '',  //no validation for this field
            'caption' =>'required',
            'image' =>'required | image',
        ]);

        $imagePath = request('image')->store('uploads','public');

        $image = Image::make(public_path("storage/{$imagePath}"))->fit(1200,1200);
        $image->save();

        /*
            What we did in tinker
                $post = new \App\Post();

                $post->caption = $data['caption'];
                $post->save();

            but the easier way is by using a create method
        */   

        auth()->user()->posts()->create([
            'caption' => $data['caption'],
            'image' => $imagePath,
        ]);

        //dd(request()->all());

        return redirect('/profile/'.auth()->user()->id);
    }

    public function show(\App\Post $post){
        return view('posts.show',compact('post'));
    }
}
