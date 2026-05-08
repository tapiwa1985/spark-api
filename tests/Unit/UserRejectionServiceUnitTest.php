<?php

namespace Tests\Unit;

use Tests\TestCase;
use Mockery as m;
use App\Models\UserRejection;
use App\Models\User;
use App\Repositories\Contracts\UserRejectionRepositoryInterface;
use App\Services\UserRejectionService;

class UserRejectionServiceUnitTest extends TestCase
{
    public function testCreateUserRejection()
    {
        $userId = 1;
        $rejectedUserId = 2;

        $userRejectionMock = m::mock(UserRejection::class)->makePartial();
        $userRejectionMock->user_id = 1;
        $userRejectionMock->rejected_user_id = 2;
        $userRejectionMock->expires_at = null;

        $data = [
            'user_id' => $userId,
            'rejected_user_id' => $rejectedUserId,
            'expires_at' => null,
        ];

        $userRejectionRepoMock = m::mock(UserRejectionRepositoryInterface::class);
        $userRejectionRepoMock->shouldReceive('create')
            ->once()
            ->with([
                'user_id' => 1,
                'rejected_user_id' => 2,
                'expires_at' => null
            ])
            ->andReturn($userRejectionMock);

        $service = new UserRejectionService($userRejectionRepoMock);

        $result = $service->create($data);

        $this->assertNotNull($result);
        $this->assertInstanceOf(UserRejection::class, $result);
        $this->assertEquals($result->user_id, $userId);
        $this->assertEquals($result->rejected_user_id, $rejectedUserId);
        $this->assertNull($result->expires_at);
    }
}
