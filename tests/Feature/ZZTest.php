<?php
namespace Tests\Feature;
use Tests\TestCase;
class ZZTest extends TestCase {
    public function test_a(): void {
        $svc = app("App\Services\ItemReturnService");
        $this->assertTrue(true);
    }
}
