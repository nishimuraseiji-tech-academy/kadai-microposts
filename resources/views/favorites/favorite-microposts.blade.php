<ul class="media-list">
    @foreach ($microposts as $micropost)
        @if (Auth::user()->is_favorite($micropost->id) )
        <li class="media mb-3">
            <img class="mr-2 rounded" src="{{ Gravatar::src($micropost->user->email, 50) }}" alt="">
            <div class="media-body">
                <div>
                    {!! link_to_route('users.show', $micropost->user->name, ['id' => $micropost->user->id]) !!} <span class="text-muted">posted at {{ $micropost->created_at }}</span>
                </div>
                
                <div>
                    <p class="mb-0">{!! nl2br(e($micropost->content)) !!}</p>
                </div>
                
                <!--Flexboxで横並び↓-->
                <div id="wrapper" style="display: flex;">
                <div>
                    @if (Auth::id() == $micropost->user_id)
                        {!! Form::open(['route' => ['microposts.destroy', $micropost->id], 'method' => 'delete']) !!}
                            {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-sm']) !!}
                        {!! Form::close() !!}
                    @endif
                </div>
                <div>
                    <!--お気に入りの追加／削除ボタン↓-->
                    @include('favorites.favorite', ['user' => $user])
                </div>
                </div>
            </div>
        </li>
        @endif
    @endforeach
</ul>
{{ $microposts->render('pagination::bootstrap-4') }}