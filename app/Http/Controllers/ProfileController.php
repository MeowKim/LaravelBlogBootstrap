<?php

namespace App\Http\Controllers;

use App\Models\User;

class ProfileController extends Controller
{
    // 인증 체크
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    // 보기
    public function index()
    {
        $id = auth()->user()->id;
        $user = User::find($id);
        $articles = $user->articles()->paginate(5);
        $user->setRelation('articles', $articles);

        return view('profile.index', compact('user'));
    }

    // 수정 폼
    public function edit()
    {
        $id = auth()->user()->id;
        $user = User::find($id);

        return view('profile.edit', compact('user'));
    }

    // 업데이트
    public function update()
    {
        $id = auth()->user()->id;
        $user = User::find($id);

        $user->name = request('name');
        $user->email = request('email');
        $user->save();
        return redirect()->route('profile.index');
    }
}
