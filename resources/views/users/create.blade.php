@extends('layouts.master')

@section('title', 'Create your account')

@section('content')
    <div class="large-12 columns">
        <h1>Smart Mirror</h1>
        <div class="panel callout">
            <h5>Hello there!</h5>
            <p>It looks like it's the first time you've used the <em>SmartMirror</em>.</p>
            <p>You're almost there, the mirror just needs your name and preferences so it can show info that's relevant to you.</p>
        </div>
    </div>
    <div class="large-12 columns">
        <form action="" method="POST" data-abide>
            <div class="row">
                <div class="large-6 columns">
                    <label>What is your name?
                        <input type="text" placeholder="Please write your name here" required pattern="[a-zA-Z]+" name="name" />
                        <small class="error">The mirror would really like to know your name.</small>
                    </label>
                </div>
                <div class="large-6 columns">
                    <label>What would you like to see?</label>
                    <input id="checkbox1" type="checkbox" checked name="preferences_quote"><label for="checkbox1">Quote of the day</label>
                    <input id="checkbox2" type="checkbox" checked name="preferences_news"><label for="checkbox2">News</label>
                </div>
            </div>
            <div class="row">
                <div class="large-12 columns">
                    <a href="@if ($googleAccessToken)
                            /users/disconnect_google
                        @else
                            /users/login_google
                        @endif" class="myButton">
                        @if ($googleAccessToken)
                            Disconnect from Google
                        @else
                            Log in with Google
                        @endif
                    </a>
                </div>
            </div>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="device_id" value="{{ $device_id }}">
            <input type="hidden" name="weight" value="{{ $weight }}">
            <input type="hidden" name="google_access_token" value="{{ $googleAccessToken }}">
            <br>
            <div class="row">
                <div class="large-12 columns">
                    <input type="submit" value="Continue" class="button small">
                </div>
            </div>

        </form>
    </div>
@endsection