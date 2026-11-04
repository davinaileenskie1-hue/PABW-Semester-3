<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| Test masing-masing modul memakai database kosong (RefreshDatabase) agar
| tidak bergantung pada isi db_sipustaka yang sebenarnya.
|
*/

uses(TestCase::class, RefreshDatabase::class)->in('Feature');
