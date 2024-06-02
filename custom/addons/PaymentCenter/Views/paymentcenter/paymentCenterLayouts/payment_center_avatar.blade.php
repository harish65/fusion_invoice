<div title="{{ $user->name }}" style="background-color: {{($skin == 'dark-mode') ? '#000000' : '#e9e9e9' }};
        width: {{ $size }}px;
        height: {{ $size }}px;
        font-size: {{ $size / 2 }}px;
        font-family: 'Karla', sans-serif;
        cursor: pointer;
        color: {{($skin == 'dark-mode') ? '#e9e9e9' : '#000000' }};
        text-align: center;
        line-height: {{ $size }}px;
@if($isRounded)
        border-radius: 50%;
@endif">
    {!! $user->deleted_at != '' ? '<strike>'.$initials.'</strike>' : $initials !!}
</div>