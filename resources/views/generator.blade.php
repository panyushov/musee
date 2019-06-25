@extends('main')


@section('content')
    <section class="musee-block">
        <div class="container">
            <div class="heading"><h2>Sitemap Generator</h2></div>
            <form method="POST" action="{{route('musee.generator.post')}}">
                @include('errors')

                @csrf
                <div class="form-group"><label for="host">Recipients (separated by comma)</label><input
                            class="form-control" type="text" name="emails"
                            id="emails"></div>

                <div class="form-group">
                    @if($flgGenerating)
                        <button class="btn btn-primary btn-block" type="submit" disabled>Generation in progress (try to
                            refresh page later)
                        </button>
                    @else
                        <button class="btn btn-primary btn-block" type="submit">Generate</button>
                    @endif
                </div>
            </form>
        </div>
    </section>
@endsection