# Lesson Grading System Implementation

## Overview
A comprehensive grading system for tracking student performance on lesson exercises with support for multiple attempts, teacher-student relationships, and lesson assignments.

## Database Schema

### Tables Created

#### 1. `teacher_student`
Manages teacher-student relationships.

**Columns:**
- `id` - Primary key
- `teacher_id` - Foreign key to users
- `student_id` - Foreign key to users
- `created_at`, `updated_at` - Timestamps

**Indexes:**
- Unique constraint on (teacher_id, student_id)
- Index on teacher_id
- Index on student_id

#### 2. `lesson_assignments`
Tracks lessons assigned by teachers to students.

**Columns:**
- `id` - Primary key
- `lesson_id` - Foreign key to lessons
- `teacher_id` - Foreign key to users (teacher)
- `student_id` - Foreign key to users (student)
- `assigned_version` - JSON snapshot of exercises at assignment time
- `assigned_at` - Timestamp when assigned
- `due_date` - Optional deadline
- `status` - Assignment status (pending/in_progress/completed/overdue)
- `created_at`, `updated_at` - Timestamps

**Indexes:**
- Unique constraint on (lesson_id, student_id)
- Indexes on teacher_id, student_id, status, due_date

#### 3. `lesson_attempts`
Records each attempt by a student to complete a lesson.

**Columns:**
- `id` - Primary key
- `lesson_id` - Foreign key to lessons
- `user_id` - Foreign key to users (student)
- `assignment_id` - Optional foreign key to lesson_assignments
- `attempt_number` - Attempt sequence number
- `exercises_snapshot` - JSON snapshot of exercises for this attempt
- `user_answers` - JSON of all user answers
- `started_at` - Timestamp when started
- `completed_at` - Timestamp when completed (nullable)
- `is_completed` - Boolean completion flag
- `created_at`, `updated_at` - Timestamps

**Indexes:**
- Composite index on (user_id, lesson_id)
- Indexes on assignment_id, is_completed, completed_at

#### 4. `lesson_scores`
Stores calculated scores for completed attempts.

**Columns:**
- `id` - Primary key
- `attempt_id` - Foreign key to lesson_attempts
- `lesson_id` - Foreign key to lessons
- `user_id` - Foreign key to users (student)
- `teacher_id` - Optional foreign key for manual grading
- `total_questions` - Total number of questions
- `correct_answers` - Number of correct answers
- `score_percentage` - Percentage score (0.00-100.00)
- `auto_graded` - Boolean flag
- `teacher_comment` - Optional text comment
- `graded_at` - Timestamp when graded
- `created_at`, `updated_at` - Timestamps

**Indexes:**
- Indexes on attempt_id, (user_id, lesson_id), teacher_id, score_percentage, graded_at

## Models

### New Models Created

1. **`TeacherStudent`** - Pivot model for teacher-student relationships
2. **`LessonAssignment`** - Lesson assignment model
3. **`LessonAttempt`** - Lesson attempt model
4. **`LessonScore`** - Score model with helper methods

### Updated Models

#### `User` Model
**New Relationships:**
- `students()` - BelongsToMany - Students of this teacher
- `teachers()` - BelongsToMany - Teachers of this student
- `assignedLessons()` - HasMany - Lessons assigned by this teacher
- `myAssignments()` - HasMany - Lessons assigned to this student
- `lessonAttempts()` - HasMany - All attempts by this user
- `scores()` - HasMany - All scores earned by this user

#### `Lesson` Model
**New Relationships:**
- `assignments()` - HasMany - All assignments for this lesson
- `attempts()` - HasMany - All attempts for this lesson
- `scores()` - HasMany - All scores for this lesson

## Services

### `LessonGradingService`
Core service for grading operations.

**Methods:**

#### `startAttempt(Lesson $lesson, User $user, ?LessonAssignment $assignment)`
- Creates a new attempt record
- Auto-increments attempt number
- Snapshots exercises from assignment or current lesson

#### `completeAttempt(LessonAttempt $attempt, array $user_answers, array $scores, int $total_score, int $max_score)`
- Marks attempt as completed
- Saves user answers
- Creates score record with percentage calculation
- Uses database transaction

#### `getActiveAttempt(Lesson $lesson, User $user)`
- Returns current incomplete attempt if exists

#### `getAssignment(Lesson $lesson, User $student)`
- Returns assignment for a lesson and student

#### `assignLesson(Lesson $lesson, User $teacher, User $student, ?\DateTimeInterface $due_date)`
- Creates new assignment
- Snapshots current exercises

#### `getBestScore(Lesson $lesson, User $student)`
- Returns highest scoring attempt

#### `getLatestScore(Lesson $lesson, User $student)`
- Returns most recent score

#### `updateAssignmentStatus(LessonAssignment $assignment)`
- Updates assignment status based on completion

## Livewire Components

### Updated: `LessonExercises`
**New Features:**
- Auto-starts attempt when component mounts
- Saves attempt and score to database on completion
- Tracks attempt_id for current session
- Supports retry with new attempt creation

**Key Changes:**
- Added `lesson_id` and `attempt_id` properties
- Added `startAttempt()` method
- Added `saveAttemptAndScore()` method
- Modified `checkAnswers()` to save to database
- Modified `resetExercises()` to start new attempt

