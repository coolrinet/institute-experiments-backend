<?php

namespace Tests\Feature\Research;

use App\Models\Research;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowResearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->user = User::has('research')
            ->has('participatoryResearch')
            ->first();
        $this->research = $this->user->research()->first();
    }

    public function test_author_can_see_his_own_research(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson(route('research.show', $this->research));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => ['id', 'name', 'description', 'isPublic', 'participants', 'machinery', 'author'],
        ]);
    }

    public function test_user_can_see_research_where_he_is_participant(): void
    {
        $participatoryResearch = $this->user->participatoryResearch()->first();

        $response = $this->actingAs($this->user)
            ->getJson(route('research.show', $participatoryResearch));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => ['id', 'name', 'description', 'participants', 'machinery', 'author'],
        ]);
    }

    public function test_user_cannot_see_research_where_he_is_not_participant(): void
    {
        $researchList = Research::whereNot('author_id', $this->user->id)
            ->has('participants')
            ->get();

        $research = null;

        foreach ($researchList as $researchItem) {
            if (! $researchItem->participants->contains($this->user)) {
                $research = $researchItem;
                break;
            }
        }

        $response = $this->actingAs($this->user)
            ->getJson(route('research.show', $research));

        $response->assertForbidden();
    }

    public function test_guest_cannot_see_someone_else_research(): void
    {
        $response = $this->getJson(route('research.show', $this->research));

        $response->assertUnauthorized();
    }
}
