@extends('beautymail::templates.widgets')

@section('content')

    @php
      $h3_style = 'margin-bottom: 0; font-size: 1.5em;';
      $h4_style = 'margin-top: 0; font-size: 1.2em; font-weight: 400;';
      $login_style = '
                      font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif;
                      background: #E8D03E;
                      text-align: center;
                      padding: 2px 20px 20px;
                      margin: 2px 20px 5px;
                      font-size: 18px;
                      box-shadow: 0 6px 33px -9px rgba(0, 0, 0, 0.28);
      ';
      $code_style = '
                      display: inline-block;
                      padding: .25em .4em .2em .4em;
                      border-radius: .15em;
                      color: black;
                      font-weight: bold;
                      font-family: "Consolas", "monospace", "Courier New", "Lucida Console", "Roboto Mono";
                      line-height: inherit;
                      margin:  3px 3px 15px;
      ';
      $login_button_styles = '
      padding: 15px 25px;
      background-color: #fff;
      color: #000;
      display: inline-block;
      font-weight: 500;
      text-decoration: unset;
      letter-spacing: 1px;
      font-size: 18px;
      ';
      $login_url_style = '
        font-size: 12px;
        margin: 3px;
        display: block;
        opacity: 0.8;
        font-weight: normal;
      ';
      $dark_style = '
        background-color: #000;
        color: #fff;
        font-size: 1.25em;
      ';
      $subinfo_styles = '
      margin: 5px;
    color: #6d6d6d;
    font-size: 13px;
      ';
    @endphp

  @if($text)
  <p style="padding: 0 20px 5px;">
    {{ $text }}
  </p>
  @endif

  <div class="login_wrapper" style="{{ $login_style }}">
    <img style="width: 120px;" width="120px" src="https://dreamteam-survey.s3.eu-central-1.amazonaws.com/images/corporate-happiness-gmbh-logo-full-white.svg" class="logo">

    <h3 style="{{ $h3_style }}">Mitarbeiter-Befragung</h3>
    <h4 style="{{ $h4_style }}">Ihre Zugangsdaten</h4>
    <p>
      Dies ist ihr individueller Zugang für die Mitarbeiter-Befragung.
    </p>

    <a
      href="{{ url(env('APP_URL').'/p/'.strtolower($user['pan']['pan']) ) }}"
      target="_blank"
      style="{{ $login_button_styles }}"
    >Jetzt Anmelden</a>
    <br>
    <span style="{{ $code_style . $login_url_style }}"> {{ preg_replace("(^https?://)", "", config('app.url') ) }}/p/{{ strtolower($user['pan']['pan']) }} </span>

    <p>
      <strong>PAN:</strong><br>
      <span style="{{ $code_style . $dark_style  }}"> {{ $user['pan']['pan'] }} </span>
      <br>

      <strong>PIN:</strong><br>
      <span style="{{ $code_style . $dark_style  }}">{{ $user['pan']['pin'] }}</span>
    </p>

    <p style="{{ $subinfo_styles }}">
      Falls sich der weiße Knopf "Jetzt Anmelden" nicht klicken lässt, kopieren Sie den Link darunter und öffnen Sie diesen in einem Webbrowser.<br>
      Ihre Zugangsdaten sind individuell und für Sie persönlich gedacht. Geben Sie die Inhalte unter keinen Umständen weiter.<br>

    </p>

  </div>

  @if($signature)
  <p style="padding: 5px 20px;">
    {{ $signature }}
  </p>
  @endif

@stop

@section('footer')

@endsection
