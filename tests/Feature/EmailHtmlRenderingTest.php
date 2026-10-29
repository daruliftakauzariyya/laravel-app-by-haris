<?php

namespace Tests\Feature;

use App\Mail\NewQuestionMail;
use App\Mail\QuestionAssignedMail;
use App\Models\Madhab;
use App\Models\QuestionAndAnswer;
use App\Models\User;
use App\Services\Mail\EmailContentSanitizer;
use Tests\TestCase;

class EmailHtmlRenderingTest extends TestCase
{
    public function test_sanitizer_preserves_html_tags_without_literal_entity_escaping(): void
    {
        $input = '<p>This is a <strong>valid</strong> paragraph.</p><p><br></p><p>Second paragraph with <em>emphasis</em>.</p>';
        $output = EmailContentSanitizer::sanitize($input);

        // Assert it produces real HTML tags with dir and style
        $this->assertStringContainsString('dir="auto"', $output);
        $this->assertStringContainsString('<strong>valid</strong>', $output);
        $this->assertStringContainsString('<em>emphasis</em>', $output);
        $this->assertStringContainsString('<p ', $output);

        // Crucial: Must NEVER contain literal escaped tag entities like &lt;p&gt;
        $this->assertStringNotContainsString('&lt;p&gt;', $output);
        $this->assertStringNotContainsString('&lt;/p&gt;', $output);
        $this->assertStringNotContainsString('&lt;br&gt;', $output);
        $this->assertStringNotContainsString('&lt;strong&gt;', $output);
    }

    public function test_sanitizer_preserves_malayalam_and_arabic_multilingual_content(): void
    {
        $arabic = 'السلام عليكم ورحمة الله وبركاته';
        $malayalam = 'Instagram, Facebook തുടങ്ങിയ സോഷ്യൽ മീഡിയ പ്ലാറ്റ്‌ഫോമുകളിൽ പണം നൽകി ബോട്ട് ഫോളോവേഴ്‌സ് വാങ്ങുന്നത് ഹലാലാണോ?';

        $q0692Html = "<p>{$arabic}</p><p><br></p><p>{$malayalam}</p>";
        $output = EmailContentSanitizer::sanitize($q0692Html);

        // Assert Arabic text is present without entity garbling
        $this->assertStringContainsString($arabic, $output);

        // Assert Malayalam text is present without corruption
        $this->assertStringContainsString($malayalam, $output);

        // Assert dir="auto" is applied for correct bidirectional layout
        $this->assertStringContainsString('dir="auto"', $output);

        // Assert no literal escaped tags
        $this->assertStringNotContainsString('&lt;p', $output);
    }

    public function test_sanitizer_strips_dangerous_scripts_and_xss_vectors(): void
    {
        $malicious = '<p>Normal text <script>alert("xss")</script><img src="x" onerror="alert(1)"><iframe src="evil.com"></iframe><a href="javascript:alert(2)">click me</a><div onclick="bad()">click div</div></p>';
        $output = EmailContentSanitizer::sanitize($malicious);

        $this->assertStringNotContainsString('<script', $output);
        $this->assertStringNotContainsString('alert(', $output);
        $this->assertStringNotContainsString('<iframe', $output);
        $this->assertStringNotContainsString('onerror', $output);
        $this->assertStringNotContainsString('onclick', $output);
        $this->assertStringNotContainsString('javascript:', $output);
        $this->assertStringContainsString('Normal text', $output);
    }

    public function test_sanitizer_converts_plain_text_safely_into_paragraphs(): void
    {
        $plain = "Line 1 with text\nLine 2 with <raw_bracket> & characters";
        $output = EmailContentSanitizer::sanitize($plain);

        $this->assertStringContainsString('dir="auto"', $output);
        $this->assertStringContainsString('Line 1 with text', $output);
        $this->assertStringContainsString('&lt;raw_bracket&gt;', $output);
        $this->assertStringContainsString('&amp;', $output);
    }

    public function test_sanitizer_handles_legacy_encoded_html_without_double_escaping(): void
    {
        $legacy = '&lt;p&gt;Assalamu Alaikum &lt;strong&gt;Brother&lt;/strong&gt;&lt;/p&gt;';
        $output = EmailContentSanitizer::sanitize($legacy);

        $this->assertStringContainsString('dir="auto"', $output);
        $this->assertStringContainsString('<strong>Brother</strong>', $output);
        $this->assertStringNotContainsString('&lt;p&gt;', $output);
        $this->assertStringNotContainsString('&lt;strong&gt;', $output);
    }

