@if (count($errors) > 0 or (isset($warnings) and count($warnings) > 0))
    There were some problems:<br><br>
    <ul>
        @if(isset($warnings) and count($warnings) > 0)
            @foreach ($warnings as $warning)
                <li>{{ $warning }}</li>
            @endforeach
        @endif
        @if(count($errors) > 0)
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        @endif

    </ul>
@endif