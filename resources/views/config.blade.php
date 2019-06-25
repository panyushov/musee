@extends('main')


@section('content')
    <section class="musee-block">
        <div class="container">
            <div class="heading"><h2>SMTP</h2></div>
            <form method="POST" action="{{route('musee.config.post')}}">
                @include('errors')

                @csrf
                <div class="form-group"><label for="host">Host</label><input required  class="form-control" type="text"
                                                                             name="host"
                                                                             @if($config) value='{{$config->host}}'
                                                                             @endif
                                                                             id="host"></div>

                <div class="form-group"><label for="port">Port</label><input required  class="form-control" type="text"
                                                                             name="port"
                                                                             @if($config) value='{{$config->port}}'
                                                                             @endif
                                                                             id="port"></div>

                <div class="form-group"><label for="username">Username</label><input required  class="form-control" type="text"
                                                                                     name="username"
                                                                                     @if($config) value='{{$config->username}}'
                                                                                     @endif
                                                                                     id="username"></div>

                <div class="form-group"><label for="password">Password</label><input required  class="form-control"
                                                                                     type="password"
                                                                                     name="password"
                                                                                     id="password"></div>

                <div class="form-group"><label for="encryption">Encryption</label><input required  disabled
                                                                                         class="form-control"
                                                                                         type="text"
                                                                                         value='{{App\Models\SMTPConfig::DEFAULT_ENCRYPTION}}'
                                                                                         name="encryption"
                                                                                         id="encryption"></div>

                <div class="form-group"><label for="from">From</label><input required  class="form-control" type="text"
                                                                             name="from"
                                                                             @if($config) value='{{$config->from}}'
                                                                             @endif
                                                                             id="from"></div>

                <div class="form-group">

                    @if($flgGenerating)
                        <button class="btn btn-primary btn-block" type="submit" disabled>Save (it's not possible to
                            change SMTP config during generation)
                        </button>
                    @else
                        <button class="btn btn-primary btn-block" type="submit">Save</button>
                    @endif
                </div>

            </form>
        </div>
    </section>
@endsection