    public function test_sanitizer_compresses_excessive_empty_paragraphs(): void
    {
        $input = '<p>First</p><p><br></p><p><br></p><p><br></p><p>Second</p>';
        $output = EmailContentSanitizer::sanitize($input);

        // Count occurrences of <br>
        $emptyPCount = substr_count($output, '<br>');
        $this->assertLessThanOrEqual(2, $emptyPCount);
        $this->assertStringContainsString('First', $output);
        $this->assertStringContainsString('Second', $output);
    }

    public function test_to_plain_text_converts_html_cleanly(): void
    {
        $html = '<p>السلام عليكم</p><p><br></p><p>ഇൻസ്റ്റാഗ്രാം ഫോളോവേഴ്‌സ് <strong>വാങ്ങൽ</strong></p>';
        $text = EmailContentSanitizer::toPlainText($html);

        $this->assertStringNotContainsString('<p>', $text);
        $this->assertStringNotContainsString('</p>', $text);
        $this->assertStringNotContainsString('<strong>', $text);
        $this->assertStringContainsString('السلام عليكم', $text);
        $this->assertStringContainsString('ഇൻസ്റ്റാഗ്രാം ഫോളോവേഴ്‌സ് വാങ്ങൽ', $text);
    }

    public function test_new_question_mailable_renders_html_and_text_alternatives(): void
    {
        $user = new User(['name' => 'Test User', 'email' => 'test@example.com']);
        $madhab = new Madhab(['name' => 'Shafii']);

        $question = new QuestionAndAnswer([
            'question_id' => 'Q0692',
            'title' => 'Islamic Ruling on Social Media Followers',
            'body' => '<p>السلام عليكم ورحمة الله وبركاته</p><p><br></p><p>ഇൻസ്റ്റാഗ്രാം ഫോളോവേഴ്‌സ് വാങ്ങുന്നത് അനുവദനീയമാണോ?</p>',
        ]);
        $question->id = 1221;
        $question->setRelation('user', $user);
        $question->setRelation('madhab', $madhab);

        $mailable = new NewQuestionMail($question);

        // Render HTML view
        $htmlRendered = $mailable->render();

        $this->assertStringContainsString('Q0692', $htmlRendered);
        $this->assertStringContainsString('Shafi', $htmlRendered);
        $this->assertStringContainsString('question-box', $htmlRendered);
        $this->assertStringContainsString('admin/questions/1221', $htmlRendered);
        $this->assertStringContainsString('السلام عليكم ورحمة الله وبركاته', $htmlRendered);
        $this->assertStringContainsString('ഇൻസ്റ്റാഗ്രാം ഫോളോവേഴ്‌സ് വാങ്ങുന്നത് അനുവദനീയമാണോ?', $htmlRendered);

        // Crucial: The rendered email HTML must NOT contain escaped literal tags &lt;p&gt; or &lt;br&gt;
        $this->assertStringNotContainsString('&lt;p&gt;', $htmlRendered);
        $this->assertStringNotContainsString('&lt;br&gt;', $htmlRendered);
        $this->assertStringNotContainsString('&lt;/p&gt;', $htmlRendered);

        // Assert Mailable defines plain text multipart view
        $content = $mailable->content();
        $this->assertEquals('emails.new-question-text', $content->text);
    }

    public function test_question_assigned_mailable_renders_html_and_text_alternatives(): void
    {
        $mufti = new User(['name' => 'Mufti Ismail', 'email' => 'mufti@example.com']);
        $madhab = new Madhab(['name' => 'Hanafi']);

        $question = new QuestionAndAnswer([
            'question_id' => 'Q0700',
            'title' => 'Assigned Fatwa Query',
            'body' => '<p>ഹലാൽ നിക്ഷേപങ്ങളെക്കുറിച്ചുള്ള ചോദ്യം</p>',
        ]);
        $question->id = 1222;
        $question->setRelation('assignedMufti', $mufti);
        $question->setRelation('madhab', $madhab);

        $mailable = new QuestionAssignedMail($question);
        $htmlRendered = $mailable->render();

        $this->assertStringContainsString('Q0700', $htmlRendered);
        $this->assertStringContainsString('Mufti Ismail', $htmlRendered);
        $this->assertStringContainsString('mufti/questions/1222', $htmlRendered);
        $this->assertStringContainsString('ഹലാൽ നിക്ഷേപങ്ങളെക്കുറിച്ചുള്ള ചോദ്യം', $htmlRendered);
        $this->assertStringNotContainsString('&lt;p&gt;', $htmlRendered);

        $content = $mailable->content();
        $this->assertEquals('emails.question-assigned-text', $content->text);
    }
}
