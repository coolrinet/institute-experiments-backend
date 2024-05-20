<?php

namespace Tests\Feature\Profile;

use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ShowProfileTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_user_can_get_its_own_profile(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson(route('profile.show'));

        $response->assertOk();
        $response->assertJson(fn (AssertableJson $json) => $json->has('data', fn (AssertableJson $json) => $json->where('id', $user->id)
            ->where('firstName', $user->first_name)
            ->where('lastName', $user->last_name)
            ->where('middleName', $user->middle_name)
            ->where('email', $user->email)
            ->missing('isAdmin')
            ->etc()
        )
        );
    }

    public function test_guest_cant_get_profile(): void
    {
        $response = $this->getJson(route('profile.show'));
        $response->assertUnauthorized();
    }

    public function test_admin_can_see_its_own_role(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)
            ->getJson(route('profile.show'));

        $response->assertOk();
        $response->assertJson(fn (AssertableJson $json) => $json->has('data', fn (AssertableJson $json) => $json->where('isAdmin', true)
            ->where('firstName', $admin->first_name)
            ->where('lastName', $admin->last_name)
            ->where('middleName', $admin->middle_name)
            ->where('email', $admin->email)
            ->etc()
        )
        );
    }
}
