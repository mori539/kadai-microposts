@if (Auth::id() != $user->id)
    {{-- フォロー済みの場合 --}}
    @if (Auth::user()->is_following($user->id))
        {{-- アンフォローボタンのフォーム --}}
        <form method="POST" action="{{ route('user.unfollow', $user->id) }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-error btn-soft btn-block normal-case"
                onclick="return confirm('id = {{ $user->id }} のフォローを外します。よろしいですか？')">Unfollow</button>
        </form>
    {{-- 未フォローの場合 --}}
    @else
        {{-- フォローボタンのフォーム --}}
        <form method="POST" action="{{ route('user.follow', $user->id) }}">
            @csrf
            <button type="submit" class="btn btn-primary btn-soft btn-block normal-case">Follow</button>
        </form>
    @endif
@endif