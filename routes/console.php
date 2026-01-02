<?php

use Illuminate\Support\Facades\Schedule;


Schedule::command('telescope:clear')->daily();
Schedule::command('update:beltone-list')->everyFourHours();
Schedule::command('update:azimut-list')->everyFourHours();
Schedule::command('update:afim-list')->everyFourHours();
//Schedule::command('update:banquemisr-list')->everyFourHours();
Schedule::command('update:gold-history')->everyFourHours();
Schedule::command('goldpricez:cache-prices')->everyTwoHours();
Schedule::command('update:currencies')->hourly();
//Schedule::command('update:zeed-funds')->everyFourHours();
Schedule::command('currency-ex:update')->everyTwoHours();
Schedule::command('exchangerates:cache-currencies')->everyTwoHours();







