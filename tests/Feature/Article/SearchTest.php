<?php

namespace Tests\Feature\Article;

use App\User;
use App\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function testById()
    {
        $user = new User();
        $user->active = 1;
        $user->name = 'John';
        $user->surname = 'Doe';
        $user->role = 'admin';
        $user->email = 'john_doe@mail.ru';
        $user->permission_expression = '/.*/';
        $user->password = hash('sha256', '1234');
        $user->access_token = 'DOE_TOKEN';
        $user->save();

        $user = new User();
        $user->active = 1;
        $user->name = 'Alex';
        $user->surname = 'Pupkin';
        $user->role = 'engineer';
        $user->email = 'alex_pupkin@mail.ru';
        $user->permission_expression = '/.*/';
        $user->password = hash('sha256', '1234');
        $user->access_token = 'PUPKIN_TOKEN';
        $user->save();

        $article = new Article();
        $article->id = 1;
        $article->user_id = 1;
        $article->version = 1;
        $article->subject = 'DOE ARTICLE';
        $article->body = 'THIS IS BODY 1 VERSION 1';
        $article->save();

        $article = new Article();
        $article->id = 1;
        $article->user_id = 1;
        $article->version = 2;
        $article->subject = 'DOE ARTICLE';
        $article->body = 'THIS IS BODY 1 VERSION 2';
        $article->save();

        $article = new Article();
        $article->id = 2;
        $article->user_id = 2;
        $article->version = 1;
        $article->subject = 'PUPKIN ARTICLE';
        $article->body = 'THIS IS BODY 2 VERSION 1';
        $article->save();

        $article = new Article();
        $article->id = 2;
        $article->user_id = 2;
        $article->version = 2;
        $article->subject = 'PUPKIN ARTICLE';
        $article->body = 'THIS IS BODY 2 VERSION 2';
        $article->save();

        // TEST ID 1
        $response = $this->json('POST', '/api/article/search', [
            'id' => '1'
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
//        $this->assertEquals('QQQ', $arr['error']);
        $this->assertEquals(1, $arr['success']);

        $items = $arr['items'];
        $this->assertEquals(1, count($items));
        $this->assertEquals('1', $items[0]['id']);
        $this->assertEquals('2', $items[0]['version']);
        $this->assertEquals('Doe John', $items[0]['owner']);
        $this->assertEquals('DOE ARTICLE', $items[0]['subject']);

        // TEST ID 2

        $response = $this->json('POST', '/api/article/search', []);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);

        $items = $arr['items'];
        $this->assertEquals(2, count($items));

        $this->assertEquals('1', $items[0]['id']);
        $this->assertEquals('2', $items[0]['version']);
        $this->assertEquals('Doe John', $items[0]['owner']);
        $this->assertEquals('DOE ARTICLE', $items[0]['subject']);

        $this->assertEquals('2', $items[1]['id']);
        $this->assertEquals('2', $items[1]['version']);
        $this->assertEquals('Pupkin Alex', $items[1]['owner']);
        $this->assertEquals('PUPKIN ARTICLE', $items[1]['subject']);

        // TEST SUBJECT

        $response = $this->json('POST', '/api/article/search', [
            'subject' => 'PUP'
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
//        $this->assertEquals('QQQ', $arr['error']);
        $this->assertEquals(1, $arr['success']);

        $items = $arr['items'];
        $this->assertEquals(1, count($items));
        $this->assertEquals('2', $items[0]['id']);
        $this->assertEquals('2', $items[0]['version']);
        $this->assertEquals('Pupkin Alex', $items[0]['owner']);
        $this->assertEquals('PUPKIN ARTICLE', $items[0]['subject']);

        // TEST BODY 1

        $response = $this->json('POST', '/api/article/search', [
            'body' => 'VERSION 2'
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);

        $items = $arr['items'];
        $this->assertEquals(2, count($items));

        $this->assertEquals('1', $items[0]['id']);
        $this->assertEquals('2', $items[0]['version']);
        $this->assertEquals('Doe John', $items[0]['owner']);
        $this->assertEquals('DOE ARTICLE', $items[0]['subject']);

        $this->assertEquals('2', $items[1]['id']);
        $this->assertEquals('2', $items[1]['version']);
        $this->assertEquals('Pupkin Alex', $items[1]['owner']);
        $this->assertEquals('PUPKIN ARTICLE', $items[1]['subject']);

        // TEST OWNER 1

        $response = $this->json('POST', '/api/article/search', [
            'owner' => 'Alex'
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
//        $this->assertEquals('QQQ', $arr['error']);
        $this->assertEquals(1, $arr['success']);

        $items = $arr['items'];
        $this->assertEquals(1, count($items));
        $this->assertEquals('2', $items[0]['id']);
        $this->assertEquals('2', $items[0]['version']);
        $this->assertEquals('Pupkin Alex', $items[0]['owner']);
        $this->assertEquals('PUPKIN ARTICLE', $items[0]['subject']);

        // TEST OWNER 2

        $response = $this->json('POST', '/api/article/search', [
            'owner' => 'Pup'
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
//        $this->assertEquals('QQQ', $arr['error']);
        $this->assertEquals(1, $arr['success']);

        $items = $arr['items'];
        $this->assertEquals(1, count($items));
        $this->assertEquals('2', $items[0]['id']);
        $this->assertEquals('2', $items[0]['version']);
        $this->assertEquals('Pupkin Alex', $items[0]['owner']);
        $this->assertEquals('PUPKIN ARTICLE', $items[0]['subject']);

        // TEST BODY & OWNER

        $response = $this->json('POST', '/api/article/search', [
            'body' => 'VERSION 2',
            'owner' => 'Doe'
        ]);

        $this->assertEquals($response->status(), 200);
        $arr = $response->json();
        $this->assertEquals(1, $arr['success']);

        $items = $arr['items'];
        $this->assertEquals(1, count($items));

        $this->assertEquals('1', $items[0]['id']);
        $this->assertEquals('2', $items[0]['version']);
        $this->assertEquals('Doe John', $items[0]['owner']);
        $this->assertEquals('DOE ARTICLE', $items[0]['subject']);
    }

}
