<?php

namespace Tests\Feature\Research;

use App\Models\Machinery;
use App\Models\Research;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResearchListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
        $this->user = User::first();
    }

    public function test_authenticated_user_can_see_list_of_research(): void
    {
        $researchCount = Research::where('is_public', true)
            ->orWhere('author_id', $this->user->id)
            ->count();

        $response = $this->actingAs($this->user)
            ->getJson(route('research.index'));

        $response->assertOk();
        $response->assertJsonCount(min($researchCount, 15), 'data');
        $response->assertJsonPath('meta.total', $researchCount);
    }

    public function test_authenticated_user_can_see_other_user_list_of_public_research(): void
    {
        $otherUser = User::whereNot('id', $this->user->id)->first();
        $researchCount = Research::where('is_public', true)
            ->where('author_id', $otherUser->id)
            ->count();

        $response = $this->actingAs($this->user)
            ->getJson(route('research.index', ['author_id' => $otherUser->id]));

        $response->assertOk();
        $response->assertJsonCount(min($researchCount, 15), 'data');
        $response->assertJsonPath('meta.total', $researchCount);
    }

    public function test_authenticated_user_can_see_list_of_its_own_research(): void
    {
        $researchCount = Research::where('author_id', $this->user->id)
            ->count();

        $response = $this->actingAs($this->user)
            ->getJson(route('research.index', ['author_id' => $this->user->id]));

        $response->assertOk();
        $response->assertJsonCount(min($researchCount, 15), 'data');
        $response->assertJsonPath('meta.total', $researchCount);
    }

    public function test_authenticated_user_can_filter_list_of_public_research_by_machinery(): void
    {
        $machinery = Machinery::has('research')->first();

        $researchCount = Research::where('machinery_id', $machinery->id)
            ->where('is_public', true)
            ->orWhere('author_id', $this->user->id)
            ->count();

        $response = $this->actingAs($this->user)
            ->getJson(route('research.index', ['machinery_id' => $machinery->id]));

        $response->assertOk();
        $response->assertJsonCount(min($researchCount, 15), 'data');
        $response->assertJsonPath('meta.total', $researchCount);
    }

    public function test_authenticated_user_can_filter_list_of_research_by_name(): void
    {
        $researchName = Research::first()->name;

        $researchCount = Research::where('name', 'like', '%'.$researchName.'%')
            ->where('is_public', true)
            ->orWhere('author_id', $this->user->id)
            ->count();

        $response = $this->actingAs($this->user)
            ->getJson(route('research.index', ['name' => $researchName]));

        $response->assertOk();
        $response->assertJsonCount(min($researchCount, 15), 'data');
        $response->assertJsonPath('meta.total', $researchCount);
    }

    public function test_unauthenticated_user_cannot_see_list_of_research(): void
    {
        $response = $this->getJson(route('research.index'));

        $response->assertUnauthorized();
    }
}
