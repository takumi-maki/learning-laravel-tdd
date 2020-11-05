<?php

namespace Tests\Feature\Http\Controllers\Api\Lesson;

use App\Models\Lesson;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\Factories\Traits\CreatesUser;
use Tests\TestCase;

class ReserveControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;
    use CreatesUser;

    public function testInvoke_正常系()
    {
        $lesson = factory(Lesson::class)->create();
        $user = $this->createUser();
        $this->actingAs($user, 'api');

        $response = $this->postJson("/api/lessons/{$lesson->id}/reserve");
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'lesson_id' => $lesson->id,
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('reservations',[
            'lesson_id' => $lesson->id,
            'user_id' => $user->id,
        ]);
    }

    public function testInvoke_異常系()
    {
        $lesson = factory(Lesson::class)->create(['capacity' => 1]);
        $lesson->reservations()->save(factory(Reservation::class)->make());
        $user = $this->createUser();
        $this->actingAs($user, 'api');

        $response = $this->postJson("/api/lessons/{$lesson->id}/reserve");
        $response->assertStatus(Response::HTTP_CONFLICT);
        $response->assertJsonStructure(['error']);

        $error = $response->json('error');
        $this->assertStringContainsString('予約できません。', $error);

        $this->assertDatabaseMissing('reservations',[
            'lesson_id' => $lesson->id,
            'user_id' => $user->id,
        ]);
    }
}