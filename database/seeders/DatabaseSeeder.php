<?php

namespace Database\Seeders;

use App\Models\Note;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin User', 'password' => Hash::make('password'), 'role' => 'admin']
        );

        // Student users
        $student1 = User::firstOrCreate(
            ['email' => 'alice@example.com'],
            ['name' => 'Alice Student', 'password' => Hash::make('password'), 'role' => 'student']
        );

        $student2 = User::firstOrCreate(
            ['email' => 'bob@example.com'],
            ['name' => 'Bob Student', 'password' => Hash::make('password'), 'role' => 'student']
        );

        // Sample notes (no actual file — placeholder paths for demo)
        $subjects = ['Mathematics', 'Physics', 'History', 'Computer Science'];
        $notes = [
            ['title' => 'Calculus Cheat Sheet',      'subject' => 'Mathematics',      'user' => $student1],
            ['title' => 'Newton\'s Laws Summary',    'subject' => 'Physics',          'user' => $student1],
            ['title' => 'World War II Timeline',     'subject' => 'History',          'user' => $student2],
            ['title' => 'OOP Concepts Overview',     'subject' => 'Computer Science', 'user' => $student2],
            ['title' => 'Trigonometry Identities',   'subject' => 'Mathematics',      'user' => $admin],
        ];

        foreach ($notes as $n) {
            Note::create([
                'user_id'     => $n['user']->id,
                'title'       => $n['title'],
                'subject'     => $n['subject'],
                'description' => 'This is a sample note for demonstration purposes.',
                'file_path'   => 'notes/sample.pdf',
                'file_name'   => 'sample.pdf',
            ]);
        }
    }
}
