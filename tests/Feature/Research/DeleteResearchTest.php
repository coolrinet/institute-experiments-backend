<?php

namespace Tests\Feature\Research;

use App\Models\Research;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteResearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->user = User::has('research')->get()
            ->filter(function (User $user) {
                return $user->research()->has('participants')->exists();
            })->first();
        $this->research = Research::has('participants')
            ->whereAuthorId($this->user->id)
            ->first();
    }

    public function test_author_can_delete_research(): void
    {
        $response = $this->actingAs($this->user)
            ->deleteJson(route('research.destroy', $this->research));

        $response->assertNoContent();
        $this->assertModelMissing($this->research);
        $this->assertDatabaseMissing('research_user', [
            'research_id' => $this->research->id,
        ]);
        $this->assertDatabaseMissing('research_machinery_parameter', [
            'research_id' => $this->research->id,
        ]);
    }

    public function test_participant_cannot_delete_research(): void
    {
        $participant = $this->research->participants()->first();

        $response = $this->actingAs($participant)
            ->deleteJson(
                route('research.destroy', $this->research)
            );

        $response->assertForbidden();
        $this->assertModelExists($this->research);
    }

    public function test_unauthenticated_user_cannot_delete_research(): void
    {
        $response = $this->deleteJson(route('research.destroy', $this->research));

        $response->assertUnauthorized();
        $this->assertModelExists($this->research);
    }

    public function test_user_cannot_delete_research_that_does_not_exist(): void
    {
        $id = Research::max('id') + 1;

        $response = $this->actingAs($this->user)
            ->deleteJson(route('research.destroy', $id));

        $response->assertNotFound();
    }
}
