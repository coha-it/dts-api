@php
$config = [
    'appName' => config('app.name'),
    'locale' => $locale = app()->getLocale(),
    'locales' => config('app.locales'),
    'githubAuth' => config('services.github.client_id'),
];
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="theme-color" content="#ffffff">

  <link href="https://dreamteam-survey.s3.eu-central-1.amazonaws.com/images/logo/favicon/favicon.ico" rel="shortcut icon" type="image/x-icon">
  <link href="https://dreamteam-survey.s3.eu-central-1.amazonaws.com/images/logo/favicon/favicon.svg" rel="icon" type="image/svg+xml" sizes="any">
  <link href="https://dreamteam-survey.s3.eu-central-1.amazonaws.com/images/logo/favicon/favicon.png" rel="icon" type="image/png" sizes="any">

  <title>{{ config('app.name') }}</title>

  <link rel="stylesheet" href='https://fonts.googleapis.com/css?family=Nunito:400,400i,600,700|Merriweather:ital,wght@0,400;1,400;1,700|Roboto:100,300,400,500,700,900|Material+Icons|Raleway:400,700|Roboto+Mono&display=swap'>
  <link href="https://cdn.jsdelivr.net/npm/@mdi/font@3.x/css/materialdesignicons.min.css" rel="stylesheet">

  <link rel="stylesheet" href="{{ mix( '/dist/css/app.css') }}" />
</head>
<body>
  <div id="app"></div>

  {{-- Global configuration object --}}
  <script>
    window.config = @json($config);
  </script>

  {{-- Load the application scripts --}}
  <script src="{{ mix( '/dist/js/app.js' ) }}"></script>
</body>
</html>
