<?php

namespace Tests\Feature\Setting;

use App\Http\Controllers\SettingController;
use App\Setting;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
Use Illuminate\Foundation\Testing\RefreshDatabase;



class TakeTest extends TestCase
{
    use RefreshDatabase;

    public function test_1()
    {
        $setting = new Setting();
        $setting->name = 'NAME_1';
        $setting->value = 'VALUE_1';
        $setting->save();

        $this->assertDatabaseHas('settings', [
            'id' => 1,
            'name' => 'NAME_1',
            'value' => 'VALUE_1'
        ]);

        $setting = new Setting();
        $setting->name = 'NAME_2';
        $setting->value = 'VALUE_2';
        $setting->save();

        $this->assertDatabaseHas('settings', [
            'id' => 2,
            'name' => 'NAME_2',
            'value' => 'VALUE_2'
        ]);

        $this->assertEquals('VALUE_1', SettingController::take('NAME_1'));
        $this->assertEquals('VALUE_2', SettingController::take('NAME_2'));
        $this->assertEquals(null, SettingController::take('NAME_3'));

    }
}
