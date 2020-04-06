<?php

namespace Tests\Feature\Setting;

use App\Setting;
use Tests\TestCase;
Use Illuminate\Foundation\Testing\RefreshDatabase;


class GetTest extends TestCase
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

        $setting = new Setting();
        $setting->name = 'NAME_2';
        $setting->value = 'VALUE_2';
        $setting->save();

        $this->assertDatabaseHas('settings', [
            'id' => 2,
            'name' => 'NAME_2',
            'value' => 'VALUE_2'
        ]);

        $response = $this->json('POST', '/api/setting/get', []);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);

        $items = $arr['items'];
        $this->assertEquals(['name' => 'NAME_1', 'value' => 'VALUE_1'], $items[0]);
        $this->assertEquals(['name' => 'NAME_2', 'value' => 'VALUE_2'], $items[1]);
    }

}
