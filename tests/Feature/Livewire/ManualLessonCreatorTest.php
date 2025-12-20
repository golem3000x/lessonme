<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Enums\System\SystemLanguageEnum;
use App\Enums\System\UserRoleEnum;
use App\Livewire\Lesson\ManualLessonCreator;
use App\Models\Topic\Topic;
use App\Models\User\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManualLessonCreatorTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Topic $topic;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->user = User::factory()->create();
        $this->user->assignRole(UserRoleEnum::STUDENT);
        $this->user->addLearningLanguage(SystemLanguageEnum::VIETNAMESE);

        $this->topic = Topic::factory()->create(['user_id' => null]);
    }

    public function test_component_renders_successfully(): void
    {
        Livewire::actingAs($this->user)
            ->test(ManualLessonCreator::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.lesson.manual-lesson-creator');
    }

    public function test_mount_loads_topics(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(ManualLessonCreator::class);

        $topics = $component->get('topics');
        $this->assertNotEmpty($topics);
    }

    public function test_mount_loads_available_languages(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(ManualLessonCreator::class);

        $languages = $component->get('available_languages');
        $this->assertNotEmpty($languages);
    }

    public function test_default_active_tab_is_basic(): void
    {
        Livewire::actingAs($this->user)
            ->test(ManualLessonCreator::class)
            ->assertSet('active_tab', 'basic');
    }

    public function test_set_active_tab(): void
    {
        Livewire::actingAs($this->user)
            ->test(ManualLessonCreator::class)
            ->call('setActiveTab', 'vocabulary')
            ->assertSet('active_tab', 'vocabulary');
    }

    // ===== VOCABULARY TESTS =====

    public function test_add_vocabulary_item(): void
    {
        Livewire::actingAs($this->user)
            ->test(ManualLessonCreator::class)
            ->call('addVocabularyItem')
            ->assertCount('vocabulary', 1);
    }

    public function test_remove_vocabulary_item(): void
    {
        Livewire::actingAs($this->user)
            ->test(ManualLessonCreator::class)
            ->call('addVocabularyItem')
            ->call('addVocabularyItem')
            ->call('removeVocabularyItem', 0)
            ->assertCount('vocabulary', 1);
    }

    // ===== GRAMMAR TESTS =====

    public function test_add_grammar_item(): void
    {
        Livewire::actingAs($this->user)
            ->test(ManualLessonCreator::class)
            ->call('addGrammarItem')
            ->assertCount('grammar_data', 1);
    }

    public function test_remove_grammar_item(): void
    {
        Livewire::actingAs($this->user)
            ->test(ManualLessonCreator::class)
            ->call('addGrammarItem')
            ->call('addGrammarItem')
            ->call('removeGrammarItem', 0)
            ->assertCount('grammar_data', 1);
    }

    // ===== DIALOGUE TESTS =====

    public function test_add_dialogue_line(): void
    {
        Livewire::actingAs($this->user)
            ->test(ManualLessonCreator::class)
            ->call('addDialogueLine')
            ->assertCount('dialogue', 1);
    }

    public function test_dialogue_alternates_speakers(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(ManualLessonCreator::class)
            ->call('addDialogueLine')
            ->call('addDialogueLine');

        $dialogue = $component->get('dialogue');
        $this->assertEquals('A', $dialogue[0]['speaker']);
        $this->assertEquals('B', $dialogue[1]['speaker']);
    }

    public function test_remove_dialogue_line(): void
    {
        Livewire::actingAs($this->user)
            ->test(ManualLessonCreator::class)
            ->call('addDialogueLine')
            ->call('addDialogueLine')
            ->call('removeDialogueLine', 0)
            ->assertCount('dialogue', 1);
    }

    // ===== EXERCISE TESTS =====

    public function test_add_gap_fill(): void
    {
        Livewire::actingAs($this->user)
            ->test(ManualLessonCreator::class)
            ->call('addGapFill')
            ->assertCount('gap_fill', 1);
    }

    public function test_gap_fill_max_limit(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(ManualLessonCreator::class);

        for ($i = 0; $i < 10; $i++) {
            $component->call('addGapFill');
        }

        $this->assertCount(7, $component->get('gap_fill'));
    }

    public function test_add_sentence_ordering(): void
    {
        Livewire::actingAs($this->user)
            ->test(ManualLessonCreator::class)
            ->call('addSentenceOrdering')
            ->assertCount('sentence_ordering', 1);
    }

    public function test_add_true_false(): void
    {
        Livewire::actingAs($this->user)
            ->test(ManualLessonCreator::class)
            ->call('addTrueFalse')
            ->assertCount('true_false', 1);
    }

    public function test_add_matching(): void
    {
        Livewire::actingAs($this->user)
            ->test(ManualLessonCreator::class)
            ->call('addMatching')
            ->assertCount('matching', 1);
    }

    public function test_add_quiz(): void
    {
        Livewire::actingAs($this->user)
            ->test(ManualLessonCreator::class)
            ->call('addQuiz')
            ->assertCount('quiz', 1);
    }

    // ===== COMMON MISTAKES TESTS =====

    public function test_add_common_mistake(): void
    {
        Livewire::actingAs($this->user)
            ->test(ManualLessonCreator::class)
            ->call('addCommonMistake')
            ->assertCount('common_mistakes', 1);
    }

    public function test_remove_common_mistake(): void
    {
        Livewire::actingAs($this->user)
            ->test(ManualLessonCreator::class)
            ->call('addCommonMistake')
            ->call('addCommonMistake')
            ->call('removeCommonMistake', 0)
            ->assertCount('common_mistakes', 1);
    }

    // ===== SAVE TESTS =====

    public function test_publish_lesson_validates_required_fields(): void
    {
        Livewire::actingAs($this->user)
            ->test(ManualLessonCreator::class)
            ->set('lesson_name', '')
            ->call('publishLesson')
            ->assertHasErrors(['lesson_name' => 'required']);
    }

    public function test_publish_lesson_creates_lesson(): void
    {
        Livewire::actingAs($this->user)
            ->test(ManualLessonCreator::class)
            ->set('lesson_name', 'Test Lesson')
            ->set('topic_id', $this->topic->id)
            ->set('language_level', 'A1')
            ->set('lesson_language', SystemLanguageEnum::VIETNAMESE->value)
            ->call('publishLesson');

        $this->assertDatabaseHas('lessons', [
            'lesson_name' => 'Test Lesson',
            'user_id' => $this->user->id,
            'is_draft' => false,
        ]);
    }

    public function test_save_draft_creates_draft_lesson(): void
    {
        Livewire::actingAs($this->user)
            ->test(ManualLessonCreator::class)
            ->set('lesson_name', 'Draft Lesson')
            ->set('topic_id', $this->topic->id)
            ->set('language_level', 'B1')
            ->set('lesson_language', SystemLanguageEnum::VIETNAMESE->value)
            ->call('saveDraft');

        $this->assertDatabaseHas('lessons', [
            'lesson_name' => 'Draft Lesson',
            'is_draft' => true,
        ]);
    }

    public function test_publish_redirects_to_lessons_index(): void
    {
        Livewire::actingAs($this->user)
            ->test(ManualLessonCreator::class)
            ->set('lesson_name', 'Test Lesson')
            ->set('topic_id', $this->topic->id)
            ->set('language_level', 'A1')
            ->set('lesson_language', SystemLanguageEnum::VIETNAMESE->value)
            ->call('publishLesson')
            ->assertRedirect(route('lessons.index'));
    }
}
