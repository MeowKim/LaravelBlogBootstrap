<?php

namespace App\Http\Controllers;

use App\Models\User;

class ProfileController extends Controller
{
    // 밸리데이션 조건
    private $validation_rules_profile = [
        'name' => 'bail|required|string|max:255',
        'email' => 'bail|required|string|email|max:255|unique:users',
    ];

    private $validation_rules_password = [
        'current_password' => 'password',
        'new_password' => 'bail|required|string|min:8|different:current_password|confirmed',
    ];

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
        // 밸리데이션 체크
        request()->validate($this->validation_rules_profile);

        $id = auth()->user()->id;
        $user = User::find($id);

        $user->name = request('name');
        $user->email = request('email');
        $user->save();

        return redirect()->route('profile.index');
    }

    // 비밀번호 변경 폼
    public function changePassword() {
        return view('profile.password.change');
    }

    // 비밀번호 업데이트
    public function updatePassword() {
        // 밸리데이션 체크
        request()->validate($this->validation_rules_password);

        $id = auth()->user()->id;
        $user = User::find($id);
        $user->password = bcrypt(request('new_password'));
        $user->save();

        return redirect()->route('profile.index')->with('success', __('ui/users.password_changed'));
    }
}
