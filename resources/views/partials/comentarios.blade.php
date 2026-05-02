@php
    $previousUserId = null;
@endphp

@php
    $usuarioProjetoId = DB::table('tb_projeto')->where('id', $id_projeto)->value('id_usuario');
    $idCargoContribuidor = DB::table('tb_contribuidor')
        ->where('id_usuario', Auth::user()->id)
        ->where('id_projeto', $id_projeto)
        ->value('id_cargo');
@endphp

@foreach($comentarios as $comment)

    @if($comment->id_usuario != $previousUserId)
        <div class="commentName" @if($comment->id_usuario == Auth::user()->id) style="justify-content: flex-end;" @endif>
            <p @if($comment->id_usuario == Auth::user()->id) style="margin: 1vh 3% 0 17%;" @endif
            >{{ DB::table('tb_usuario')->where('id', $comment->id_usuario)->value('nm_usuario') }} @if($comment->id_usuario == Auth::user()->id)(você)@endif</p>
        </div>
    @endif                         

    <div class="comment_cont" user="{{ $comment->id_usuario }}" @if($comment->id_usuario == Auth::user()->id) style="justify-content: flex-end;" @endif>

        @php
            $extensions = ['.webp', '.png', '.jpg', '.jpeg', '.jfif'];
            $userImagePath = null;

            foreach ($extensions as $ext) {
                $possiblePath = "user_" . DB::table('tb_usuario')->where('id', $comment->id_usuario)->value('id') . "/profile{$ext}";
                if (Storage::disk('public')->exists($possiblePath)) {
                    $userImagePath = $possiblePath;
                    break;
                }
            }
        @endphp

        @if($comment->id_usuario != Auth::user()->id)   
            <div class="commentImg" @if($comment->id_usuario == Auth::user()->id) style="display: none;" @endif>
                @if($comment->id_usuario != $previousUserId)
                    <img src="{{ $userImagePath ? url('storage/' . $userImagePath) : asset('image/ProfileImg.svg') }}" loading="lazy">
                @endif                                       
            </div>
        @endif

        <div class="comment">
            <p>{{ $comment->ds_comentario }}</p>
        </div>

        @if($comment->id_usuario != Auth::user()->id)
            <div class="commentImg" @if($comment->id_usuario != Auth::user()->id) style="display: none;" @endif>
                @if($comment->id_usuario != $previousUserId)
                    <img src="{{ $userImagePath ? url('storage/' . $userImagePath) : asset('image/ProfileImg.svg') }}" loading="lazy">
                @endif
            </div>
        @endif                                
                                    
    </div>

    @php
        $previousUserId = $comment->id_usuario;
    @endphp

@endforeach