### New: `StudentScores`
Teacher-facing component to view all scores for a lesson.

**Features:**
- Paginated table of all scores
- Student information display
- Score percentage with color coding
- Letter grade calculation
- Attempt number tracking
- Authorization check (lesson owner or admin)

## Views

### New View: `lessons/scores.blade.php`
Displays student scores for a lesson (teacher view).

### Updated View: `lessons/show.blade.php`
- Added "View Scores" button (visible to lesson owner/admin)
- Passes `lesson_id` to `LessonExercises` component

### New View: `livewire/teacher/student-scores.blade.php`
Displays formatted table of student scores with:
- Student avatar and info
- Attempt number
- Score (correct/total)
- Percentage with color coding
- Letter grade badge
- Completion timestamp
- Pagination

## Routes

### New Route
```php
Route::get('/lessons/{lesson}/scores', [LessonController::class, 'scores'])
    ->name('lessons.scores')
    ->middleware(['auth', 'lesson.owner']);
```

## Translations

### English (`resources/lang/en/lessons.php`)
```php
'student_scores' => 'Student Scores',
'no_scores_yet' => 'No scores available yet.',
'student' => 'Student',
'attempt' => 'Attempt',
'score' => 'Score',
'percentage' => 'Percentage',
'grade' => 'Grade',
'completed_at' => 'Completed At',
'view_scores' => 'View Scores',
'my_attempts' => 'My Attempts',
'duration' => 'Duration',
'minutes' => 'minutes',
```

### Vietnamese (`resources/lang/vi/lessons.php`)
```php
'student_scores' => 'Điểm Học Sinh',
'no_scores_yet' => 'Chưa có điểm nào.',
'student' => 'Học sinh',
'attempt' => 'Lần thử',
'score' => 'Điểm',
'percentage' => 'Phần trăm',
'grade' => 'Xếp loại',
'completed_at' => 'Hoàn thành lúc',
'view_scores' => 'Xem Điểm',
'my_attempts' => 'Lần Thử Của Tôi',
'duration' => 'Thời gian',
'minutes' => 'phút',
```

## Enums

### New: `AssignmentStatusEnum`
```php
PENDING = 'pending'
IN_PROGRESS = 'in_progress'
COMPLETED = 'completed'
OVERDUE = 'overdue'
```

### Updated: `TableNameEnum`
Added constants:
- `TEACHER_STUDENT`
- `LESSON_ASSIGNMENTS`
- `LESSON_ATTEMPTS`
- `LESSON_SCORES`

## Usage Flow

### Student Taking a Lesson

1. Student visits lesson page with exercises
2. `LessonExercises` component automatically creates attempt via `startAttempt()`
3. Student completes exercises
4. Student clicks "Check Answers"
5. Component calculates score and calls `saveAttemptAndScore()`
6. Score is saved to database
7. Student can retry - new attempt is created

### Teacher Viewing Scores

1. Teacher opens their lesson
2. Clicks "View Scores" button (only visible to owner)
3. `StudentScores` component displays all attempts
4. Shows student name, attempt number, score, grade
5. Paginated list with color-coded performance indicators

### Lesson Assignment (Future Feature)

1. Teacher assigns lesson to student with optional due date
2. Assignment snapshots current exercise version
3. Student receives assigned lesson
4. Student's attempt links to assignment
5. Assignment status updates on completion

## Key Features

### Version Control Integration
- **Lesson History**: Tracks content changes (existing)
- **Assignment Snapshots**: Preserves exercises at assignment time
- **Attempt Snapshots**: Stores exercises for each attempt
- Students work on assigned version even if lesson is updated

### Multiple Attempts
- Each attempt is tracked separately
- Attempt numbers auto-increment
- All attempts and scores preserved
- Can view best score or latest score

### Automatic Grading
- Scores calculated on frontend
- Saved to database with percentage
- Letter grades (A-F) automatically assigned
- Color-coded performance indicators

### Data Integrity
- Uses database transactions for score saving
- Cascade deletes configured
- Proper foreign key constraints
- Snake_case naming convention throughout

### Authorization
- Lesson owner can view all scores
- Admin users have full access
- Students can only see their own attempts
- Protected routes use middleware

## Future Enhancements

1. **Teacher Dashboard** - Overview of all students and assignments
2. **Student Progress Tracking** - Historical performance graphs
3. **Manual Grading** - Teacher can review and adjust scores
4. **Detailed Answer Review** - View student's specific answers
5. **Assignment Management UI** - Bulk assign lessons to students
6. **Email Notifications** - Notify students of assignments and grades
7. **Export Reports** - Generate PDF/Excel reports of student performance
8. **Time Tracking** - Record time spent on each attempt
9. **Question-level Analytics** - Identify difficult questions
10. **Student Groups** - Assign lessons to groups of students

## Technical Notes

- All code follows strict typing (`declare(strict_types=1)`)
- Uses Enum values instead of hardcoded strings
- WHERE clauses use explicit operators (`=`, `!=`, etc.)
- Snake_case for variables and database columns
- Proper PHPDoc annotations on all methods
- Leverages Laravel's Eloquent relationships
- Supports pagination for scalability
