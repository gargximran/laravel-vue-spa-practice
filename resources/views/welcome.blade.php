<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>LaraVue</title>
        <link rel="stylesheet" href="{{asset('css/app.css', true)}}">
        <base href="{{secure_url('/')}}">

       
    </head>
    <body>
       <v-app id="app">
            <appcomp />
       </v-app>
       <script src="{{ asset('js/app.js', true) }}"></script>
    </body>
</html>
