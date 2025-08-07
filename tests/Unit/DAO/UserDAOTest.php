<?php

namespace Tests\Unit\DAO;

use PHPUnit\Framework\TestCase;
use App\DAO\Auth\UserDAO;

class UserDAOTest extends TestCase
{
    private UserDAO $userDAO;
    private array $createdUserIds = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->userDAO = new UserDAO();
    }

    protected function tearDown(): void
    {
        foreach ($this->createdUserIds as $id) {
            $this->userDAO->delete($id);
        }
        $this->createdUserIds = [];
        parent::tearDown();
    }

    /** @test */
    public function it_should_find_user_by_school_id()
    {
        $schoolId = 'UT_SID_' . uniqid();
        $id = $this->userDAO->create([
            'school_id' => $schoolId,
            'full_name' => 'John Doe',
            'role' => 'student',
            'year_level' => '1st',
            'section' => 'A',
        ]);
        $this->createdUserIds[] = $id;

        $found = $this->userDAO->findBySchoolId($schoolId);
        $this->assertNotFalse($found);
        $this->assertEquals($schoolId, $found['school_id']);
    }

    /** @test */
    public function it_should_return_null_when_user_not_found_by_school_id()
    {
        $found = $this->userDAO->findBySchoolId('NON_EXIST_' . uniqid());
        $this->assertFalse($found);
    }

    /** @test */
    public function it_should_find_user_by_id()
    {
        $id = $this->userDAO->create([
            'school_id' => 'UT_ID_' . uniqid(),
            'full_name' => 'John Doe',
            'role' => 'student',
            'year_level' => '1st',
            'section' => 'A',
        ]);
        $this->createdUserIds[] = $id;

        $found = $this->userDAO->findById($id);
        $this->assertNotFalse($found);
        $this->assertEquals($id, (int)$found['user_id']);
    }

    /** @test */
    public function it_should_create_user_successfully()
    {
        $id = $this->userDAO->create([
            'school_id' => 'UT_CREATE_' . uniqid(),
            'full_name' => 'John Doe',
            'role' => 'student',
            'year_level' => '1st',
            'section' => 'A',
        ]);
        $this->createdUserIds[] = $id;
        $this->assertIsNumeric($id);
    }

    /** @test */
    public function it_should_update_user_successfully()
    {
        $id = $this->userDAO->create([
            'school_id' => 'UT_UPD_' . uniqid(),
            'full_name' => 'John Doe',
            'role' => 'student',
            'year_level' => '1st',
            'section' => 'A',
        ]);
        $this->createdUserIds[] = $id;

        $ok = $this->userDAO->update($id, [
            'school_id' => 'UT_UPD_' . uniqid(),
            'full_name' => 'John Updated',
            'role' => 'student',
            'year_level' => '2nd',
            'section' => 'B',
        ]);
        $this->assertTrue($ok);

        $found = $this->userDAO->findById($id);
        $this->assertEquals('John Updated', $found['full_name']);
        $this->assertEquals('2nd', $found['year_level']);
        $this->assertEquals('B', $found['section']);
    }

    /** @test */
    public function it_should_delete_user_successfully()
    {
        $id = $this->userDAO->create([
            'school_id' => 'UT_DEL_' . uniqid(),
            'full_name' => 'John Doe',
            'role' => 'student',
            'year_level' => '1st',
            'section' => 'A',
        ]);
        $ok = $this->userDAO->delete($id);
        $this->assertTrue($ok);
        $found = $this->userDAO->findById($id);
        $this->assertFalse($found);
    }

    /** @test */
    public function it_should_get_all_users()
    {
        $before = $this->userDAO->getAllUsers();
        $id1 = $this->userDAO->create(['school_id' => 'A_' . uniqid(), 'full_name' => 'A', 'role' => 'student', 'year_level' => '1st', 'section' => 'A']);
        $id2 = $this->userDAO->create(['school_id' => 'B_' . uniqid(), 'full_name' => 'B', 'role' => 'faculty']);
        $this->createdUserIds[] = $id1;
        $this->createdUserIds[] = $id2;

        $all = $this->userDAO->getAllUsers();
        $this->assertGreaterThanOrEqual(count($before) + 2, count($all));
    }

    /** @test */
    public function it_should_get_users_by_role()
    {
        $id1 = $this->userDAO->create(['school_id' => 'S_' . uniqid(), 'full_name' => 'S1', 'role' => 'student', 'year_level' => '1st', 'section' => 'A']);
        $id2 = $this->userDAO->create(['school_id' => 'S_' . uniqid(), 'full_name' => 'S2', 'role' => 'student', 'year_level' => '1st', 'section' => 'B']);
        $id3 = $this->userDAO->create(['school_id' => 'F_' . uniqid(), 'full_name' => 'F', 'role' => 'faculty']);
        $this->createdUserIds = array_merge($this->createdUserIds, [$id1, $id2, $id3]);

        $students = $this->userDAO->getUsersByRole('student');
        $this->assertGreaterThanOrEqual(2, count($students));
        foreach ($students as $s) { $this->assertSame('student', $s['role']); }
    }

    /** @test */
    public function it_should_get_students_by_year_and_section()
    {
        $id1 = $this->userDAO->create(['school_id' => 'YS1_' . uniqid(), 'full_name' => 'A', 'role' => 'student', 'year_level' => '1st', 'section' => 'A']);
        $id2 = $this->userDAO->create(['school_id' => 'YS2_' . uniqid(), 'full_name' => 'B', 'role' => 'student', 'year_level' => '1st', 'section' => 'B']);
        $this->createdUserIds = array_merge($this->createdUserIds, [$id1, $id2]);

        $res = $this->userDAO->getStudentsByYearSection('1st', 'A');
        $this->assertNotEmpty($res);
        foreach ($res as $u) {
            $this->assertSame('1st', $u['year_level']);
            $this->assertSame('A', $u['section']);
        }
    }
}