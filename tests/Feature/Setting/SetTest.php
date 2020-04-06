<?php

namespace Tests\Feature\Setting;

use App\Setting;
use Tests\TestCase;
Use Illuminate\Foundation\Testing\RefreshDatabase;


class SetTest extends TestCase
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

        $response = $this->json('POST', '/api/setting/set', [
            'items' => [
                ['name' => 'NAME_1', 'value' => 'NEW_VALUE_1'],
                ['name' => 'NAME_2', 'value' => 'NEW_VALUE_2']
            ]
        ]);

        $this->assertDatabaseHas('settings', [
            'id' => 1,
            'name' => 'NAME_1',
            'value' => 'NEW_VALUE_1'
        ]);

        $this->assertDatabaseHas('settings', [
            'id' => 2,
            'name' => 'NAME_2',
            'value' => 'NEW_VALUE_2'
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);
    }

    public function testWithMissedName()
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

        $response = $this->json('POST', '/api/setting/set', [
            'items' => [
                ['name' => 'NAME_2', 'value' => 'NEW_VALUE']
            ]
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('3.4', $arr['error']);
    }

    public function testWithWrongName()
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

        $response = $this->json('POST', '/api/setting/set', [
            'items' => [
                ['name' => '', 'value' => 'NEW_VALUE']
            ]
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('3.2', $arr['error']);
    }

    public function testWithWrongValue()
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

        $response = $this->json('POST', '/api/setting/set', [
            'items' => [
                ['name' => 'NAME_1', 'value' => '']
            ]
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('3.3', $arr['error']);
    }

    public function testWithWrongItems()
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

        $response = $this->json('POST', '/api/setting/set', []);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(0, $arr['success']);
        $this->assertEquals('3.1', $arr['error']);
    }
}
