<?php

namespace Tests\Feature;

use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CommentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_comment_can_be_created()
    {
        $data = ['name' => 'test', 'message' => 'test message'];

        $response = $this->post('/api/comments', $data);

        $response->assertCreated();
        $this->assertDatabaseHas('comments', $data);
    }

    public function test_comment_name_is_required()
    {
        $data = ['name' => '', 'message' => 'test message'];

        $response = $this->postJson('/api/comments', $data);

        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                'name' => ['The name field is required.']
            ]
        ]);
        $this->assertDatabaseMissing('comments', $data);
    }

    public function test_comment_message_is_required()
    {
        $data = ['name' => 'test', 'message' => ''];

        $response = $this->postJson('/api/comments', $data);

        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                'message' => ['The message field is required.']
            ]
        ]);
        $this->assertDatabaseMissing('comments', $data);
    }

    public function test_a_comment_can_be_edited()
    {

        $comment = Comment::factory()->create();

        $response = $this->patch('/api/comments/' . $comment->id, ['name' => 'Changed name', 'message' => 'Changed message']);

        $response->assertOk();
        tap($comment->fresh(), function ($comment) {
            $this->assertEquals('Changed name', $comment->name);
            $this->assertEquals('Changed message', $comment->message);
        });
    }

    public function test_a_comment_can_be_deleted()
    {
        $comment = Comment::factory()->create();

        $response = $this->delete('/api/comments/' . $comment->id);

        $response->assertNoContent();
        $this->assertDatabaseMissing('comments', $comment->toArray());
    }

    public function test_a_comment_can_has_subcomments()
    {
        $comment = Comment::factory()->create();
        $subcomment = ['name' => 'test', 'message' => 'test message', 'parent_id' => $comment->id];

        $response = $this->post('/api/comments', $subcomment);

        $response->assertCreated();
        $this->assertDatabaseHas('comments', $subcomment);
    }

    public function test_comments_are_retrieved_up_third_layer()
    {
        $comment = Comment::factory()->create();
        $subcomment = Comment::factory()->create(['parent_id' => $comment->id]);
        $subcomment2 = Comment::factory()->create(['parent_id' => $subcomment->id]);

        $response = $this->getJson('/api/comments');

        $response->assertOk();
        $response->assertExactJson(
            [
                [
                    'id' => $comment->id,
                    'name' => $comment->name,
                    'message' => $comment->message,
                    'comments' => [
                        [
                            'id' => $subcomment->id,
                            'name' => $subcomment->name,
                            'message' => $subcomment->message,
                            'comments' => [
                                [
                                    'id' => $subcomment2->id,
                                    'name' => $subcomment2->name,
                                    'message' => $subcomment2->message,
                                    'comments' => []
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );
    }
}
