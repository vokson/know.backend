<?php

namespace Tests\Feature\Setting;

use App\Http\Controllers\SettingController;
use App\Setting;
use Tests\TestCase;
Use Illuminate\Foundation\Testing\RefreshDatabase;


class SaveTest extends TestCase
{
    use RefreshDatabase;

    public function test()
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

        $result = SettingController::save('NAME_1', 'NEW_VALUE');
        $this->assertEquals(true, $result);
        $this->assertEquals('NEW_VALUE', SettingController::take('NAME_1'));

        $result = SettingController::save('NAME_2', 'NEW_VALUE');
        $this->assertEquals(false, $result);

    }
}
